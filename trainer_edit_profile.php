<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Use trainer_id from session
$trainer_id = $_SESSION['trainer_id'];

// Fetch trainer details (add trainer_image)
$sql = "SELECT name, email, phone, address, age, dob, blood_group, location, gender, intrests, website, trainer_image FROM trainer_details WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $address, $age, $dob, $blood_group, $location, $gender, $intrests, $website, $trainer_image);
$stmt->fetch();
$stmt->close();

$userData = [
    'name' => $name ?? 'Trainer',
    'email' => $email ?? '',
    'phone' => $phone ?? '',
    'address' => $address ?? '',
    'age' => $age ?? '',
    'dob' => $dob ?? '',
    'blood_group' => $blood_group ?? '',
    'location' => $location ?? '',
    'gender' => $gender ?? '',
    'interests' => $intrests ?? '',
    'website' => $website ?? '',
    'trainer_image' => $trainer_image ?? '',
];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? $userData['name'];
    $email = $_POST['email'] ?? $userData['email'];
    $phone = $_POST['phone'] ?? $userData['phone'];
    $address = $_POST['address'] ?? $userData['address'];
    $age = $_POST['age'] ?? $userData['age'];
    $dob = $_POST['dob'] ?? $userData['dob'];
    $blood_group = $_POST['blood_group'] ?? $userData['blood_group'];
    $location = $_POST['location'] ?? $userData['location'];
    $gender = $_POST['gender'] ?? $userData['gender'];
    $intrests = $_POST['interests'] ?? $userData['interests'];
    $website = $_POST['website'] ?? $userData['website'];

    // Handle image upload
    $trainer_image = $userData['trainer_image'];
    if (isset($_FILES['trainer_image']) && $_FILES['trainer_image']['error'] == UPLOAD_ERR_OK) {
        $imgTmp = $_FILES['trainer_image']['tmp_name'];
        $imgName = basename($_FILES['trainer_image']['name']);
        $imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imgExt, $allowed)) {
            $newName = 'trainer_' . $trainer_id . '_' . time() . '.' . $imgExt;
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $imgPath = $uploadDir . $newName;
            if (move_uploaded_file($imgTmp, $imgPath)) {
                $trainer_image = $newName;
            }
        }
    }

    $sql = "INSERT INTO trainer_details (id, name, email, phone, address, age, dob, blood_group, location, gender, intrests, website, trainer_image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            name = VALUES(name), 
            email = VALUES(email), 
            phone = VALUES(phone), 
            address = VALUES(address), 
            age = VALUES(age), 
            dob = VALUES(dob), 
            blood_group = VALUES(blood_group), 
            location = VALUES(location), 
            gender = VALUES(gender), 
            intrests = VALUES(intrests), 
            website = VALUES(website),
            trainer_image = VALUES(trainer_image)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssisssssss", $trainer_id, $name, $email, $phone, $address, $age, $dob, $blood_group, $location, $gender, $intrests, $website, $trainer_image);

    if ($stmt->execute()) {
        header("Location: trainer_profile.php?success=1");
        exit();
    } else {
        $error = "Failed to update profile. Please try again.";
    }
    $stmt->close();
}

