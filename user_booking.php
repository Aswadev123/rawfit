<?php
// -----------------------------
// COURSE DETAILS PAGE
// -----------------------------

ini_set('display_errors', 1); // dev only
error_reporting(E_ALL);

session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Logged-in user info
$user_id = intval($_SESSION['user_id']);
$userName = "User";
$stmt = $conn->prepare("SELECT name FROM register WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $userName = $row['name'];
}
$stmt->close();

// Get course_id from URL
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

$course = null;
$trainer = null;

if ($course_id > 0) {
    // Fetch course details
    $sql = "SELECT * FROM trainer_courses WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $course = [
            'id' => $row['id'],
            'trainer_id' => $row['trainer_id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'category' => $row['category'],
            'duration' => $row['duration'],
            'status' => $row['status'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'image' => !empty($row['image_path']) ? "uploads/" . $row['image_path'] : "https://via.placeholder.com/400x300",
            'price' => rand(60, 85) // demo price
        ];
    }
    $stmt->close();

    // Fetch trainer details (from trainers table if exists)
    if ($course) {
        $tstmt = $conn->prepare("SELECT * FROM trainer_details WHERE id = ? LIMIT 1");
        $tstmt->bind_param("i", $course['trainer_id']);
        $tstmt->execute();
        $tres = $tstmt->get_result();
        if ($trow = $tres->fetch_assoc()) {
            $trainer = $trow;
        }
        $tstmt->close();

        // fallback: all fields empty if not found
        if (!$trainer) {
            $trainer = [
                'id' => '',
                'name' => 'Unknown Trainer',
                'email' => '',
                'phone' => '',
                'address' => '',
                'age' => '',
                'dob' => '',
                'blood_group' => '',
                'location' => '',
                'gender' => '',
                'intrests' => '',
                'website' => '',
                'created_at' => '',
                'updated_at' => '',
                'trainer_image' => ''
            ];
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>RawFit - Course Details</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { fontFamily: { inter: ['Inter', 'sans-serif'] } } }
    }
  </script>
</head>
<body class="bg-gray-900 font-inter">

<!-- Navbar -->
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
                    <a href="home.php" class="nav-link active flex items-center space-x-2 px-3 py-2 rounded-lg bg-orange-500 text-white transition-colors">
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
                    <a href="trainer.php" class="nav-link flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span>Trainers</span>
                    </a>
                </div>

                <!-- User Info -->
                <div class="relative flex items-center space-x-4">
                    <div class="hidden sm:block text-right">
                        <p class="text-white font-medium" id="userName"><?php echo htmlspecialchars($userName); ?></p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center cursor-pointer" id="profileButton">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <!-- Dropdown Menu -->
                    <div id="profileDropdown" class="absolute top-full right-0 mt-2 w-48 bg-gray-800/90 backdrop-blur-md border border-gray-700 rounded-lg shadow-lg hidden z-50">
                        <a href="profile.php" class="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">View Profile</a>
                        <a href="logout.php" class="block px-4 py-2 text-white hover:bg-gray-700 transition-colors">Logout</a>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div class="md:hidden flex items-center justify-around py-3 border-t border-gray-800">
                <a href="home.php" class="mobile-nav-link flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9,22 9,12 15,12 15,22"/>
                    </svg>
                    <span class="text-xs">Home</span>
                </a>
                <a href="nutrition.php" class="mobile-nav-link flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/>
                        <path d="M12 18h.01"/>
                    </svg>
                    <span class="text-xs">Nutrition</span>
                </a>
                <a href="trainer.php" class="mobile-nav-link active flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-orange-500">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span class="text-xs">Trainer</span>
                </a>
            </div>
        </div>
    </nav>
<br><br>

<main class="pt-24 min-h-screen p-4 sm:p-6 lg:p-8">
  <div class="max-w-7xl mx-auto">
    <?php if ($course && $trainer): ?>
      
      <!-- Course Heading -->
      <div class="mb-8">
       <h1 class="text-3xl sm:text-4xl font-bold mb-2 bg-gradient-to-r from-orange-500 to-red-500 text-transparent bg-clip-text">
  <?php echo htmlspecialchars($course['title']); ?>
</h1>
        <p class="text-gray-400 text-lg">
  Led by <span class="font-semibold text-white bg-gradient-to-r from-orange-400 to-red-400 text-transparent bg-clip-text"><?php echo htmlspecialchars($trainer['name']); ?></span>
</p>
</div>

      <!-- Course & Details -->
      <div class="grid lg:grid-cols-2 gap-8 mb-12">
        <div>
          <img src="<?php echo htmlspecialchars($course['image']); ?>" alt="Course Image"
            class="w-full h-96 object-cover rounded-xl shadow-lg">
        </div>
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
          <h2 class="text-2xl font-bold text-white mb-4">Course Details</h2>
          <div class="space-y-4">
            <div>
              <h3 class="text-lg font-semibold text-orange-400">Category</h3>
              <p class="text-gray-300"><?php echo htmlspecialchars($course['category']); ?></p>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-orange-400">Duration</h3>
              <p class="text-gray-300"><?php echo htmlspecialchars($course['duration']); ?></p>
            </div>
            
            <div>
              <h3 class="text-lg font-semibold text-orange-400">Schedule</h3>
              <p class="text-gray-300">
                From <?php echo htmlspecialchars(date('M d, Y', strtotime($course['start_date']))); ?> 
                to <?php echo htmlspecialchars(date('M d, Y', strtotime($course['end_date']))); ?>
              </p>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-orange-400">Status</h3>
              <p class="text-gray-300"><?php echo htmlspecialchars($course['status']); ?></p>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-orange-400">Description</h3>
              <p class="text-gray-300"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
            </div>
            <a href="book.php"
              class="block text-center bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-300">
              Get Course
            </a>
          </div>
        </div>
      </div>

      <!-- Trainer Info -->
      <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
        <h2 class="text-2xl font-bold text-white mb-4">About Your Trainer</h2>
        <div class="flex flex-col md:flex-row gap-6">
          <div class="w-32 h-32 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center overflow-hidden">
            <?php if (!empty($trainer['trainer_image'])): ?>
              <img src="<?php echo htmlspecialchars($trainer['trainer_image']); ?>" alt="Trainer Image" class="w-32 h-32 object-cover rounded-full">
            <?php else: ?>
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" class="text-white">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            <?php endif; ?>
          </div>
          <div>
            <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($trainer['name']); ?></h3>
            <p class="text-orange-400 font-medium mb-2"><?php echo htmlspecialchars($trainer['email']); ?></p>
            <p class="text-gray-300 mb-2">Phone: <?php echo htmlspecialchars($trainer['phone']); ?></p>
            <p class="text-gray-300 mb-2">Address: <?php echo htmlspecialchars($trainer['address']); ?></p>
            <p class="text-gray-300 mb-2">Age: <?php echo htmlspecialchars($trainer['age']); ?></p>
            <p class="text-gray-300 mb-2">DOB: <?php echo htmlspecialchars($trainer['dob']); ?></p>
            <p class="text-gray-300 mb-2">Blood Group: <?php echo htmlspecialchars($trainer['blood_group']); ?></p>
            <p class="text-gray-300 mb-2">Location: <?php echo htmlspecialchars($trainer['location']); ?></p>
            <p class="text-gray-300 mb-2">Gender: <?php echo htmlspecialchars($trainer['gender']); ?></p>
            <p class="text-gray-300 mb-2">Interests: <?php echo nl2br(htmlspecialchars($trainer['intrests'])); ?></p>
            <p class="text-gray-300 mb-2">Website: <?php echo htmlspecialchars($trainer['website']); ?></p>
            <p class="text-gray-300 mb-2">Created At: <?php echo htmlspecialchars($trainer['created_at']); ?></p>
            <p class="text-gray-300 mb-2">Updated At: <?php echo htmlspecialchars($trainer['updated_at']); ?></p>
          </div>
        </div>
      </div>

    <?php else: ?>
      <!-- Course Not Found -->
      <div class="text-center py-12">
        <h3 class="text-xl font-semibold text-gray-300 mb-2">Course Not Found</h3>
        <p class="text-gray-500">The requested course could not be found.</p>
        <a href="trainer.php" class="mt-4 inline-block bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold py-3 px-6 rounded-lg">
          Back to Trainers
        </a>
      </div>
    <?php endif; ?>
  </div>
</main>
</body>
</html>
