<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get logged-in user name and nutrition profile
$user_id = $_SESSION['user_id'];
$sql = "SELECT name FROM register WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$userName = "User"; // Default name
if ($row = $result->fetch_assoc()) {
    $userName = $row['name'];
}

// Fetch existing nutrition profile
$sql = "SELECT age, gender, weight, height, activity_level, goal FROM nutrition_profiles WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


$nutritionData = [
    'age' => 25,
    'gender' => 'male',
    'weight' => 70,
    'height' => 175,
    'activity_level' => 'moderate',
    'goal' => 'maintain'
];
if ($row = $result->fetch_assoc()) {
    $nutritionData = [
        'age' => $row['age'],
        'gender' => $row['gender'],
        'weight' => $row['weight'],
        'height' => $row['height'],
        'activity_level' => $row['activity_level'],
        'goal' => $row['goal']
    ];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RawFit - Nutrition Calculator</title>
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
<body class="bg-gray-900 font-inter">
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
                    <a href="home.php" class="nav-link flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9,22 9,12 15,12 15,22"/>
                        </svg>
                        <span>Home</span>
                    </a>
                    <a href="#nutrition" class="nav-link active flex items-center space-x-2 px-3 py-2 rounded-lg bg-orange-500 text-white transition-colors">
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
                <a href="#nutrition" class="mobile-nav-link active flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-orange-500">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/>
                        <path d="M12 18h.01"/>
                    </svg>
                    <span class="text-xs">Nutrition</span>
                </a>
                <a href="trainer.php" class="mobile-nav-link flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-gray-400">
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

    <!-- Main Content -->
    <br><br><br>
    <main class="pt-20 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3 incorporar el texto completo3xl sm:text-4xl font-bold text-white mb-2">
                    Nutrition <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-500">Calculator</span>
                </h1>
                <p class="text-gray-400 text-lg">Track your calories, macros, and reach your nutrition goals</p>
            </div>

            <!-- Calculator Section -->
            <div class="grid lg:grid-cols-2 gap-8 mb-8">
                <!-- User Info Form -->
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 sm:p-8 border border-gray-700">
                    <h2 class="text-2xl font-bold text-white mb-6">Your Information</h2>
                    <div id="userInfoForm" class="space-y-6">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-300 font-medium mb-2">Age</label>
                                <input type="number" id="age" value="<?php echo htmlspecialchars($nutritionData['age']); ?>" min="15" max="100" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-gray-300 font-medium mb-2">Gender</label>
                                <select id="gender" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                    <option value="male" <?php echo $nutritionData['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo $nutritionData['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-300 font-medium mb-2">Weight (kg)</label>
                                <input type="number" id="weight" value="<?php echo htmlspecialchars($nutritionData['weight']); ?>" min="30" max="200" step="0.1" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-gray-300 font-medium mb-2">Height (cm)</label>
                                <input type="number" id="height" value="<?php echo htmlspecialchars($nutritionData['height']); ?>" min="100" max="250" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-300 font-medium mb-2">Activity Level</label>
                            <select id="activityLevel" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="sedentary" <?php echo $nutritionData['activity_level'] === 'sedentary' ? 'selected' : ''; ?>>Sedentary (little/no exercise)</option>
                                <option value="light" <?php echo $nutritionData['activity_level'] === 'light' ? 'selected' : ''; ?>>Light (light exercise 1-3 days/week)</option>
                                <option value="moderate" <?php echo $nutritionData['activity_level'] === 'moderate' ? 'selected' : ''; ?>>Moderate (moderate exercise 3-5 days/week)</option>
                                <option value="active" <?php echo $nutritionData['activity_level'] === 'active' ? 'selected' : ''; ?>>Active (hard exercise 6-7 days/week)</option>
                                <option value="veryActive" <?php echo $nutritionData['activity_level'] === 'veryActive' ? 'selected' : ''; ?>>Very Active (very hard exercise & physical job)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-300 font-medium mb-2">Goal</label>
                            <select id="goal" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="lose" <?php echo $nutritionData['goal'] === 'lose' ? 'selected' : ''; ?>>Lose Weight</option>
                                <option value="maintain" <?php echo $nutritionData['goal'] === 'maintain' ? 'selected' : ''; ?>>Maintain Weight</option>
                                <option value="gain" <?php echo $nutritionData['goal'] === 'gain' ? 'selected' : ''; ?>>Gain Weight</option>
                            </select>
                        </div>
                        <button id="submitInfoBtn" class="w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105">
                            Calculate & Save
                        </button>
                        <div id="form-message" class="text-center text-gray-400"></div>
                    </div>
                </div>

                <!-- Results -->
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 sm:p-8 border border-gray-700">
                    <h2 class="text-2xl font-bold text-white mb-6">Your Daily Requirements</h2>
                    <div class="space-y-6">
                        <div class="bg-gray-700/30 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-300">BMR (Base Metabolic Rate)</span>
                                <span class="text-white font-bold text-lg" id="bmrValue"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-300">TDEE (Total Daily Energy)</span>
                                <span class="text-white font-bold text-lg" id="tdeeValue"></span>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-orange-500/20 to-red-500/20 rounded-lg p-4 border border-orange-500/30">
                            <h3 class="text-orange-400 font-semibold mb-3">Target Intake</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Calories</span>
                                    <span class="text-white font-bold" id="targetCalories">2,050</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Protein</span>
                                    <span class="text-white font-bold" id="targetProtein">154g</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Carbs</span>
                                    <span class="text-white font-bold" id="targetCarbs">205g</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-300">Fats</span>
                                    <span class="text-white font-bold" id="targetFats">68g</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Macro Tracking -->
            <div class="grid lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                        Calories
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Consumed</span>
                            <span class="text-white font-bold" id="consumedCalories">970</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Remaining</span>
                            <span class="text-white font-bold" id="remainingCalories">1,080</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3">
                            <div class="h-3 rounded-full bg-gradient-to-r from-blue-500 to-blue-600" style="width: 47%" id="caloriesProgress"></div>
                        </div>
                        <div class="text-center">
                            <span class="text-blue-400 font-semibold" id="caloriesPercentage">47%</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                        Protein
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Consumed</span>
                            <span class="text-white font-bold" id="consumedProtein">40g</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Remaining</span>
                            <span class="text-white font-bold" id="remainingProtein">114g</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3">
                            <div class="h-3 rounded-full bg-gradient-to-r from-green-500 to-green-600" style="width: 26%" id="proteinProgress"></div>
                        </div>
                        <div class="text-center">
                            <span class="text-green-400 font-semibold" id="proteinPercentage">26%</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <div class="w-4 h-4 bg-yellow-500 rounded-full mr-2"></div>
                        Carbs
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Consumed</span>
                            <span class="text-white font-bold" id="consumedCarbs">105g</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Remaining</span>
                            <span class="text-white font-bold" id="remainingCarbs">100g</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3">
                            <div class="h-3 rounded-full bg-gradient-to-r from-yellow-500 to-yellow-600" style="width: 51%" id="carbsProgress"></div>
                        </div>
                        <div class="text-center">
                            <span class="text-yellow-400 font-semibold" id="carbsPercentage">51%</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <div class="w-4 h-4 bg-purple-500 rounded-full mr-2"></div>
                        Fats
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Consumed</span>
                            <span class="text-white font-bold" id="consumedFats">40g</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Remaining</span>
                            <span class="text-white font-bold" id="remainingFats">28g</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3">
                            <div class="h-3 rounded-full bg-gradient-to-r from-purple-500 to-purple-600" style="width: 59%" id="fatsProgress"></div>
                        </div>
                        <div class="text-center">
                            <span class="text-purple-400 font-semibold" id="fatsPercentage">59%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Food Log -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 sm:p-8 border border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">Today's Food Log</h2>
                    <button onclick="openFoodModal()" class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 flex items-center space-x-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        <span>Add Food</span>
                    </button>
                </div>
                <div class="space-y-4" id="foodLogList">
                    <div class="flex items-center justify-between p-4 bg-gray-700/30 rounded-lg">
                        <div>
                            <h4 class="text-white font-medium">Breakfast</h4>
                            <p class="text-gray-400 text-sm">420 cal • 25g protein • 45g carbs • 18g fat</p>
                        </div>
                        <button class="text-red-400 hover:text-red-300 transition-colors">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                <line x1="10" y1="11" x2="10" y2="17"/>
                                <line x1="14" y1="11" x2="14" y2="17"/>
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-700/30 rounded-lg">
                        <div>
                            <h4 class="text-white font-medium">Lunch</h4>
                            <p class="text-gray-400 text-sm">550 cal • 35g protein • 60g carbs • 22g fat</p>
                        </div>
                        <button class="text-red-400 hover:text-red-300 transition-colors">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                <line x1="10" y1="11" x2="10" y2="17"/>
                                <line x1="14" y1="11" x2="14" y2="17"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Food Modal -->
    <div id="foodModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Add Food Item</h3>
                <button onclick="closeFoodModal()" class="text-gray-400 hover:text-white">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form id="addFoodForm" class="space-y-4">
                <div>
                    <label class="block text-gray-300 font-medium mb-2">Food Name</label>
                    <input type="text" id="foodName" placeholder="e.g., Chicken Breast" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-300 font-medium mb-2">Calories</label>
                        <input type="number" id="foodCalories" placeholder="250" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-gray-300 font-medium mb-2">Protein (g)</label>
                        <input type="number" id="foodProtein" placeholder="30" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-300 font-medium mb-2">Carbs (g)</label>
                        <input type="number" id="foodCarbs" placeholder="15" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-gray-300 font-medium mb-2">Fats (g)</label>
                        <input type="number" id="foodFats" placeholder="8" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closeFoodModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold py-3 px-4 rounded-lg transition-all">
                        Add Food
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Nutrition Calculator functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize nutrition calculator
            initializeNutritionCalculator();
            
            // Set up event listeners
            setupEventListeners();
            
            // Update navigation
            updateNavigation();
        });

        function initializeNutritionCalculator() {
            // Calculate initial values
            calculateNutritionRequirements();
            
            // Load saved food log from database
            loadFoodLog();
            
            // Update progress bars
            updateMacroProgress();
        }
        function setupEventListeners() {
            // User info form
            const submitInfoBtn = document.getElementById('submitInfoBtn');
            if (submitInfoBtn) {
                submitInfoBtn.addEventListener('click', function() {
                    saveNutritionData();
                    calculateNutritionRequirements();
                    updateMacroProgress();
                });
                
                // Real-time updates when inputs change
                const inputs = document.querySelectorAll('#userInfoForm input, #userInfoForm select');
                inputs.forEach(input => {
                    input.addEventListener('change', function() {
                        calculateNutritionRequirements();
                        updateMacroProgress();
                    });
                });
            }
            
            // Add food form
            const addFoodForm = document.getElementById('addFoodForm');
            if (addFoodForm) {
                addFoodForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    addFoodItem();
                });
            }
        }

        function saveNutritionData() {
            const age = parseInt(document.getElementById('age').value) || 25;
            const gender = document.getElementById('gender').value;
            const weight = parseFloat(document.getElementById('weight').value) || 70;
            const height = parseInt(document.getElementById('height').value) || 175;
            const activityLevel = document.getElementById('activityLevel').value;
            const goal = document.getElementById('goal').value;
            const formMessage = document.getElementById('form-message');

            // Client-side validation
            if (age < 15 || age > 100 || weight < 30 || weight > 200 || height < 100 || height > 250) {
                formMessage.textContent = 'Please enter valid values for age (15-100), weight (30-200 kg), and height (100-250 cm).';
                formMessage.classList.add('text-red-500');
                return;
            }

            // Send data to PHP script via AJAX
            fetch('save_nutrition.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    age: age,
                    gender: gender,
                    weight: weight,
                    height: height,
                    activity_level: activityLevel,
                    goal: goal
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    formMessage.textContent = 'Nutrition profile saved successfully!';
                    formMessage.classList.remove('text-red-500');
                    formMessage.classList.add('text-green-500');
                } else {
                    formMessage.textContent = data.message || 'An error occurred. Please try again.';
                    formMessage.classList.add('text-red-500');
                }
            })
            .catch(error => {
                formMessage.textContent = 'An error occurred. Please try again.';
                formMessage.classList.add('text-red-500');
                console.error('Error:', error);
            });
        }

        function calculateNutritionRequirements() {
            const age = parseInt(document.getElementById('age').value) || 25;
            const weight = parseFloat(document.getElementById('weight').value) || 70;
            const height = parseInt(document.getElementById('height').value) || 175;
            const gender = document.getElementById('gender').value;
            const activityLevel = document.getElementById('activityLevel').value;
            const goal = document.getElementById('goal').value;
            
            // Calculate BMR using Mifflin-St Jeor equation
            let bmr;
            if (gender === 'male') {
                bmr = 88.362 + (13.397 * weight) + (4.799 * height) - (5.677 * age);
            } else {
                bmr = 447.593 + (9.247 * weight) + (3.098 * height) - (4.330 * age);
            }
            
            // Calculate TDEE
            const activityMultipliers = {
                sedentary: 1.2,
                light: 1.375,
                moderate: 1.55,
                active: 1.725,
                veryActive: 1.9
            };
            
            const tdee = bmr * activityMultipliers[activityLevel];
            
            // Adjust for goal
            let targetCalories;
            switch (goal) {
                case 'lose':
                    targetCalories = tdee - 500;
                    break;
                case 'gain':
                    targetCalories = tdee + 500;
                    break;
                default:
                    targetCalories = tdee;
            }
            
            // Calculate macros (30% protein, 40% carbs, 30% fats)
            const targetProtein = Math.round((targetCalories * 0.3) / 4);
            const targetCarbs = Math.round((targetCalories * 0.4) / 4);
            const targetFats = Math.round((targetCalories * 0.3) / 9);
            
            // Update display
            document.getElementById('bmrValue').textContent = Math.round(bmr) + ' cal';
            document.getElementById('tdeeValue').textContent = Math.round(tdee) + ' cal';
            document.getElementById('targetCalories').textContent = Math.round(targetCalories);
            document.getElementById('targetProtein').textContent = targetProtein + 'g';
            document.getElementById('targetCarbs').textContent = targetCarbs + 'g';
            document.getElementById('targetFats').textContent = targetFats + 'g';
            
            // Store targets for progress calculation
            window.nutritionTargets = {
                calories: Math.round(targetCalories),
                protein: targetProtein,
                carbs: targetCarbs,
                fats: targetFats
            };
        }

        function loadFoodLog() {
            // Load from localStorage or use default data
            const savedFoodLog = localStorage.getItem('foodLog');
            let foodLog;
            
            if (savedFoodLog) {
                foodLog = JSON.parse(savedFoodLog);
            } else {
                foodLog = [
                    { name: 'Breakfast', calories: 420, protein: 25, carbs: 45, fats: 18 },
                    { name: 'Lunch', calories: 550, protein: 35, carbs: 60, fats: 22 }
                ];
            }
            
            window.foodLog = foodLog;
            updateFoodLogDisplay();
        }

        function updateFoodLogDisplay() {
            const foodLogList = document.getElementById('foodLogList');
            if (!foodLogList) return;
            
            foodLogList.innerHTML = '';
            
            window.foodLog.forEach((food, index) => {
                const foodElement = document.createElement('div');
                foodElement.className = 'flex items-center justify-between p-4 bg-gray-700/30 rounded-lg';
                
                foodElement.innerHTML = `
                    <div>
                        <h4 class="text-white font-medium">${food.name}</h4>
                        <p class="text-gray-400 text-sm">${food.calories} cal • ${food.protein}g protein • ${food.carbs}g carbs • ${food.fats}g fat</p>
                    </div>
                    <button onclick="removeFoodItem(${index})" class="text-red-400 hover:text-red-300 transition-colors">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                    </button>
                `;
                
                foodLogList.appendChild(foodElement);
            });
        }

        function updateMacroProgress() {
            if (!window.foodLog || !window.nutritionTargets) return;
            
            // Calculate consumed totals
            const consumed = window.foodLog.reduce((totals, food) => {
                totals.calories += food.calories;
                totals.protein += food.protein;
                totals.carbs += food.carbs;
                totals.fats += food.fats;
                return totals;
            }, { calories: 0, protein: 0, carbs: 0, fats: 0 });
            
            const targets = window.nutritionTargets;
            
            // Update consumed values
            document.getElementById('consumedCalories').textContent = consumed.calories;
            document.getElementById('consumedProtein').textContent = consumed.protein + 'g';
            document.getElementById('consumedCarbs').textContent = consumed.carbs + 'g';
            document.getElementById('consumedFats').textContent = consumed.fats + 'g';
            
            // Update remaining values
            document.getElementById('remainingCalories').textContent = Math.max(0, targets.calories - consumed.calories);
            document.getElementById('remainingProtein').textContent = Math.max(0, targets.protein - consumed.protein) + 'g';
            document.getElementById('remainingCarbs').textContent = Math.max(0, targets.carbs - consumed.carbs) + 'g';
            document.getElementById('remainingFats').textContent = Math.max(0, targets.fats - consumed.fats) + 'g';
            
            // Update progress bars and percentages
            const macros = [
                { name: 'calories', consumed: consumed.calories, target: targets.calories },
                { name: 'protein', consumed: consumed.protein, target: targets.protein },
                { name: 'carbs', consumed: consumed.carbs, target: targets.carbs },
                { name: 'fats', consumed: consumed.fats, target: targets.fats }
            ];
            
            macros.forEach(macro => {
                const percentage = Math.min((macro.consumed / macro.target) * 100, 100);
                const progressBar = document.getElementById(macro.name + 'Progress');
                const percentageElement = document.getElementById(macro.name + 'Percentage');
                
                if (progressBar) {
                    progressBar.style.width = percentage + '%';
                }
                
                if (percentageElement) {
                    percentageElement.textContent = Math.round(percentage) + '%';
                }
            });
        }

        function openFoodModal() {
            const modal = document.getElementById('foodModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeFoodModal() {
            const modal = document.getElementById('foodModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                
                // Clear form
                const form = document.getElementById('addFoodForm');
                if (form) {
                    form.reset();
                }
            }
        }

        function addFoodItem() {
            const name = document.getElementById('foodName').value;
            const calories = parseInt(document.getElementById('foodCalories').value) || 0;
            const protein = parseInt(document.getElementById('foodProtein').value) || 0;
            const carbs = parseInt(document.getElementById('foodCarbs').value) || 0;
            const fats = parseInt(document.getElementById('foodFats').value) || 0;

            if (!name) {
                alert('Please enter a food name');
                return;
            }

            const newFood = { name, calories, protein, carbs, fats };
            window.foodLog.push(newFood);

            // Save to database
            saveFoodLog();

            // Update display
            updateFoodLogDisplay();
            updateMacroProgress();

            // Close modal
            closeFoodModal();

            // Show success message
            showNotification('Food item added successfully!', 'success');
        }

function removeFoodItem(index) {
    if (confirm('Are you sure you want to remove this food item?')) {
        window.foodLog.splice(index, 1);

        // Save to database
        saveFoodLog();

        // Update display
        updateFoodLogDisplay();
        updateMacroProgress();

        showNotification('Food item removed', 'info');
    }
}

            function removeFoodItem(index) {
                if (confirm('Are you sure you want to remove this food item?')) {
                    window.foodLog.splice(index, 1);

                    // Save to localStorage
                    localStorage.setItem('foodLog', JSON.stringify(window.foodLog));

                    // Save to server
                    saveFoodLog();

                    // Update display
                    updateFoodLogDisplay();
                    updateMacroProgress();

                    showNotification('Food item removed', 'info');
                }
            }
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            } text-white font-medium`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 100);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        function updateNavigation() {
            // Update active navigation state for nutrition page
            const navLinks = document.querySelectorAll('.nav-link');
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
            
            // Remove active class from all links
            [...navLinks, ...mobileNavLinks].forEach(link => {
                link.classList.remove('active', 'bg-orange-500', 'text-white', 'text-orange-500');
            });
            
            // Add active class to nutrition links
            const nutritionLinks = document.querySelectorAll('a[href="nutrition.php"], a[href="#nutrition"]');
            nutritionLinks.forEach(link => {
                if (link.classList.contains('mobile-nav-link')) {
                    link.classList.add('active', 'text-orange-500');
                    link.classList.remove('text-gray-400');
                } else {
                    link.classList.add('active', 'bg-orange-500', 'text-white');
                    link.classList.remove('text-gray-300');
                }
            });
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('foodModal');
            if (modal && e.target === modal) {
                closeFoodModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFoodModal();
            }
        });

        // Profile dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const profileButton = document.getElementById('profileButton');
            const profileDropdown = document.getElementById('profileDropdown');

            if (profileButton && profileDropdown) {
                profileButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('hidden');
                });

                // Hide dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!profileDropdown.classList.contains('hidden')) {
                        if (!profileDropdown.contains(e.target) && e.target !== profileButton) {
                            profileDropdown.classList.add('hidden');
                        }
                    }
                });
            }
        });

        // Save food log to server
// Save food log to server
function saveFoodLog() {
    fetch('save_food_log.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ foodLog: window.foodLog })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Food log saved!', 'success');
        } else {
            showNotification('Error saving food log', 'error');
        }
    })
    .catch(error => {
        console.error('Error saving food log:', error);
        showNotification('Error saving food log', 'error');
    });
}

        // Load food log from database
        function loadFoodLog() {
            fetch('load_food_log.php')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.foodLog) {
                    window.foodLog = data.foodLog;
                } else {
                    // Use default data if no saved data
                    window.foodLog = [
                        { name: 'Breakfast', calories: 420, protein: 25, carbs: 45, fats: 18 },
                        { name: 'Lunch', calories: 550, protein: 35, carbs: 60, fats: 22 }
                    ];
                }
                updateFoodLogDisplay();
                updateMacroProgress();
            })
            .catch(error => {
                console.error('Error loading food log:', error);
                // Use default data on error
                window.foodLog = [
                    { name: 'Breakfast', calories: 420, protein: 25, carbs: 45, fats: 18 },
                    { name: 'Lunch', calories: 550, protein: 35, carbs: 60, fats: 22 }
                ];
                updateFoodLogDisplay();
                updateMacroProgress();
            });
        }
    </script>
</body>
</html>