<?php
session_start();

// Check if trainer is logged in
if (!isset($_SESSION['trainer_id'])) {
    header("Location: login.php"); // Redirect to login page
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

// Initialize variables
$course = null;
$is_edit = false;

// Check if editing an existing course
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $is_edit = true;
    $course_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM trainer_courses WHERE id = ? AND trainer_id = ?");
    $stmt->execute([$course_id, $_SESSION['trainer_id']]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        $message = "Course not found or you don't have permission to edit it.";
    }
}

// Handle delete request
if (isset($_GET['delete']) && $_GET['delete'] == 'true' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $course_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM trainer_courses WHERE id = ? AND trainer_id = ?");
    if ($stmt->execute([$course_id, $_SESSION['trainer_id']])) {
        $message = "Course deleted successfully!";
        header("Location: courses.php"); // Redirect to a courses list page
        exit();
    } else {
        $message = "Failed to delete course.";
    }
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
    $document = $_FILES['document'] ?? null;

    // Basic validation
    $errors = [];
    if (empty($title)) $errors[] = "Course title is required.";
    if (empty($description)) $errors[] = "Description is required.";
    if (empty($category)) $errors[] = "Category is required.";
    if (empty($duration)) $errors[] = "Duration is required.";
    if (empty($status)) $errors[] = "Status is required.";
    if (empty($start_date)) $errors[] = "Start date is required.";
    if (empty($end_date)) $errors[] = "End date is required.";
    if (!$is_edit && $image && $image['error'] == UPLOAD_ERR_NO_FILE) {
        $errors[] = "Image is required for new courses.";
    } elseif ($image && $image['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image['type'], $allowed_types)) {
            $errors[] = "Only JPEG, PNG, and GIF images are allowed.";
        }
        if ($image['size'] > 5 * 1024 * 1024) { // 5MB limit
            $errors[] = "Image size must be less than 5MB.";
        }
    }

    // Document upload handling
    $doc_name = $course['doc_path'] ?? null;

    if ($document && $document['error'] == UPLOAD_ERR_OK) {
        $allowed_doc_types = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];
        if (!in_array($document['type'], $allowed_doc_types)) {
            $errors[] = "Only PDF, DOC, DOCX, TXT, PPT, and PPTX files are allowed for documents.";
        }
        if ($document['size'] > 5 * 1024 * 1024) {
            $errors[] = "Document size must be less than 5MB.";
        }
        if (empty($errors)) {
            $upload_dir = "uploads/";
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            $doc_name = uniqid() . '_' . basename($document['name']);
            $doc_target = $upload_dir . $doc_name;
            if (!move_uploaded_file($document['tmp_name'], $doc_target)) {
                $errors[] = "Failed to upload document.";
            }
        }
    }

    if (empty($errors)) {
        $trainer_id = $_SESSION['trainer_id'];
        $created_at = date('Y-m-d H:i:s');
        $image_name = $course['image_path'] ?? null;

        // Handle image upload if a new image is provided
        if ($image && $image['error'] == UPLOAD_ERR_OK) {
            $upload_dir = "uploads/"; // Create this directory in your project root
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            $image_name = uniqid() . '_' . basename($image['name']);
            $target_file = $upload_dir . $image_name;

            if (!move_uploaded_file($image['tmp_name'], $target_file)) {
                $errors[] = "Failed to upload image.";
            }
        }

        if (empty($errors)) {
            if ($is_edit) {
                // Update existing course
                $stmt = $pdo->prepare("UPDATE trainer_courses SET title = ?, description = ?, category = ?, duration = ?, status = ?, start_date = ?, end_date = ?, image_path = ?, doc_path = ? WHERE id = ? AND trainer_id = ?");
                $stmt->execute([$title, $description, $category, $duration, $status, $start_date, $end_date, $image_name, $doc_name, $course_id, $trainer_id]);
                $message = "Course '$title' updated successfully!";
            } else {
                // Insert new course
                $stmt = $pdo->prepare("INSERT INTO trainer_courses (trainer_id, title, description, category, duration, status, start_date, end_date, image_path, doc_path, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$trainer_id, $title, $description, $category, $duration, $status, $start_date, $end_date, $image_name, $doc_name, $created_at]);
                $message = "Course '$title' added successfully! Image uploaded as $image_name.";
            }
        } else {
            $message = implode(" ", $errors);
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
    <title><?php echo $is_edit ? 'Edit Course' : 'Add New Course'; ?> - Rawfit</title>
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
            <h1 class="text-2xl font-bold text-white mb-6"><?php echo $is_edit ? 'Edit Course' : 'Add New Course'; ?></h1>
            <?php if (isset($message)): ?>
                <div class="mb-4 p-3 rounded-lg <?php echo strpos($message, 'successfully') !== false ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-300">Course Title</label>
                    <input type="text" name="title" id="title" value="<?php echo $is_edit ? htmlspecialchars($course['title']) : (isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''); ?>" 
                           class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
                    <textarea name="description" id="description" rows="3" 
                              class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary"><?php echo $is_edit ? htmlspecialchars($course['description']) : (isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''); ?></textarea>
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-300">Category</label>
                    <select name="category" id="category" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">All Specialties</option>
                        <option value="strength" <?php echo ($is_edit && $course['category'] === 'strength') || (isset($_POST['category']) && $_POST['category'] === 'strength') ? 'selected' : ''; ?>>Strength Training</option>
                        <option value="cardio" <?php echo ($is_edit && $course['category'] === 'cardio') || (isset($_POST['category']) && $_POST['category'] === 'cardio') ? 'selected' : ''; ?>>Cardio & HIIT</option>
                        <option value="yoga" <?php echo ($is_edit && $course['category'] === 'yoga') || (isset($_POST['category']) && $_POST['category'] === 'yoga') ? 'selected' : ''; ?>>Yoga & Flexibility</option>
                        <option value="boxing" <?php echo ($is_edit && $course['category'] === 'boxing') || (isset($_POST['category']) && $_POST['category'] === 'boxing') ? 'selected' : ''; ?>>Boxing</option>
                        <option value="crossfit" <?php echo ($is_edit && $course['category'] === 'crossfit') || (isset($_POST['category']) && $_POST['category'] === 'crossfit') ? 'selected' : ''; ?>>CrossFit</option>
                        <option value="nutrition" <?php echo ($is_edit && $course['category'] === 'nutrition') || (isset($_POST['category']) && $_POST['category'] === 'nutrition') ? 'selected' : ''; ?>>Nutrition</option>
                    </select>
                </div>
                 <div>
                    <label for="status" class="block text-sm font-medium text-gray-300">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Select Status</option>
                        <option value="active" <?php echo ($is_edit && $course['status'] === 'active') || (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : ''; ?>>Completed</option>
                        <option value="inactive" <?php echo ($is_edit && $course['status'] === 'inactive') || (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>Available</option>
                        <option value="completed" <?php echo ($is_edit && $course['status'] === 'completed') || (isset($_POST['status']) && $_POST['status'] === 'completed') ? 'selected' : ''; ?>>Unavailable</option>
                    </select>
                </div>
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-300">Duration (weeks)</label>
                    <input type="number" name="duration" id="duration" min="1" max="52" value="<?php echo $is_edit ? htmlspecialchars($course['duration']) : (isset($_POST['duration']) ? htmlspecialchars($_POST['duration']) : ''); ?>" 
                           class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-300">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="<?php echo $is_edit ? htmlspecialchars($course['start_date']) : (isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ''); ?>" 
                           class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-300">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="<?php echo $is_edit ? htmlspecialchars($course['end_date']) : (isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : ''); ?>" 
                           class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-300">Course Image</label>
                    <?php if ($is_edit && $course['image_path']): ?>
                        <p class="text-sm text-gray-400 mb-2">Current Image: <img src="uploads/<?php echo htmlspecialchars($course['image_path']); ?>" alt="Course Image" class="h-20 inline-block"></p>
                    <?php endif; ?>
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif" 
                           class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                </div>

                <!-- Display uploaded document if exists -->
                <?php if ($is_edit && !empty($course['doc_path'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Current Document</label>
                        <a href="uploads/<?php echo htmlspecialchars($course['doc_path']); ?>" target="_blank" class="text-primary underline hover:text-secondary">
                            <?php echo htmlspecialchars(basename($course['doc_path'])); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Document Upload Section -->
                <div>
                    <label for="document" class="block text-sm font-medium text-gray-300">Replace/Upload Document (PDF, DOCX, etc.)</label>
                    <input type="file" name="document" id="document"
                           class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white file:bg-orange-500 file:text-white file:rounded-lg file:px-4 file:py-2 file:border-0 focus:outline-none focus:ring-2 focus:ring-primary transition-all"
                           accept=".pdf,.doc,.docx,.txt,.ppt,.pptx">
                    <p class="text-xs text-gray-400 mt-1">Upload to replace the current document (max 5MB).</p>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" name="save" class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-orange-600 text-center">
                        <?php echo $is_edit ? 'Update Course' : 'Save Course'; ?>
                    </button>
                    <?php if ($is_edit): ?>
                        <a href="edit_course.php?delete=true&id=<?php echo $course['id']; ?>" class="flex-1 bg-secondary text-white py-2 px-4 rounded-lg hover:bg-red-600 text-center" onclick="return confirm('Are you sure you want to delete this course?');">
                            Delete Course
                        </a>
                    <?php endif; ?>
                </div>
            </form>
            <p class="text-xs text-gray-500 mt-4">Page generated at: <?php echo date('h:i A T, F j, Y'); ?></p>
        </div>
    </div>
</body>
</html>