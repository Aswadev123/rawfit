<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid CSRF token.";
    } else {
        $form_type = $_POST['form_type'] ?? "";

        // LOGIN
        if ($form_type === "login") {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = "All fields are required.";
            } else {
                $stmt = $conn->prepare("SELECT admin_id, username, password FROM admins WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($admin_id, $username_db, $hashed_password);
                    $stmt->fetch();

                    if (password_verify($password, $hashed_password)) {
                        session_regenerate_id(true);
                        $_SESSION['loggedin'] = true;
                        $_SESSION['role'] = 'admin';
                        $_SESSION['username'] = $username_db;

                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        header("Location: admin.php");
                        exit;
                    } else {
                        $error = "Invalid username or password.";
                    }
                } else {
                    $error = "Invalid username or password.";
                }
                $stmt->close();
            }
        }

        // REGISTER
        elseif ($form_type === "register") {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $secret_code = trim($_POST['secret_code'] ?? '');

            if (empty($username) || empty($password) || empty($secret_code)) {
                $error = "All fields are required.";
            } elseif ($secret_code !== "RAWFITADMIN") {
                $error = "Invalid secret code.";
            } else {
                // Check duplicate
                $stmt = $conn->prepare("SELECT admin_id FROM admins WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $error = "Username already exists.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt->close();

                    $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
                    $stmt->bind_param("ss", $username, $hashed_password);
                    if ($stmt->execute()) {
                        $success = "Registration successful. Please login.";
                    } else {
                        $error = "Error: Could not register.";
                    }
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - RawFit</title>
    <style>
        body { background-color: #000; font-family: 'Inter', sans-serif; margin: 0; padding: 0; color: #fff; }
        .container { max-width: 400px; margin: 100px auto; background: #1A1F2E; padding: 20px; border-radius: 12px; }
        h2 { text-align: center; color: #F97316; }
        input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 8px; border: none; background: #2D2D2D; color: #fff; }
        button { width: 100%; padding: 10px; background: #F97316; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        button:hover { background: #FBA63C; }
        .toggle-link { text-align: center; display: block; margin-top: 10px; color: #3B82F6; cursor: pointer; }
        .error { color: #EF4444; text-align: center; }
        .success { color: #22C55E; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <h2 id="formTitle">Admin Login</h2>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>

    <!-- LOGIN FORM -->
    <form method="POST" id="loginForm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="form_type" value="login">
        <input type="text" name="username" placeholder="Admin Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <!-- REGISTER FORM -->
    <form method="POST" id="registerForm" style="display:none;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="form_type" value="register">
        <input type="text" name="username" placeholder="New Admin Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="secret_code" placeholder="Enter Admins Code "required>
        <button type="submit">Register</button>
    </form>

    <span class="toggle-link" onclick="toggleForms()">Don’t have an account? Register</span>
</div>

<script>
function toggleForms() {
    const login = document.getElementById("loginForm");
    const register = document.getElementById("registerForm");
    const title = document.getElementById("formTitle");
    const link = document.querySelector(".toggle-link");

    if (login.style.display === "none") {
        login.style.display = "block";
        register.style.display = "none";
        title.innerText = "Admin Login";
        link.innerText = "Don’t have an account? Register";
    } else {
        login.style.display = "none";
        register.style.display = "block";
        title.innerText = "Admin Register";
        link.innerText = "Already have an account? Login";
    }
}
</script>
</body>
</html>
