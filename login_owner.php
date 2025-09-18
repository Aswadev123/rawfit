<?php
session_start();

// DB connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$mode = isset($_GET['action']) && $_GET['action'] === 'register' ? 'register' : 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'login') {
        // ✅ Login
        $email = trim($_POST['owner_email']);
        $password = trim($_POST['owner_password']);

        $stmt = $conn->prepare("SELECT owner_id, owner_name, owner_email, owner_password FROM ownerlog WHERE owner_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['owner_password'])) {
                session_regenerate_id(true);
                $_SESSION['loggedin']     = true;
                $_SESSION['role']         = 'owner';
                $_SESSION['owner_id']     = $row['owner_id'];
                $_SESSION['owner_email']  = $row['owner_email'];
                $_SESSION['owner_name']   = $row['owner_name'];

                header("Location: owner_dashboard.php");
                exit;
            } else {
                $error = "❌ Invalid password.";
            }
        } else {
            $error = "❌ No account found with that email.";
        }

    } else {
        // ✅ Register
        $name     = trim($_POST['owner_name']);
        $email    = trim($_POST['owner_email']);
        $phone    = trim($_POST['owner_phone']);
        $password = trim($_POST['owner_password']);
        $hashed   = password_hash($password, PASSWORD_BCRYPT);

        // Check if email exists
        $check = $conn->prepare("SELECT owner_id FROM ownerlog WHERE owner_email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "❌ Email already registered.";
        } else {
            $stmt = $conn->prepare("INSERT INTO ownerlog (owner_name, owner_email, owner_phone, owner_password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashed);

            if ($stmt->execute()) {
                $new_owner_id = $stmt->insert_id;

                session_regenerate_id(true);
                $_SESSION['loggedin']     = true;
                $_SESSION['role']         = 'owner';
                $_SESSION['owner_id']     = $new_owner_id;
                $_SESSION['owner_email']  = $email;
                $_SESSION['owner_name']   = $name;

                header("Location: owner_dashboard.php");
                exit;
            } else {
                $error = "❌ Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $mode === 'login' ? 'Owner Login' : 'Owner Register'; ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center h-screen">

<div class="bg-gray-800 p-8 rounded-xl w-full max-w-md shadow-lg">
  <h2 class="text-2xl font-bold text-orange-400 mb-6 text-center">
    <?= $mode === 'login' ? 'Gym Owner Login' : 'Gym Owner Register'; ?>
  </h2>

  <?php if ($error): ?>
    <p class="mb-4 text-red-400"><?= $error; ?></p>
  <?php endif; ?>

  <?php if ($mode === 'login'): ?>
    <!-- ✅ LOGIN FORM -->
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="owner_email" required class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Password</label>
        <input type="password" name="owner_password" required class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
      </div>

      <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all">
        Login
      </button>
    </form>

    <p class="mt-4 text-gray-400 text-sm text-center">
      Don’t have an account? <a href="?action=register" class="text-orange-400 hover:underline">Register</a>
    </p>

  <?php else: ?>
    <!-- ✅ REGISTER FORM -->
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Full Name</label>
        <input type="text" name="owner_name" required class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="owner_email" required class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Phone</label>
        <input type="text" name="owner_phone" maxlength="10" required class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Password</label>
        <input type="password" name="owner_password" required class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
      </div>

      <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all">
        Register
      </button>
    </form>

    <p class="mt-4 text-gray-400 text-sm text-center">
      Already have an account? <a href="?action=login" class="text-orange-400 hover:underline">Login</a>
    </p>
  <?php endif; ?>
</div>

</body>
</html>
