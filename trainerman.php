<?php
session_start();

if (!isset($_SESSION['trainer_id'])) {
    header("Location: trainerlogin.php"); // Redirect to trainer login page
    exit();
}

// Fetch trainer details from the database
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$trainer_id = $_SESSION['trainer_id'];

// Fetch trainer name/email from trainerlog
$sql = "SELECT trainer_id, trainer_name, trainer_email FROM trainerlog WHERE trainer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();

$trainer = [
    'id' => '',
    'name' => '',
    'email' => '',
    'avatar' => '',
    'title' => 'Cardio Instructor'
];

if ($row = $result->fetch_assoc()) {
    $trainer['id'] = $row['trainer_id'];
    $trainer['name'] = $row['trainer_name'];
    $trainer['email'] = $row['trainer_email'];
}

// Fetch trainer image from trainer_details
$sql_img = "SELECT trainer_image FROM trainer_details WHERE id = ?";
$stmt_img = $conn->prepare($sql_img);
$stmt_img->bind_param("i", $trainer_id);
$stmt_img->execute();
$stmt_img->bind_result($trainer_image);
$stmt_img->fetch();
$stmt_img->close();

if (!empty($trainer_image)) {
    $trainer['avatar'] = "uploads/" . $trainer_image;
} else {
    $trainer['avatar'] = "https://images.pexels.com/photos/2379004/pexels-photo-2379004.jpeg?auto=compress&w=400&h=400&fit=crop";
}

// Fetch total courses for this trainer
$sql_courses = "SELECT COUNT(*) FROM trainer_courses WHERE trainer_id = ?";
$stmt_courses = $conn->prepare($sql_courses);
$stmt_courses->bind_param("i", $trainer_id);
$stmt_courses->execute();
$stmt_courses->bind_result($total_courses);
$stmt_courses->fetch();
$stmt_courses->close();

// Quick stats data
$quickStats = [
    'total_courses' => $total_courses,
    'active_students' => 0,
    'average_rating' => 4.8
];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Dashboard - Rawfit</title>
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
                    },
                    colors: {
                        primary: '#F97316', // Orange-500
                        secondary: '#EF4444', // Red-500
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-900 font-inter text-gray-100 min-h-screen">
    <!-- Navigation Header -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-black/90 backdrop-blur-md border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Brand -->
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-primary to-secondary rounded-lg flex items-center justify-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-xl">Rawfit</span>
                </div>

                <!-- Profile Menu -->
                <div class="relative">
                    <button id="profile-menu-button" class="flex items-center space-x-3 text-sm rounded-lg px-3 py-2 text-gray-300 hover:text-white hover:bg-gray-800 transition-colors focus:outline-none">
                        <img class="h-8 w-8 rounded-full object-cover" src="<?php echo $trainer['avatar']; ?>" alt="<?php echo htmlspecialchars($trainer['name']); ?>">
                        <span class="hidden md:block font-medium"><?php echo htmlspecialchars($trainer['name']); ?></span>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-gray-800/90 backdrop-blur-sm rounded-xl border border-gray-700 py-1 z-50">
                        <a href="trainer_profile.php" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            View Profile
                        </a>
                        <a href="logout.php" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-red-600/20 hover:text-red-400 transition-colors">
                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Sign Out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-8">
        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-400">Welcome <?php echo htmlspecialchars($trainer['name']); ?></p>
        </div>
        
        <!-- Content Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Add Course Sections -->
        <section class="lg:col-span-1 space-y-6">
           <!-- Add Course Section 1 -->
            <div class="h-[205px] bg-gray-800/60 backdrop-blur-sm rounded-xl border border-gray-700 p-6 flex flex-col items-center justify-center">
                <h1 class="text-lg font-semibold text-white mb-4">Add Course</h1>
                <p class="text-sm text-gray-400 text-center mb-4">Create a new course to share your expertise with our fitness community.</p>
              
                <a href="create-course.php" 
                class="w-full max-w-md bg-primary text-white py-3 px-4 rounded-lg font-medium 
                        hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-primary transition-all text-center block">
                    + Add New Course
                </a>
                <p class="text-xs text-gray-500 mt-2">Last updated: September 12, 2025, 10:45 AM IST</p>
            </div>
            <!-- Quick Actions -->
            <div class="h-[250px] bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="manage-courses.php" 
                       class="block w-full text-left px-4 py-3 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-all">
                        <div class="font-medium text-white">Manage Courses</div>
                        <div class="text-sm text-gray-400">Edit or delete existing courses</div>
                    </a>
                    <a href="view-students.php" 
                       class="block w-full text-left px-4 py-3 bg-gray-700/50 rounded-lg hover:bg-gray-700 transition-all">
                        <div class="font-medium text-white">View Students</div>
                        <div class="text-sm text-gray-400">See enrolled students and progress</div>
                    </a>
                  
                </div>
            </div>

        </section>

        <!-- Sidebar -->
        <aside class="lg:col-span-1 space-y-6">
            <!-- Trainer Profile Card -->
            <div class="h-[205px] bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700 p-6">
                <div class="flex items-center space-x-4">
                    <img class="h-16 w-16 rounded-full object-cover border border-gray-600" 
                         src="<?php echo $trainer['avatar']; ?>" 
                         alt="<?php echo htmlspecialchars($trainer['name']); ?>'s avatar">
                    <div>
                        <h3 class="text-lg font-semibold text-white">
                            <?php echo htmlspecialchars($trainer['name']); ?>
                        </h3>
                        <p class="text-sm text-gray-400">
                            <?php echo htmlspecialchars($trainer['title']); ?>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            <?php echo htmlspecialchars($trainer['email']); ?>
                        </p>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="trainer_profile.php" 
                       class="block w-full bg-transparent border border-primary text-primary py-2 px-4 rounded-lg font-medium 
                              hover:bg-primary/10 hover:text-orange-400 transition-all text-center">
                        View Full Profile
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="h-[205px] bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-400">Total Courses</span>
                        <span class="font-semibold text-primary">
                            <?php echo $quickStats['total_courses']; ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-400">Active Students</span>
                        <span class="font-semibold text-green-500">
                            <?php echo $quickStats['active_students']; ?>
                        </span>
                    </div>
                   
                </div>
            </div>

            
        </aside>
    </div>
</div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Profile dropdown functionality
            const profileButton = document.getElementById('profile-menu-button');
            const profileDropdown = document.getElementById('profile-dropdown');
            
            if (profileButton && profileDropdown) {
                profileButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', function() {
                    profileDropdown.classList.add('hidden');
                });

                profileDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Smooth animations for interactive elements
            const cards = document.querySelectorAll('.bg-gray-800\\/50');
            cards.forEach(card => {
                card.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                    this.style.boxShadow = '';
                });
            });
        });
    </script>
</body>
</html>