<?php
session_start();

// Check if trainer is logged in
if (!isset($_SESSION['trainer_id'])) {
    header("Location: ");
    exit();
}

// Database configuration
$host = 'localhost';
$dbname = 'rawfit';
$username = 'root'; // Change to your actual MySQL username
$password = '';     // Change to your actual MySQL password (empty for XAMPP default)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $status = $_POST['status'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $image = $_FILES['image'] ?? null;

    // Basic validation
    $errors = [];
    if (empty($title)) $errors[] = "Course title is required.";
    if (empty($description)) $errors[] = "Description is required.";
    if (empty($category)) $errors[] = "Category is required.";
    if (empty($duration)) $errors[] = "Duration is required.";
    if (empty($status)) $errors[] = "Status is required.";
    if (empty($start_date)) $errors[] = "Start date is required.";
    if (empty($end_date)) $errors[] = "End date is required.";
    if ($image && $image['error'] == UPLOAD_ERR_NO_FILE) {
        $errors[] = "Image is required.";
    } elseif ($image && $image['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image['type'], $allowed_types)) {
            $errors[] = "Only JPEG, PNG, and GIF images are allowed.";
        }
        if ($image['size'] > 5 * 1024 * 1024) { // 5MB limit
            $errors[] = "Image size must be less than 5MB.";
        }
    }

    if (empty($errors)) {
        // Handle image upload
        $upload_dir = "uploads/";
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        $image_name = uniqid() . '_' . basename($image['name']);
        $target_file = $upload_dir . $image_name;

        if (move_uploaded_file($image['tmp_name'], $target_file)) {
            // Insert data into database
            $trainer_id = $_SESSION['trainer_id'];
            $created_at = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare("INSERT INTO trainer_courses (trainer_id, title, description, category, duration, status, start_date, end_date, image_path, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$trainer_id, $title, $description, $category, $duration, $status, $start_date, $end_date, $image_name, $created_at]);
            
            $message = "Course '$title' added successfully! Image uploaded as $image_name.";
        } else {
            $message = "Failed to upload image.";
        }
    } else {
        $message = implode(" ", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course - Rawfit</title>
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
    <nav class="fixed top-0 left-0 right-0 z-50 bg-black/90 backdrop-blur-md border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-primary to-secondary rounded-lg flex items-center justify-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-xl">Rawfit</span>
                </div>
                <div class="relative">
                    <a href="logout.php" class="text-sm text-gray-300 hover:text-white hover:bg-gray-800 px-3 py-2 rounded-lg transition-colors">
                        Sign Out
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-8">
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700 p-6">
            <h1 class="text-2xl font-bold text-white mb-6">Add New Course</h1>
            <?php if (isset($message)): ?>
                <div class="mb-4 p-3 rounded-lg <?php echo strpos($message, 'successfully') !== false ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-300">Course Title</label>
                    <input type="text" name="title" id="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                           class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
                    <textarea name="description" id="description" rows="3" 
                              class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-300">Category</label>
                    <select name="category" id="category" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">All Specialties</option>
                        <option value="strength" <?php echo (isset($_POST['category']) && $_POST['category'] === 'strength') ? 'selected' : ''; ?>>Strength Training</option>
                        <option value="cardio" <?php echo (isset($_POST['category']) && $_POST['category'] === 'cardio') ? 'selected' : ''; ?>>Cardio & HIIT</option>
                        <option value="yoga" <?php echo (isset($_POST['category']) && $_POST['category'] === 'yoga') ? 'selected' : ''; ?>>Yoga & Flexibility</option>
                        <option value="boxing" <?php echo (isset($_POST['category']) && $_POST['category'] === 'boxing') ? 'selected' : ''; ?>>Boxing</option>
                        <option value="crossfit" <?php echo (isset($_POST['category']) && $_POST['category'] === 'crossfit') ? 'selected' : ''; ?>>CrossFit</option>
                        <option value="nutrition" <?php echo (isset($_POST['category']) && $_POST['category'] === 'nutrition') ? 'selected' : ''; ?>>Nutrition</option>
                    </select>
                </div>
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-300">Duration (weeks)</label>
                    <input type="number" name="duration" id="duration" min="1" max="52" value="<?php echo isset($_POST['duration']) ? htmlspecialchars($_POST['duration']) : ''; ?>" 
                           class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-300">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Select Status</option>
                        <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        <option value="completed" <?php echo (isset($_POST['status']) && $_POST['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-300">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="<?php echo isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ''; ?>" 
                           class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-300">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="<?php echo isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : ''; ?>" 
                           class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-300">Course Image</label>
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif" 
                           class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <button type="submit" class="w-full bg-primary text-white py-3 px-4 rounded-lg font-medium hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-primary transition-all">
                    Submit Course
                </button>
            </form>
            <p class="text-xs text-gray-500 mt-4">Page generated at: 11:12 AM IST, September 12, 2025</p>
        </div>
    </div>
</body>
</html>