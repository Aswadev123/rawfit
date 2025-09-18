<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session timeout (30 minutes)
if (isset($_SESSION['loggedin']) && isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: trainerlogin.php?action=login");
    exit;
}
$_SESSION['last_activity'] = time();

// Database connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Action: login or register
$action = isset($_GET['action']) && in_array($_GET['action'], ['login', 'trainerlog']) ? $_GET['action'] : 'login';
$error = $success = '';
$form_data = $_POST;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid CSRF token. Please try again.";
    } else {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        if ($action === 'trainerlog') {
            $trainer_name = trim($_POST['trainer_name'] ?? '');
            $trainer_email = filter_var(trim($_POST['trainer_email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $trainer_phone = trim($_POST['trainer_phone'] ?? '');
            $trainer_password = $_POST['trainer_password'] ?? '';
            $confirm_password = $_POST['confirmpassword'] ?? '';

            if (empty($trainer_name) || empty($trainer_email) || empty($trainer_phone) || empty($trainer_password) || empty($confirm_password)) {
                $error = "All fields are required.";
            } elseif (!filter_var($trainer_email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format.";
            } elseif (!preg_match('/^\d{10}$/', $trainer_phone)) {
                $error = "Phone number must be 10 digits.";
            } elseif ($trainer_password !== $confirm_password) {
                $error = "Passwords do not match.";
            } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $trainer_password)) {
                $error = "Password must be at least 8 characters long and include letters and numbers.";
            } else {
                $stmt = $conn->prepare("SELECT trainer_email FROM trainerlog WHERE trainer_email = ?");
                $stmt->bind_param("s", $trainer_email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error = "Email already exists.";
                } else {
                    $hashed_password = password_hash($trainer_password, PASSWORD_DEFAULT);
                    $stmt_insert = $conn->prepare("INSERT INTO trainerlog (trainer_name, trainer_email, trainer_phone, trainer_password) VALUES (?, ?, ?, ?)");
                    $stmt_insert->bind_param("ssss", $trainer_name, $trainer_email, $trainer_phone, $hashed_password);
                    if ($stmt_insert->execute()) {
                        $success = "Registration successful! Redirecting to login...";
                        header("refresh:3;url=trainerlogin.php?action=login");
                    } else {
                        $error = "Registration failed. Please try again.";
                    }
                    $stmt_insert->close();
                }
                $stmt->close();
            }
        } elseif ($action === 'login') {
            $trainer_email = filter_var(trim($_POST['trainer_email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $trainer_password = $_POST['trainer_password'] ?? '';

            if (empty($trainer_email) || empty($trainer_password)) {
                $error = "All fields are required.";
            } elseif (!filter_var($trainer_email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format.";
            } else {
                $stmt = $conn->prepare("SELECT trainer_id, trainer_name, trainer_password FROM trainerlog WHERE trainer_email = ?");
                $stmt->bind_param("s", $trainer_email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($trainer_id, $trainer_name, $hashed_password);
                    $stmt->fetch();
                    if (password_verify($trainer_password, $hashed_password)) {
                        session_regenerate_id(true);
                        $_SESSION['loggedin'] = true;
                        $_SESSION['trainer_id'] = $trainer_id;
                        $_SESSION['trainer_email'] = $trainer_email;
                        $_SESSION['trainer_name'] = $trainer_name;
                        header("Location: trainerman.php");
                        exit;
                    } else {
                        $error = "Invalid email or password.";
                    }
                } else {
                    $error = "Invalid email or password.";
                }
                $stmt->close();
            }
        }
    }
}
$conn->close();
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RawFit - <?php echo ucfirst($action); ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=M+PLUS+Rounded+1c:wght@700&family=Orbitron:wght@700&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
body { background-color: #000; }
#auth { display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 40px; }
.auth-container { background: #1A1F2E; border-radius: 20px; padding: 40px; max-width: 500px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
.auth-title { font-family: 'Orbitron', sans-serif; font-size: 32px; text-align: center; margin-bottom: 10px; color: #F97316; }
label { display: block; margin-bottom: 6px; font-size: 14px; color: #FFFFFF; }
input { width: 100%; padding: 10px; margin-bottom: 20px; border: none; border-radius: 8px; background: #2D2D2D; color: #FFFFFF; }
.password-container {
    position: relative;
    display: flex;
    align-items: center; /* vertical centering */
}
.password-container input {
    flex: 1;
    padding-right: 40px;
}
.toggle-password {
    position: absolute;
    right: 12px;
    cursor: pointer;
    font-size: 18px;
    color: #FFFFFF;
    user-select: none;
}
.btn-primary { background: #F97316; color: #FFFFFF; font-weight: bold; padding: 12px; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; width: 100%; }
.btn-primary:hover { background: #FBA63C; }
.footer-text { text-align: center; margin-top: 20px; font-size: 14px; color: #999999; }
.footer-text a { color: #F97316; text-decoration: none; font-weight: bold; }
.auth-error { color: #FF6B6B; text-align: center; background: rgba(255,0,0,0.1); padding: 10px; border-radius: 8px; margin-bottom: 20px; }
.auth-success { color: #00FF88; text-align: center; background: rgba(0,255,0,0.1); padding: 10px; border-radius: 8px; margin-bottom: 20px; }
</style>
</head>
<body>
<section id="auth">
<div class="auth-container">
    <div class="auth-title"><?php echo strtoupper($action); ?></div>
    <?php if ($error): ?><p class="auth-error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if ($success): ?><p class="auth-success"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

        <?php if ($action === 'trainerlog'): ?>
            <label for="trainer_name">Name</label>
            <input type="text" name="trainer_name" placeholder="John Doe" value="<?php echo htmlspecialchars($form_data['trainer_name'] ?? ''); ?>" required autocomplete="off">

            <label for="trainer_email">Email</label>
            <input type="email" name="trainer_email" placeholder="example@gmail.com" value="<?php echo htmlspecialchars($form_data['trainer_email'] ?? ''); ?>" required autocomplete="off">

            <label for="trainer_phone">Phone</label>
            <input type="tel" name="trainer_phone" placeholder="1234567890" pattern="\d{10}" value="<?php echo htmlspecialchars($form_data['trainer_phone'] ?? ''); ?>" required autocomplete="off">

            <label for="trainer_password">Password</label>
            <div class="password-container">
                <input type="password" name="trainer_password" placeholder="********" id="trainer_password" pattern="^(?=.*[A-Za-z])(?=.*\d).{8,}$" required autocomplete="off">
                <span class="toggle-password" data-target="trainer_password">👁</span>
            </div>

            <label for="confirmpassword">Confirm Password</label>
            <input type="password" name="confirmpassword" placeholder="********" pattern="^(?=.*[A-Za-z])(?=.*\d).{8,}$" required autocomplete="off">

        <?php else: ?>
            <label for="trainer_email">Email</label>
            <input type="email" name="trainer_email" placeholder="example@gmail.com" value="<?php echo htmlspecialchars($form_data['trainer_email'] ?? ''); ?>" required autocomplete="off">

            <label for="trainer_password">Password</label>
            <div class="password-container">
                <input type="password" name="trainer_password" placeholder="********" id="trainer_password" pattern="^(?=.*[A-Za-z])(?=.*\d).{8,}$" required autocomplete="off">
                <span class="toggle-password" data-target="trainer_password">👁</span>
            </div>
        <?php endif; ?>

        <button class="btn-primary" type="submit"><?php echo ucfirst($action); ?></button>
    </form>

    <div class="footer-text">
        <?php if ($action === 'login'): ?>
            Don't have an account? <a href="trainerlogin.php?action=trainerlog">Sign up</a>
        <?php else: ?>
            Already have an account? <a href="trainerlogin.php?action=login">Login</a>
        <?php endif; ?>
    </div>
</div>
</section>

<script>
document.querySelectorAll('.toggle-password').forEach(function(el) {
    el.addEventListener('click', function() {
        const input = document.getElementById(el.getAttribute('data-target'));
        input.type = input.type === 'password' ? 'text' : 'password';
    });
});
</script>
</body>
</html>