// Fallback: If name is empty, fetch from trainerlog
if (empty($name)) {
    $sql = "SELECT name FROM trainerlog WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $trainer_id);
    $stmt->execute();
    $stmt->bind_result($reg_name);
    $stmt->fetch();
    $stmt->close();
    $userData['name'] = $reg_name ?? 'Trainer';
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RawFit Edit Profile</title>
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
                    <a href="nutrition.php" class="nav-link flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/>
                            <path d="M12 18h.01"/>
                        </svg>
                        <span>Nutrition</span>
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
                        <a href="trainer_profile.php" class="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">View Profile</a>
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
    <br><br><br>

    <!-- Main Content -->
    <main class="pt-20 p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Edit Profile Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">
                    Edit Profile - <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-500"><?php echo htmlspecialchars(explode(" ", $userData['name'])[0]); ?></span>
                </h1>
                <p class="text-gray-400 text-lg">Update your personal details and preferences.</p>
            </div>

            <!-- Edit Profile Form -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 sm:p-8 border border-gray-700">
                <?php if (isset($error)): ?>
                    <p class="text-red-500 mb-4"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <p class="text-green-500 mb-4">Profile updated successfully!</p>
                <?php endif; ?>
                <form method="POST" action="" class="space-y-6" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div>
                            <label class="block text-gray-300 mb-2" for="name">Full Name</label>
                            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($userData['name']); ?>" class="w-full bg-gray-700 text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                            <label class="block text-gray-300 mt-4 mb-2" for="email">Email</label>
                            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($userData['email']); ?>" class="w-full bg-gray-700 text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                            <label class="block text-gray-300 mt-4 mb-2" for="phone">Phone</label>
                            <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($userData['phone']); ?>" class="w-full bg-gray-700 text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                            <label class="block text-gray-300 mt-4 mb-2" for="address">Address</label>
                            <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($userData['address']); ?>" class="w-full bg-gray-700 text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                            <label class="block text-gray-300 mt-4 mb-2" for="age">Age</label>
                            <input type="number" name="age" id="age" value="<?php echo htmlspecialchars($userData['age']); ?>" class="w-full bg-gray-700 text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                            <label class="block text-gray-300 mt-4 mb-2" for="dob">Date of Birth</label>
                            <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($userData['dob']); ?>" class="w-full bg-gray-700 text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                            <label class="block text-gray-300 mt-4 mb-2" for="blood_group">Blood Group</label>
                            <input type="text" name="blood_group" id="blood_group" value="<?php echo htmlspecialchars($userData['blood_group']); ?>" class="w-full bg-gray-700 text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                        </div>

                        <!-- Additional Details -->
                        <div>
                            <label class="block text-gray-300 mb-2" for="location">Location</label>
                            <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($userData['location']); ?>" class="w-full bg-gray-700 text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                            <label class="block text-gray-300 mt-4 mb-2" for="gender">Gender/Pronouns</label>
                            <input type="text" name="gender" id="gender" value="<?php echo htmlspecialchars($userData['gender']); ?>" class="w-full bg-gray-700 text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                            <label class="block text-gray-300 mt-4 mb-2" for="interests">Interests/Hobbies</label>
                            <input type="text" name="interests" id="interests" value="<?php echo htmlspecialchars($userData['interests']); ?>" class="w-full bg-gray-700 text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                            <label class="block text-gray-300 mt-4 mb-2" for="website">Website/Link</label>
                            <input type="url" name="website" id="website" value="<?php echo htmlspecialchars($userData['website']); ?>" class="w-full bg-gray-700 text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                        
                            <label class="block text-gray-300 mt-4 mb-2" for="trainer_image">Profile Image</label>
                            <?php if (!empty($userData['trainer_image'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($userData['trainer_image']); ?>" alt="Profile Image" class="h-20 mb-2 rounded-full">
                            <?php endif; ?>
                            <input type="file" name="trainer_image" id="trainer_image" accept="image/jpeg,image/png,image/gif" class="w-full bg-gray-700 text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 text-right">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg text-white font-medium hover:from-orange-600 hover:to-red-600 hover:scale-105 transition-all duration-300 shadow-lg">
                            Save Changes
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ml-2">
                                <path d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800/50 backdrop-blur-sm py-12 border-t border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
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
                    <p class="text-gray-400 mb-4">Transform your body, transform your life. Join our fitness community and achieve your goals with expert guidance and state-of-the-art facilities.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="m16 11.37-4-4-4 4"/><path d="M21 16.5l-4-4-4 4"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                        </a>
                    </div>
                </div>
               
                <div>
                    <h4 class="text-white font-semibold mb-4">Support</h4>
                    <ul class="text-gray-400 space-y-2">
                        <li><a href="#" class="hover:text-orange-500 transition-colors">FAQ</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">Contact Us</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Contact Info</h4>
                    <ul class="text-gray-400 space-y-2">
                        <li>123 Fitness Street</li>
                        <li>FitCity, GC 12345</li>
                        <li>(555) 123-4567</li>
                        <li>info@rawfitgym.com</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-700 flex flex-col sm:flex-row justify-between items-center">
                <p class="text-gray-400">&copy; 2025 RawFit Gym. All rights reserved.</p>
                <div class="flex space-x-4 mt-4 sm:mt-0">
                    <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize profile page
            initializeProfile();

            // Update navigation active states
            updateNavigation();
        });

        function initializeProfile() {
            // Load user data
            const userData = {
                name: '<?php echo htmlspecialchars($userData['name']); ?>'
            };

            // Update user name in header
            const userNameElement = document.getElementById('userName');
            if (userNameElement) {
                userNameElement.textContent = userData.name;
            }
        }

        function updateNavigation() {
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.nav-link');
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

            // Remove active class from all links
            [...navLinks, ...mobileNavLinks].forEach(link => {
                link.classList.remove('active', 'bg-orange-500', 'text-white', 'text-orange-500');
                link.classList.add('text-gray-300', 'hover:text-white', 'hover:bg-gray-800');
            });

            // Add active class to profile page link in dropdown
            const profileDropdownLink = document.querySelector('a[href="trainer_profile.php"]');
            if (profileDropdownLink) {
                profileDropdownLink.classList.add('bg-orange-500', 'text-white');
                profileDropdownLink.classList.remove('text-gray-300', 'hover:text-white', 'hover:bg-gray-800');
            }
        }

        // Profile button functionality
        const profileButton = document.getElementById('profileButton');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profileButton && profileDropdown) {
            profileButton.addEventListener('click', function(e) {
                e.preventDefault();
                profileDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>