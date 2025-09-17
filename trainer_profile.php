<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get logged-in trainer details
$trainer_id = $_SESSION['trainer_id'];
$sql = "SELECT name, email, phone, address, age, dob, blood_group, location, gender, intrests, website, updated_at, trainer_image FROM trainer_details WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();

$userData = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'age' => '',
    'dob' => '',
    'blood_group' => '',
    'location' => '',
    'gender' => '',
    'interests' => '',
    'website' => '',
    'created_at' => '',
    'updated_at' => '',
    'trainer_image' => ''
];
if ($row = $result->fetch_assoc()) {
    foreach ($userData as $key => $val) {
        if (isset($row[$key])) $userData[$key] = $row[$key];
    }
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RawFit Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-900 font-inter text-white min-h-screen">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-black/90 backdrop-blur-md border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white">
                            <path d="M6.5 6.5h11v11h-11z"/>
                            <path d="M6.5 6.5L2 2"/>
                            <path d="M17.5 6.5L22 2"/>
                            <path d="M6.5 17.5L2 22"/>
                            <path d="M17.5 17.5L22 22"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-xl">RawFit</span>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="trainerman.php" class="nav-link flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9,22 9,12 15,12 15,22"/>
                        </svg>
                        <span>Home</span>
                    </a>
                    <a href="manage-courses.php" class="nav-link flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/>
                            <path d="M12 18h.01"/>
                        </svg>
                        <span>Course</span>
                    </a>
          
                </div>

                <!-- User Info -->
                <div class="relative flex items-center space-x-4">
                    <div class="hidden sm:block text-right">
                        <p class="text-white font-medium" id="userName"><?php echo htmlspecialchars($userData['name']); ?></p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center cursor-pointer" id="profileButton">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <!-- Dropdown Menu -->
                    <div id="profileDropdown" class="absolute top-full right-0 mt-2 w-48 bg-gray-800/90 backdrop-blur-md border border-gray-700 rounded-lg shadow-lg hidden z-50">
                       
                        <a href="logout.php" class="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">Logout</a>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div class="md:hidden flex items-center justify-around py-3 border-t border-gray-800">
                <a href="index.php" class="mobile-nav-link flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-gray-400 hover:text-orange-500">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9,22 9,12 15,12 15,22"/>
                    </svg>
                    <span class="text-xs">Home</span>
                </a>
                <a href="nutrition.php" class="mobile-nav-link flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-gray-400 hover:text-orange-500">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/>
                        <path d="M12 18h.01"/>
                    </svg>
                    <span class="text-xs">Nutrition</span>
                </a>
                <a href="trainer.php" class="mobile-nav-link flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-gray-400 hover:text-orange-500">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span class="text-xs">Trainers</span>
                </a>
            </div>
        </div>
    </nav>
            <br><br>
    <!-- Main Content -->
   <main class="pt-20 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Profile Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">
                Profile - <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-500"><?php echo htmlspecialchars(explode(" ", $userData['name'])[0]); ?></span>
            </h1>
            <p class="text-gray-400 text-lg">Manage your personal details and fitness progress.</p>
        </div>

        <!-- Profile Card -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
            <!-- User Info -->
            <div class="flex items-center space-x-4 mb-6">
                <?php if (!empty($userData['trainer_image'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($userData['trainer_image']); ?>" alt="Profile Image" class="w-16 h-16 rounded-full object-cover border-2 border-orange-500">
                <?php else: ?>
                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-orange-500 to-red-500 flex items-center justify-center">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                <?php endif; ?>
                <div>
                    <h2 class="text-2xl font-semibold text-white"><?php echo htmlspecialchars($userData['name']); ?></h2>
                    <p class="text-gray-400 text-sm">Member since: August 2025</p>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
             <!-- Contact Information -->
<div class="bg-gray-800/70 p-7 rounded-lg ">
    <h2 class="text-lg font-bold text-orange mb-3">Contact Information</h2>
    <p class="text-gray-300 mb-4">Name: <?php echo htmlspecialchars($userData['name']); ?></p>
    <p class="text-gray-300 mb-4">Email: <?php echo htmlspecialchars($userData['email']); ?></p>
    <p class="text-gray-300 mb-4">Phone: <?php echo htmlspecialchars($userData['phone']); ?></p>
    <p class="text-gray-300 mb-4">Address: <?php echo htmlspecialchars($userData['address'] ?? 'N/A'); ?></p>
    <p class="text-gray-300 mb-4">Age: <?php echo htmlspecialchars($userData['age']); ?></p>
    <p class="text-gray-300 mb-4">Date of Birth: <?php echo htmlspecialchars($userData['dob']); ?></p>
    <p class="text-gray-300 mb-4">Blood Group: <?php echo htmlspecialchars($userData['blood_group']); ?></p>
    <p class="text-gray-300 mb-4">Location: <?php echo htmlspecialchars($userData['location']); ?></p>
    <p class="text-gray-300 mb-4">Gender/Pronouns: <?php echo htmlspecialchars($userData['gender']); ?></p>
    <p class="text-gray-300 mb-4">Interests/Hobbies: <?php echo htmlspecialchars($userData['interests']); ?></p>
    <p class="text-gray-300 mb-4">Website/Link: <?php echo htmlspecialchars($userData['website']); ?></p>
    <p class="text-gray-300 mb-4">Last Updated: <?php echo htmlspecialchars($userData['updated_at']); ?></p>

</div>

                <!-- Fitness Stats -->
                <div class="bg-gray-800/70 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-300 mb-3">Fitness Stats</h3>
             
                </div>
            </div>

            <!-- Edit Profile Button -->
            <div class="mt-6 text-right">
                <a href="trainer_edit_profile.php" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg text-white font-medium hover:from-orange-600 hover:to-red-600 transition-colors">
                    Edit Profile <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ml-2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileButton = document.getElementById('profileButton');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profileButton && profileDropdown) {
            profileButton.addEventListener('click', function(e) {
                e.preventDefault();
                profileDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });
        }
    });
    </script>
</body>
</html>