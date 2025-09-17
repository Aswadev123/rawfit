<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RawFit - All Courses</title>
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
    <?php
    session_start();

    // Redirect if not logged in or not a trainer
    if (!isset($_SESSION['trainer_id'])) {
        header("Location: login.php");
        exit();
    }

    // Database connection
    $conn = new mysqli("localhost", "root", "", "rawfit");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle form submission for editing
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_course'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        $duration = $_POST['duration'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $image = $_FILES['image'];

        // Handle image upload if new image is provided
        $image_path = null;
        if ($image && $image['error'] == UPLOAD_ERR_OK) {
            $upload_dir = "uploads/";
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            $image_name = uniqid() . '_' . basename($image['name']);
            $target_file = $upload_dir . $image_name;
            if (move_uploaded_file($image['tmp_name'], $target_file)) {
                $image_path = $image_name;
            }
        }

        // Update course in database
        $stmt = $conn->prepare("UPDATE trainer_courses SET title = ?, description = ?, category = ?, duration = ?, start_date = ?, end_date = ?, image_path = ? WHERE id = ? AND trainer_id = ?");
        $stmt->bind_param("sssissssi", $title, $description, $category, $duration, $start_date, $end_date, $image_path, $id, $_SESSION['trainer_id']);
        $stmt->execute();
        $stmt->close();

        // Redirect to refresh the page
        header("Location: all_courses.php");
        exit();
    }

    // Handle delete action
    if (isset($_GET['delete']) && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM trainer_courses WHERE id = ? AND trainer_id = ?");
        $stmt->bind_param("ii", $id, $_SESSION['trainer_id']);
        $stmt->execute();
        $stmt->close();
        header("Location: all_courses.php");
        exit();
    }

    // Fetch all courses
    $courses = [];
    $sql = "SELECT id, trainer_id, title, description, category, duration, start_date, end_date, image_path, created_at 
            FROM trainer_courses WHERE trainer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['trainer_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
    }

    $stmt->close();
    $conn->close();
    ?>

    <!-- Navigation -->
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

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-8">
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700 p-6">
            <h1 class="text-2xl font-bold text-white mb-6">All Courses</h1>
            <br>
            <?php if (isset($_GET['edit']) && isset($_GET['id'])) {
                $edit_id = $_GET['id'];
                $edit_course = null;
                foreach ($courses as $course) {
                    if ($course['id'] == $edit_id) {
                        $edit_course = $course;
                        break;
                    }
                }
                if ($edit_course): ?>
                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_course['id']); ?>">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-300">Course Title</label>
                            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($edit_course['title']); ?>" 
                                   class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary"><?php echo htmlspecialchars($edit_course['description']); ?></textarea>
                        </div>
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-300">Category</label>
                            <select name="category" id="category" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="strength" <?php echo $edit_course['category'] === 'strength' ? 'selected' : ''; ?>>Strength Training</option>
                                <option value="cardio" <?php echo $edit_course['category'] === 'cardio' ? 'selected' : ''; ?>>Cardio & HIIT</option>
                                <option value="yoga" <?php echo $edit_course['category'] === 'yoga' ? 'selected' : ''; ?>>Yoga & Flexibility</option>
                                <option value="boxing" <?php echo $edit_course['category'] === 'boxing' ? 'selected' : ''; ?>>Boxing</option>
                                <option value="crossfit" <?php echo $edit_course['category'] === 'crossfit' ? 'selected' : ''; ?>>CrossFit</option>
                                <option value="nutrition" <?php echo $edit_course['category'] === 'nutrition' ? 'selected' : ''; ?>>Nutrition</option>
                            </select>
                        </div>
                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-300">Duration (weeks)</label>
                            <input type="number" name="duration" id="duration" min="1" max="52" value="<?php echo htmlspecialchars($edit_course['duration']); ?>" 
                                   class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-300">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($edit_course['start_date']); ?>" 
                                   class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-300">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($edit_course['end_date']); ?>" 
                                   class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-300">Course Image (optional)</label>
                            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif" 
                                   class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            <?php if ($edit_course['image_path']): ?>
                                <img src="uploads/<?php echo htmlspecialchars($edit_course['image_path']); ?>" alt="Current Image" class="mt-2 w-32 h-32 object-cover rounded">
                            <?php endif; ?>
                        </div>
                        <button type="submit" name="edit_course" class="w-full bg-primary text-white py-3 px-4 rounded-lg font-medium hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-primary transition-all">
                            Save Changes
                        </button>
                        <a href="all_courses.php" class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-gray-700 text-center block mt-2">
                            Cancel
                        </a>
                    </form>
                <?php else: ?>
                    <p class="text-gray-400 text-center py-6">Course not found.</p>
                <?php endif;
            } else { ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($courses as $course): ?>
                        <div class="bg-gray-700/50 rounded-lg p-4 border border-gray-600">
                            <img src="<?php echo $course['image_path'] ? "uploads/" . htmlspecialchars($course['image_path']) : "https://via.placeholder.com/300x200"; ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" class="w-full h-40 object-cover rounded-t-lg">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-white"><?php echo htmlspecialchars($course['title']); ?></h3>
                                <p class="text-gray-400 text-sm mb-2">Category: <?php echo htmlspecialchars($course['category']); ?></p>
                                <p class="text-gray-400 text-sm mb-2">Duration: <?php echo htmlspecialchars($course['duration']); ?> weeks</p>
                                <p class="text-gray-400 text-sm mb-2">Start: <?php echo htmlspecialchars($course['start_date']); ?></p>
                                <p class="text-gray-400 text-sm mb-2">End: <?php echo htmlspecialchars($course['end_date']); ?></p>
                                <p class="text-gray-400 text-sm mb-4">Created: <?php echo htmlspecialchars($course['created_at']); ?></p>
                                <div class="flex space-x-2">
                                    <a href="edit_course.php?edit=true&id=<?php echo $course['id']; ?>" class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-orange-600 text-center">
                                        Edit
                                    </a>
                                    <a href="edit_course.php?delete=true&id=<?php echo $course['id']; ?>" class="flex-1 bg-secondary text-white py-2 px-4 rounded-lg hover:bg-red-600 text-center" onclick="return confirm('Are you sure you want to delete this course?');">
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (empty($courses)): ?>
                    <p class="text-gray-400 text-center py-6">No courses found.</p>
                <?php endif; ?>
            <?php } ?>
        </div>
    </div>
</body>
</html>