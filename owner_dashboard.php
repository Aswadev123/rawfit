<?php
session_start();

// ✅ Only allow logged-in gym owners
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'owner') {
    header("Location: ownerlogin.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$owner_id = $_SESSION['owner_id'];

// ✅ Handle Delete Gym
if (isset($_GET['delete'])) {
    $gym_id = intval($_GET['delete']);
    // Delete image first
    $res = $conn->query("SELECT gym_image FROM gyms WHERE gym_id=$gym_id AND owner_id=$owner_id");
    if ($res && $res->num_rows) {
        $img = $res->fetch_assoc()['gym_image'];
        if ($img && file_exists("uploads/gyms/$img")) unlink("uploads/gyms/$img");
    }
    $stmt = $conn->prepare("DELETE FROM gyms WHERE gym_id=? AND owner_id=?");
    $stmt->bind_param("ii", $gym_id, $owner_id);
    $stmt->execute();
    $stmt->close();
    header("Location: owner_dashboard.php");
    exit;
}

// ✅ Handle Add/Edit Gym
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_gym'])) {
    $gym_id        = intval($_POST['gym_id'] ?? 0);
    $gym_name      = $_POST['gym_name'] ?? '';
    $location      = $_POST['location'] ?? '';
    $phone         = $_POST['phone'] ?? '';
    $timings       = $_POST['timings'] ?? '';
    $facilities    = $_POST['facilities'] ?? '';
    $gym_address   = $_POST['gym_address'] ?? '';
    $gym_city      = $_POST['gym_city'] ?? '';
    $gym_state     = $_POST['gym_state'] ?? '';
    $gym_zip       = $_POST['gym_zip'] ?? '';
    $gym_phone     = $_POST['gym_phone'] ?? '';
    $gym_email     = $_POST['gym_email'] ?? '';
    $gym_description = $_POST['gym_description'] ?? '';

    // Image upload
    $gym_image = null;
    if (isset($_FILES['gym_image']) && $_FILES['gym_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "uploads/gyms/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $ext = pathinfo($_FILES['gym_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid("gym_", true) . "." . strtolower($ext);
        $filepath = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['gym_image']['tmp_name'], $filepath)) {
            $gym_image = $filename;
        }
    }

    if ($gym_id > 0) {
        // Update existing gym
        $sql = "UPDATE gyms SET gym_name=?, location=?, phone=?, timings=?, facilities=?, gym_address=?, gym_city=?, gym_state=?, gym_zip=?, gym_phone=?, gym_email=?, gym_description=?"
             . ($gym_image ? ", gym_image='$gym_image'" : "")
             . " WHERE gym_id=? AND owner_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssssii", $gym_name, $location, $phone, $timings, $facilities, $gym_address, $gym_city, $gym_state, $gym_zip, $gym_phone, $gym_email, $gym_description, $gym_id, $owner_id);
    } else {
        // Add new gym
        $sql = "INSERT INTO gyms (owner_id, gym_name, location, phone, timings, facilities, gym_image, gym_address, gym_city, gym_state, gym_zip, gym_phone, gym_email, gym_description, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssssssssss", $owner_id, $gym_name, $location, $phone, $timings, $facilities, $gym_image, $gym_address, $gym_city, $gym_state, $gym_zip, $gym_phone, $gym_email, $gym_description);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: owner_dashboard.php");
    exit;
}

// ✅ Pre-fill form for editing
$edit_gym = null;
if (isset($_GET['edit'])) {
    $gym_id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM gyms WHERE gym_id=$gym_id AND owner_id=$owner_id");
    if ($res && $res->num_rows) $edit_gym = $res->fetch_assoc();
}

