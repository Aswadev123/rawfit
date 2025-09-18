<?php
session_start();

// ✅ Only allow logged-in users
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'user') {
    header("Location: login_owner.php"); // Redirect to user login page if not logged in or not a user
    exit;
}

$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// ✅ Fetch all gyms (available to users)
$gyms = $conn->query("SELECT gym_name, location, gym_address, gym_city, gym_state, gym_zip, gym_phone, gym_email, timings, facilities, gym_description, gym_image 
                      FROM gyms 
                      ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Available Gyms</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white font-inter">

<!-- Navbar -->
<nav class="bg-gray-800 px-6 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-orange-400">RawFit - User Dashboard</h1>
    <div class="flex items-center space-x-4">
        <span class="text-gray-300">Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
        <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">Logout</a>
    </div>
</nav>

<!-- Available Gyms Section -->
<div class="max-w-6xl mx-auto mt-10 bg-gray-800 p-8 rounded-xl shadow-lg">
    <h2 class="text-2xl font-semibold text-orange-400 mb-6">Available Gyms</h2>

    <?php if ($gyms->num_rows > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php while($row = $gyms->fetch_assoc()): ?>
                <div class="bg-gray-700 rounded-lg shadow-md p-6">
                    <?php if (!empty($row['gym_image'])): ?>
                        <img src="uploads/gyms/<?= htmlspecialchars($row['gym_image']); ?>" alt="Gym Image" class="w-full h-48 object-cover rounded-lg mb-4">
                    <?php else: ?>
                        <div class="w-full h-48 bg-gray-600 rounded-lg mb-4 flex items-center justify-center text-gray-400">No Image</div>
                    <?php endif; ?>
                    <h3 class="text-xl font-semibold text-orange-400"><?= htmlspecialchars($row['gym_name']); ?></h3>
                    <p class="text-gray-300"><strong>Location:</strong> <?= htmlspecialchars($row['location']); ?></p>
                    <p class="text-gray-300"><strong>Address:</strong> <?= htmlspecialchars($row['gym_address']); ?></p>
                    <p class="text-gray-300"><strong>City/State/ZIP:</strong> <?= htmlspecialchars($row['gym_city'] . ', ' . $row['gym_state'] . ' ' . $row['gym_zip']); ?></p>
                    <p class="text-gray-300"><strong>Phone:</strong> <?= htmlspecialchars($row['gym_phone']); ?></p>
                    <p class="text-gray-300"><strong>Email:</strong> <?= htmlspecialchars($row['gym_email']); ?></p>
                    <p class="text-gray-300"><strong>Timings:</strong> <?= htmlspecialchars($row['timings']); ?></p>
                    <p class="text-gray-300"><strong>Facilities:</strong> <?= htmlspecialchars($row['facilities']); ?></p>
                    <p class="text-gray-300"><strong>Description:</strong> <?= htmlspecialchars($row['gym_description'] ?: 'No description available'); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-400">No gyms available yet.</p>
    <?php endif; ?>
</div>

</body>
</html>