<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// ✅ Only allow admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: adminlogin.php?action=login");
    exit;
}

// ✅ Logout handling
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: adminlogin.php?action=login");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete user
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM register WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit;
}

// Handle update user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE register SET name=?, email=?, phone=?, role=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $email, $phone, $role, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php");
    exit;
}

// Search & filter users
$search = $_GET['search'] ?? '';
$filter_role = $_GET['role'] ?? '';

$sql = "SELECT id, name, email, phone, role FROM register WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $search_term = "%$search%";
    $params[] = &$search_term;
    $params[] = &$search_term;
    $types .= "ss";
}

if (!empty($filter_role)) {
    $sql .= " AND role = ?";
    $params[] = &$filter_role;
    $types .= "s";
}

$sql .= " ORDER BY id ASC";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    array_unshift($params, $types);
    call_user_func_array([$stmt, 'bind_param'], $params);
}

$stmt->execute();
$result = $stmt->get_result();

// ✅ Fetch messages for admin
$msgResult = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - RawFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=M+PLUS+Rounded+1c:wght@700&family=Orbitron:wght@700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #000; font-family: 'Inter', sans-serif; margin: 0; padding: 0; color: #fff; }
        header { background: #1A1F2E; padding: 20px; text-align: center; font-family: 'Orbitron', sans-serif; font-size: 28px; color: #F97316; display: flex; justify-content: space-between; align-items: center; }
        header .logout { font-size: 16px; background: #EF4444; color: #fff; padding: 8px 12px; border-radius: 6px; text-decoration: none; }
        header .logout:hover { background: #F87171; }
        .container { padding: 20px; max-width: 1100px; margin: auto; }
        h2 { color: #F97316; margin-bottom: 15px; }
        form.search-bar { margin-bottom: 20px; display: flex; gap: 10px; }
        input[type="text"], select { padding: 10px; border-radius: 8px; border: none; background: #2D2D2D; color: #fff; }
        .btn-search { background: #F97316; color: white; border: none; border-radius: 8px; padding: 10px 15px; cursor: pointer; font-weight: bold; }
        .btn-search:hover { background: #FBA63C; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: #1A1F2E; border-radius: 12px; overflow: hidden; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #333; }
        th { background: #F97316; color: #fff; }
        tr:hover { background: #2D2D2D; }
        .btn { padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-edit { background: #3B82F6; color: white; }
        .btn-delete { background: #EF4444; color: white; }
        .btn-edit:hover { background: #60A5FA; }
        .btn-delete:hover { background: #F87171; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); justify-content: center; align-items: center; }
        .modal-content { background: #1A1F2E; padding: 20px; border-radius: 12px; width: 400px; }
        .modal-content h2 { color: #F97316; margin-bottom: 20px; }
        input, select { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 8px; border: none; background: #2D2D2D; color: #fff; }
        .btn-save { background: #22C55E; color: white; }
        .btn-save:hover { background: #4ADE80; }
        .close-btn { background: #EF4444; color: white; float: right; border-radius: 6px; padding: 5px 10px; cursor: pointer; }
    </style>
</head>
<body>

<header>
    <div>Admin Dashboard - RawFit</div>
    <a href="admin.php?action=logout" class="logout">Logout</a>
</header>

<div class="container">
    <h2>Manage Users</h2>

    <!-- Search + Filter -->
    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search by name or email..." value="<?= htmlspecialchars($search); ?>">
        <select name="role">
            <option value="">All Roles</option>
            <option value="user" <?= $filter_role === "user" ? "selected" : "" ?>>User</option>
            <option value="trainer" <?= $filter_role === "trainer" ? "selected" : "" ?>>Trainer</option>
            <option value="admin" <?= $filter_role === "admin" ? "selected" : "" ?>>Admin</option>
        </select>
        <button type="submit" class="btn-search">Search</button>
    </form>

    <!-- Users Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']); ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['phone']); ?></td>
                <td><?= htmlspecialchars($row['role']); ?></td>
                <td>
                    <button class="btn btn-edit" onclick="openModal(<?= $row['id']; ?>, '<?= htmlspecialchars($row['name'], ENT_QUOTES); ?>', '<?= htmlspecialchars($row['email'], ENT_QUOTES); ?>', '<?= htmlspecialchars($row['phone'], ENT_QUOTES); ?>', '<?= $row['role']; ?>')">Edit</button>
                    <a href="admin.php?delete=<?= $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="btn btn-delete">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- Contact Messages Section -->
    <h2 style="margin-top:40px;">Contact Messages</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Message</th>
            <th>Date</th>
        </tr>
        <?php while ($msg = $msgResult->fetch_assoc()): ?>
            <tr>
                <td><?= $msg['id']; ?></td>
                <td><?= htmlspecialchars($msg['name']); ?></td>
                <td><?= htmlspecialchars($msg['email']); ?></td>
                <td><?= htmlspecialchars($msg['phone']); ?></td>
                <td><?= htmlspecialchars($msg['message']); ?></td>
                <td><?= $msg['created_at']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">X</span>
        <h2>Edit User</h2>
        <form method="POST">
            <input type="hidden" name="id" id="edit_id">
            <label>Name</label>
            <input type="text" name="name" id="edit_name" required>
            <label>Email</label>
            <input type="email" name="email" id="edit_email" required>
            <label>Phone</label>
            <input type="text" name="phone" id="edit_phone" required>
            <label>Role</label>
            <select name="role" id="edit_role">
                <option value="user">User</option>
                <option value="trainer">Trainer</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="update_user" class="btn btn-save">Save</button>
        </form>
    </div>
</div>

<script>
    function openModal(id, name, email, phone, role) {
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_email").value = email;
        document.getElementById("edit_phone").value = phone;
        document.getElementById("edit_role").value = role;
        document.getElementById("editModal").style.display = "flex";
    }
    function closeModal() {
        document.getElementById("editModal").style.display = "none";
    }
</script>

</body>
</html>
<?php $conn->close(); ?>