// ✅ Fetch all gyms
$gyms = $conn->query("SELECT * FROM gyms WHERE owner_id=$owner_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Gym Owner Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white font-inter">

<!-- Navbar -->
<nav class="bg-gray-800 px-6 py-4 flex justify-between items-center">
<h1 class="text-xl font-bold text-orange-400">RawFit - Owner Dashboard</h1>
<div class="flex items-center space-x-4">
<span class="text-gray-300">Welcome, <?= htmlspecialchars($_SESSION['owner_name']); ?></span>
<a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">Logout</a>
</div>
</nav>

<!-- Add/Edit Gym Form -->
<div class="max-w-4xl mx-auto mt-10 bg-gray-800 p-8 rounded-xl shadow-lg">
<h2 class="text-2xl font-semibold text-orange-400 mb-6"><?= $edit_gym ? "Edit Gym" : "Add Your Gym Details" ?></h2>

<form method="POST" enctype="multipart/form-data" class="space-y-4">
<input type="hidden" name="save_gym" value="1">
<input type="hidden" name="gym_id" value="<?= $edit_gym['gym_id'] ?? 0 ?>">

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<input type="text" name="gym_name" placeholder="Gym Name" value="<?= htmlspecialchars($edit_gym['gym_name'] ?? '') ?>" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
<input type="text" name="location" placeholder="Location" value="<?= htmlspecialchars($edit_gym['location'] ?? '') ?>" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
<input type="text" name="phone" placeholder="Phone" value="<?= htmlspecialchars($edit_gym['phone'] ?? '') ?>" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
<input type="text" name="timings" placeholder="Timings" value="<?= htmlspecialchars($edit_gym['timings'] ?? '') ?>" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
<input type="text" name="facilities" placeholder="Facilities" value="<?= htmlspecialchars($edit_gym['facilities'] ?? '') ?>" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
<input type="text" name="gym_address" placeholder="Gym Address" value="<?= htmlspecialchars($edit_gym['gym_address'] ?? '') ?>" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
<input type="text" name="gym_city" placeholder="City" value="<?= htmlspecialchars($edit_gym['gym_city'] ?? '') ?>" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
<input type="text" name="gym_state" placeholder="State" value="<?= htmlspecialchars($edit_gym['gym_state'] ?? '') ?>" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
<input type="text" name="gym_zip" placeholder="ZIP" value="<?= htmlspecialchars($edit_gym['gym_zip'] ?? '') ?>" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
<input type="text" name="gym_phone" placeholder="Gym Phone" value="<?= htmlspecialchars($edit_gym['gym_phone'] ?? '') ?>" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
<input type="email" name="gym_email" placeholder="Gym Email" value="<?= htmlspecialchars($edit_gym['gym_email'] ?? '') ?>" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
<input type="text" name="gym_description" placeholder="Description" value="<?= htmlspecialchars($edit_gym['gym_description'] ?? '') ?>" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
<input type="file" name="gym_image" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">

<button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all">
<?= $edit_gym ? "Update Gym" : "Add Gym" ?>
</button>
</div>
</form>
</div>

<!-- My Gyms Section -->
<div class="max-w-6xl mx-auto mt-10 bg-gray-800 p-8 rounded-xl shadow-lg">
<h2 class="text-2xl font-semibold text-orange-400 mb-6">My Gyms</h2>

<?php if ($gyms->num_rows > 0): ?>
<div class="overflow-x-auto">
<table class="w-full border border-gray-700 rounded-lg overflow-hidden">
<thead class="bg-gray-700">
<tr>
<th class="px-4 py-2 text-left">Image</th>
<th class="px-4 py-2 text-left">Gym Name</th>
<th class="px-4 py-2 text-left">Location</th>
<th class="px-4 py-2 text-left">Phone</th>
<th class="px-4 py-2 text-left">Timings</th>
<th class="px-4 py-2 text-left">Facilities</th>
<th class="px-4 py-2 text-center">Actions</th>
</tr>
</thead>
<tbody>
<?php while($row = $gyms->fetch_assoc()): ?>
<tr class="border-b border-gray-700">
<td class="px-4 py-2">
<?php if (!empty($row['gym_image'])): ?>
<img src="uploads/gyms/<?= htmlspecialchars($row['gym_image']); ?>" alt="Gym Image" class="w-16 h-16 object-cover rounded-lg">
<?php else: ?>
<span class="text-gray-400">No Image</span>
<?php endif; ?>
</td>
<td class="px-4 py-2"><?= htmlspecialchars($row['gym_name']); ?></td>
<td class="px-4 py-2"><?= htmlspecialchars($row['location']); ?></td>
<td class="px-4 py-2"><?= htmlspecialchars($row['phone']); ?></td>
<td class="px-4 py-2"><?= htmlspecialchars($row['timings']); ?></td>
<td class="px-4 py-2"><?= htmlspecialchars($row['facilities']); ?></td>
<td class="px-4 py-2 text-center space-x-2">
<a href="owner_dashboard.php?edit=<?= $row['gym_id']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg">Edit</a>
<a href="owner_dashboard.php?delete=<?= $row['gym_id']; ?>" onclick="return confirm('Delete this gym?');" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php else: ?>
<p class="text-gray-400">You haven’t added any gyms yet.</p>
<?php endif; ?>
</div>

</body>
</html>
