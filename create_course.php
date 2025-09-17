<?php
session_start();

// Handle course creation
$message = '';
$messageType = '';

if ($_POST && isset($_POST['course_title'])) {
    $title = trim($_POST['course_title']);
    $description = trim($_POST['course_description']);
    $category = $_POST['course_category'];
    $duration = $_POST['course_duration'];
    $difficulty = $_POST['difficulty'];
    $specialization = $_POST['specialization'];
    $rating = $_POST['rating'];
    $reviews = $_POST['reviews'];
    $experience = $_POST['experience'];
    $bio = $_POST['bio'];
    $certifications = $_POST['certifications'];
    $price = $_POST['price'];
    $session_duration = $_POST['duration'];
    $status = $_POST['status'];

    if (empty($title) || empty($description)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } else {
        // In real app, save to database
        $message = 'Course "' . htmlspecialchars($title) . '" created successfully!';
        $messageType = 'success';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Course</title>
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
                        primary: '#F97316',
                        secondary: '#EF4444',
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
                <a href="trainerman.php" class="bg-gradient-to-r from-primary to-secondary text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 hover:from-orange-600 hover:to-red-600">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-8">
        <h2 class="text-2xl font-bold text-white mb-6">Create New Course</h2>
        <?php if ($message): ?>
        <div id="message-alert" class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-500/20 text-green-300 border border-green-500/50' : 'bg-red-500/20 text-red-300 border border-red-500/50'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <form id="course-form" method="POST" class="p-6 space-y-6 bg-gray-800/50 rounded-xl border border-gray-700">
            <!-- Course Title -->
            <div>
                <label for="course_title" class="block text-sm font-medium text-gray-300 mb-2">Course Title *</label>
                <input type="text" id="course_title" name="course_title" required
                       class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition-all"
                       placeholder="Enter course title">
            </div>
            <!-- Course Description -->
            <div>
                <label for="course_description" class="block text-sm font-medium text-gray-300 mb-2">Description *</label>
                <textarea id="course_description" name="course_description" rows="4" required
                          class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition-all resize-none"
                          placeholder="Describe what students will learn in this course"></textarea>
            </div>
            <!-- Category and Duration Row -->
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="course_category" class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                    <select id="course_category" name="course_category"
                            class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition-all">
                        <option value="">Select category</option>
                        <option value="programming">Programming</option>
                        <option value="design">Design</option>
                        <option value="marketing">Marketing</option>
                        <option value="business">Business</option>
                        <option value="data-science">Data Science</option>
                    </select>
                </div>
                <div>
                    <label for="course_duration" class="block text-sm font-medium text-gray-300 mb-2">Duration (hours)</label>
                    <input type="number" id="course_duration" name="course_duration" min="1" max="200"
                           class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition-all"
                           placeholder="e.g., 40">
                </div>
            </div>
            <!-- Difficulty Level -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-3">Difficulty Level</label>
                <div class="flex space-x-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="difficulty" value="beginner" class="sr-only">
                        <span class="difficulty-option px-4 py-2 rounded-lg border border-gray-600 text-gray-300 hover:bg-green-500/20 hover:border-green-500 transition-all">
                            Beginner
                        </span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="difficulty" value="intermediate" class="sr-only">
                        <span class="difficulty-option px-4 py-2 rounded-lg border border-gray-600 text-gray-300 hover:bg-yellow-500/20 hover:border-yellow-500 transition-all">
                            Intermediate
                        </span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="difficulty" value="advanced" class="sr-only">
                        <span class="difficulty-option px-4 py-2 rounded-lg border border-gray-600 text-gray-300 hover:bg-red-500/20 hover:border-red-500 transition-all">
                            Advanced
                        </span>
                    </label>
                </div>
            </div>
            <!-- Specialization -->
            <div>
                <label for="specialization" class="block text-sm font-medium text-gray-300 mb-2">Specialization *</label>
                <input type="text" id="specialization" name="specialization" required
                       class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition-all"
                       placeholder="Enter specialization">
            </div>
            <!-- Rating -->
            <div>
                <label for="rating" class="block text-sm font-medium text-gray-300 mb-2">Rating</label>
                <input type="number" id="rating" name="rating" step="0.1" min="0" max="5"
                       class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition-all"
                       placeholder="e.g., 4.5">
            </div>
            <!-- Number of Reviews -->
            <div>
                <label for="reviews" class="block text-sm font-medium text-gray-300 mb-2">Number of Reviews</label>
                <input type="number" id="reviews" name="reviews" min="0"
                       class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition-all"
                       placeholder="e.g., 25">
            </div>
            <!-- Experience -->
            <div>
                <label for="experience" class="block text-sm font-medium text-gray-300 mb-2">Experience (years)</label>
                <input type="number" id="experience" name="experience" min="0"
                       class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition-all"
                       placeholder="e.g., 5">
            </div>
            <!-- Bio/Description -->
            <div>
                <label for="bio" class="block text-sm font-medium text-gray-300 mb-2">Bio/Description</label>
                <textarea id="bio" name="bio" rows="3"
                          class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition-all resize-none"
                          placeholder="Tell something about yourself"></textarea>
            </div>
            <!-- Certifications -->
            <div>
                <label for="certifications" class="block text-sm font-medium text-gray-300 mb-2">Certifications</label>
                <input type="text" id="certifications" name="certifications"
                       class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition-all"
                       placeholder="e.g., AWS Certified, PMP">
            </div>
            <!-- Price and Duration Row -->
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Session Price ($)</label>
                    <input type="number" id="price" name="price" min="0"
                           class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition-all"
                           placeholder="e.g., 100">
                </div>
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-300 mb-2">Session Duration (min)</label>
                    <input type="number" id="duration" name="duration" min="1"
                           class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition-all"
                           placeholder="e.g., 60">
                </div>
            </div>
            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Course Status</label>
                <select id="status" name="status"
                        class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary transition-all">
                    <option value="available">Available</option>
                    <option value="busy">Busy</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>
            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-primary to-secondary text-white py-3 px-6 rounded-lg font-medium hover:from-orange-600 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-primary transition-all transform hover:scale-[1.02]">
                    Create Course
                </button>
            </div>
        </form>
    </div>
    <script>
        // Difficulty level selection
        document.addEventListener('DOMContentLoaded', function() {
            const difficultyOptions = document.querySelectorAll('.difficulty-option');
            difficultyOptions.forEach(option => {
                option.addEventListener('click', function() {
                    difficultyOptions.forEach(opt => {
                        opt.classList.remove('border-primary', 'bg-primary/20', 'text-primary');
                        opt.classList.add('border-gray-600', 'text-gray-300');
                    });
                    this.classList.remove('border-gray-600', 'text-gray-300');
                    this.classList.add('border-primary', 'bg-primary/20', 'text-primary');
                    const radioInput = this.parentElement.querySelector('input[type="radio"]');
                    radioInput.checked = true;
                });
            });
        });
    </script>
</body>
</html>