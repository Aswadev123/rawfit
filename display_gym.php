<?php
session_start();

// ✅ Only allow logged-in users


$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// ✅ Fetch all gyms (available to users)
$gyms = $conn->query("SELECT * FROM gyms ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Dashboard - Available Gyms</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white font-inter">

<!-- Navbar -->
<nav class="bg-gray-800 px-6 py-4 flex justify-between items-center">
<h1 class="text-xl font-bold text-orange-400">RawFit - User Dashboard</h1>
<div class="flex items-center space-x-4">
<span class="text-gray-300">Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span> <!-- Assuming user_name in session -->
<a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">Logout</a>
</div>
</nav>

<!-- Available Gyms Section -->
<div class="max-w-6xl mx-auto mt-10 bg-gray-800 p-8 rounded-xl shadow-lg">
<h2 class="text-2xl font-semibold text-orange-400 mb-6">Available Gyms</h2>

<?php if ($gyms->num_rows > 0): ?>
<div class="overflow-x-auto">
<table class="w-full border border-gray-700 rounded-lg overflow-hidden">
<thead class="bg-gray-700">
<tr>
<th class="px-4 py-2 text-left">Image</th>
<th class="px-4 py-2 text-left">Gym Name</th>
<th class="px-4 py-2 text-left">Location</th>
<th class="px-4 py-2 text-left">Address</th>
<th class="px-4 py-2 text-left">City/State/ZIP</th>
<th class="px-4 py-2 text-left">Phone</th>
<th class="px-4 py-2 text-left">Email</th>
<th class="px-4 py-2 text-left">Timings</th>
<th class="px-4 py-2 text-left">Facilities</th>
<th class="px-4 py-2 text-left">Description</th>
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
<td class="px-4 py-2"><?= htmlspecialchars($row['gym_address']); ?></td>
<td class="px-4 py-2"><?= htmlspecialchars($row['gym_city'] . ' / ' . $row['gym_state'] . ' / ' . $row['gym_zip']); ?></td>
<td class="px-4 py-2"><?= htmlspecialchars($row['gym_phone'] ?? $row['phone']); ?></td> <!-- Fallback to phone if gym_phone empty -->
<td class="px-4 py-2"><?= htmlspecialchars($row['gym_email']); ?></td>
<td class="px-4 py-2"><?= htmlspecialchars($row['timings']); ?></td>
<td class="px-4 py-2"><?= htmlspecialchars($row['facilities']); ?></td>
<td class="px-4 py-2"><?= htmlspecialchars($row['gym_description']); ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php else: ?>
<p class="text-gray-400">No gyms available yet.</p>
<?php endif; ?>
</div>

</body>
</html>