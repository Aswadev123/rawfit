<?php 
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Determine action
$action = $_GET['action'] ?? 'login';
$error = '';
$success = '';

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Security token mismatch.";
    } else {
        if ($action === 'register') {
            // Registration
            $name = trim($_POST['name']);
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $phone = trim($_POST['phone']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirmpassword'];

            // Validations
            if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
                $error = "All fields are required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format.";
            } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
                $error = "Phone number must be exactly 10 digits with no other characters.";
            } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $password)) {
                $error = "Password must be at least 8 characters and contain at least one uppercase letter, one number, and one special character.";
            } elseif ($password !== $confirm_password) {
                $error = "Passwords do not match.";
            } else {
                $stmt = $conn->prepare("SELECT email FROM register WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error = "An account with this email already exists.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt_insert = $conn->prepare("INSERT INTO register (name, email, phone, password) VALUES (?, ?, ?, ?)");
                    $stmt_insert->bind_param("ssss", $name, $email, $phone, $hashed_password);

                    if ($stmt_insert->execute()) {
                        $success = "Registration successful! <a href='auth.php?action=login'>Log in</a>.";
                    } else {
                        $error = "Something went wrong. Please try again.";
                    }
                    $stmt_insert->close();
                }
                $stmt->close();
            }
        } elseif ($action === 'login') {
            // Login
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                $error = "All fields are required.";
            } else {
                $stmt = $conn->prepare("SELECT id, name, password FROM register WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($id, $name, $hashed_password);
                    $stmt->fetch();

                    if (password_verify($password, $hashed_password)) {
                        // Set session
                        session_regenerate_id(true);
                        $_SESSION['loggedin'] = true;
                        $_SESSION['user_id'] = $id;
                        $_SESSION['email'] = $email;
                        $_SESSION['name'] = $name;

                        // Refresh CSRF
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                        // Redirect all users to home
                        header("Location: home.php");
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
        body { background-color: #000000; }
        #auth { display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 40px; }
        .auth-container { background: #1A1F2E; border-radius: 20px; padding: 40px; width: 100%; max-width: 500px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); }
        .auth-title { font-family: 'M PLUS Rounded 1c', sans-serif; font-size: 32px; text-align: center; margin-bottom: 10px; padding: 10px; font-weight: 700; color: #F97316; }
        .auth-subtitle { font-size: 14px; text-align: center; margin-bottom: 30px; color: #666666; }
        label { display: block; margin-bottom: 6px; font-size: 14px; color: #FFFFFF; }
        input { width: 100%; padding: 10px; margin-bottom: 20px; border: none; border-radius: 8px; background-color: #2D2D2D; color: #FFFFFF; }
        .btn-primary { background: #F97316; color: #FFFFFF; font-weight: bold; padding: 12px; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; width: 100%; }
        .btn-primary:hover { background: #FBA63C; }
        .footer-text { text-align: center; margin-top: 20px; font-size: 14px; color: #999999; }
        .footer-text a { color: #F97316; text-decoration: none; font-weight: bold;}
        .auth-error { color: #FF6B6B; text-align: center; background: rgba(255, 0, 0, 0.1); padding: 10px; border-radius: 8px; margin-bottom: 20px; }
        .auth-success { color: #00FF88; text-align: center; background: rgba(0, 255, 0, 0.1); padding: 10px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <section id="auth">
        <div class="auth-container">
            <div class="auth-title"><?php echo strtoupper($action); ?></div>
            <div class="auth-subtitle">Please enter your details</div>

            <?php if ($error) echo "<p class='auth-error'>$error</p>"; ?>
            <?php if ($success) echo "<p class='auth-success'>$success</p>"; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <?php if ($action === 'register'): ?>
                    <label for="name">Name</label>
                    <input type="text" name="name" placeholder="John Doe" required>
                    <label for="email">Email</label>
                    <input type="email" name="email" placeholder="example@gmail.com" required>
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" placeholder="1234567890" pattern="[0-9]{10}" maxlength="10" required>
                    <label for="password">Password</label>
                    <input type="password" name="password" placeholder="********" required>
                    <label for="confirmpassword">Confirm Password</label>
                    <input type="password" name="confirmpassword" placeholder="********" required>
                <?php else: ?>
                    <label for="email">Email</label>
                    <input type="email" name="email" placeholder="example@gmail.com" required>
                    <label for="password">Password</label>
                    <input type="password" name="password" placeholder="********" required>
                <?php endif; ?>

                <button class="btn-primary" type="submit"><?php echo ucfirst($action); ?></button>
            </form>

            <div class="footer-text">
                <?php if ($action === 'login'): ?>
                    Don't have an account? <a href="auth.php?action=register">Sign up</a>
                <?php else: ?>
                    Already have an account? <a href="auth.php?action=login">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
</body>
</html>
