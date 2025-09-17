<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RawFit - Add Trainer Course</title>
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
                    <a href="nutrition.php" class="nav-link flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/>
                            <path d="M12 18h.01"/>
                        </svg>
                        <span>Nutrition</span>
                    </a>
                    <a href="trainer.php" class="nav-link active flex items-center space-x-2 px-3 py-2 rounded-lg bg-orange-500 text-white transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span>Trainers</span>
                    </a>
                </div>
            
                <!-- User Info -->
                <div class="flex items-center space-x-4">
                    <div class="hidden sm:block text-right">
                        <p class="text-white font-medium">John Doe</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
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

    <!-- Main Content -->
     
    <main class="pt-20 min-h-screen p-4 sm:p-6 lg:p-8">
       <br><br> <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">
                    Add New <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-500">Trainer Course</span>
                </h1>
                <p class="text-gray-400 text-lg">Create a new trainer profile to offer your services</p>
            </div>

            <!-- Add Trainer Form -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 sm:p-8 border border-gray-700">
                <form id="addTrainerForm" class="space-y-6">
                    <div>
                        <label class="block text-gray-300 font-medium mb-2" for="trainerName">Trainer Name</label>
                        <input type="text" id="trainerName" placeholder="Enter your full name" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-gray-300 font-medium mb-2" for="specialty">Specialty</label>
                        <select id="specialty" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Select Specialty</option>
                            <option value="strength">Strength Training</option>
                            <option value="cardio">Cardio & HIIT</option>
                            <option value="yoga">Yoga & Flexibility</option>
                            <option value="boxing">Boxing</option>
                            <option value="crossfit">CrossFit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-300 font-medium mb-2" for="description">Description</label>
                        <textarea id="description" rows="4" placeholder="Describe your expertise and experience" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-gray-300 font-medium mb-2" for="price">Price per Session ($)</label>
                            <input type="number" id="price" placeholder="e.g., 75" min="0" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-gray-300 font-medium mb-2" for="duration">Session Duration (minutes)</label>
                            <input type="number" id="duration" placeholder="e.g., 60" min="0" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-300 font-medium mb-2" for="imageUrl">Profile Image URL</label>
                        <input type="url" id="imageUrl" placeholder="Enter image URL (e.g., https://example.com/image.jpg)" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-gray-300 font-medium mb-2" for="availability">Availability</label>
                        <select id="availability" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="available">Available</option>
                            <option value="busy">Busy</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                    <div class="flex space-x-4 pt-4">
                        <button type="button" onclick="clearForm()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors">Clear Form</button>
                        <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold py-3 px-4 rounded-lg transition-all">Add Trainer</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            setupFormListener();
            updateNavigation();
        });

        function setupFormListener() {
            const form = document.getElementById('addTrainerForm');
            if (form) {
                form.addEventListener('submit', handleFormSubmit);
            }
        }

        function handleFormSubmit(e) {
            e.preventDefault();

            // Get form values
            const trainerName = document.getElementById('trainerName').value.trim();
            const specialty = document.getElementById('specialty').value;
            const description = document.getElementById('description').value.trim();
            const price = parseFloat(document.getElementById('price').value);
            const duration = parseInt(document.getElementById('duration').value);
            const imageUrl = document.getElementById('imageUrl').value.trim();
            const availability = document.getElementById('availability').value;
            const rating = parseInt(document.getElementById('rating').value);

            // Validate inputs
            if (!trainerName) {
                showNotification('Please enter a trainer name', 'error');
                return;
            }
            if (!specialty) {
                showNotification('Please select a specialty', 'error');
                return;
            }
            if (!description) {
                showNotification('Please provide a description', 'error');
                return;
            }
            if (isNaN(price) || price <= 0) {
                showNotification('Please enter a valid price', 'error');
                return;
            }
            if (isNaN(duration) || duration <= 0) {
                showNotification('Please enter a valid session duration', 'error');
                return;
            }
            if (!imageUrl || !isValidUrl(imageUrl)) {
                showNotification('Please enter a valid image URL', 'error');
                return;
            }

            // Create trainer object
            const trainer = {
                id: 'TR' + Date.now(),
                name: trainerName,
                specialty: specialty,
                description: description,
                price: price,
                duration: duration,
                imageUrl: imageUrl,
                availability: availability,
                rating: rating,
                reviews: Math.floor(Math.random() * 50) + 10 // Random review count for simulation
            };

            // Save trainer to localStorage
            saveTrainer(trainer);

            // Show success message
            showNotification(`Trainer ${trainerName} added successfully`, 'success');

            // Clear form
            clearForm();

            // Add to recent activity
            addToRecentActivity(`Added trainer ${trainerName}`, 'just now', 'trainer');
        }

        function saveTrainer(trainer) {
            // Get existing trainers
            const existingTrainers = JSON.parse(localStorage.getItem('trainers') || '[]');
            // Add new trainer
            existingTrainers.push(trainer);
            // Save back to localStorage
            localStorage.setItem('trainers', JSON.stringify(existingTrainers));
        }

        function clearForm() {
            document.getElementById('addTrainerForm').reset();
        }

        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            } text-white font-medium max-w-sm`;
            notification.textContent = message;

            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 100);

            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 4000);
        }

        function addToRecentActivity(activity, time, type) {
            const recentActivity = JSON.parse(localStorage.getItem('recentActivity') || '[]');
            recentActivity.unshift({ activity, time, type });
            recentActivity.splice(10);
            localStorage.setItem('recentActivity', JSON.stringify(recentActivity));
        }

        function updateNavigation() {
            const navLinks = document.querySelectorAll('.nav-link');
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

            [...navLinks, ...mobileNavLinks].forEach(link => {
                link.classList.remove('active', 'bg-orange-500', 'text-white', 'text-orange-500');
            });

            const trainerLinks = document.querySelectorAll('a[href="trainer.php"], a[href="#trainers"]');
            trainerLinks.forEach(link => {
                if (link.classList.contains('mobile-nav-link')) {
                    link.classList.add('active', 'text-orange-500');
                    link.classList.remove('text-gray-400');
                } else {
                    link.classList.add('active', 'bg-orange-500', 'text-white');
                    link.classList.remove('text-gray-300');
                }
            });
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>