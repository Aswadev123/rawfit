<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RawFit - Select Trainer</title>
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
    <?php
    session_start();

    // Redirect if not logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ");
        exit();
    }

    // Database connection
    $conn = new mysqli("localhost", "root", "", "rawfit");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get logged-in user name
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

    $stmt->close();

    // Fetch trainer courses
    $trainers = [];
    $sql = "SELECT id, trainer_id, title, description, category, duration, start_date, end_date, image_path, created_at 
            FROM trainer_courses";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $trainerName = "Trainer " . $row['trainer_id'];
            $rating = rand(4, 5);
            $availability = (rand(0, 1) ? "Available" : "Unavailable");
            $price = rand(60, 85);
            $duration = $row['duration'] * 7;

            $trainers[] = [
                'id' => $row['id'], // Add this line
                'name' =>  $row['title'],
                'specialty' => $row['category'],
                'description' => $row['description'],
                'rating' => $rating,
                'availability' => $availability,
                'price' => $price,
                'duration' => $duration,
                'image' => $row['image_path'] ? "uploads/" . $row['image_path'] : "https://via.placeholder.com/400x300",
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date']
            ];
        }
    }

    $conn->close();
    ?>

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
                     <a href="display_gym.php" class="nav-link flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/>
                            <path d="M12 18h.01"/>
                        </svg>
                        <span>Gyms</span>
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

    <!-- Main Content -->
    <br><br>
    <main class="pt-20 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">
                    Select Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-500">Trainer</span>
                </h1>
                <p class="text-gray-400 text-lg">Book sessions with our certified expert trainers</p>
            </div>

            <!-- Filter Bar -->
            <div class="mb-8 bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 sm:p-6 border border-gray-700">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" id="searchTrainers" placeholder="Search trainers..." class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <select id="specialtyFilter" class="bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">All Specialties</option>
                            <option value="strength">Strength Training</option>
                            <option value="cardio">Cardio & HIIT</option>
                            <option value="yoga">Yoga & Flexibility</option>
                            <option value="boxing">Boxing</option>
                            <option value="crossfit">CrossFit</option>
                        </select>
                    </div>
                    <div>
                        <select id="ratingFilter" class="bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4+ Stars</option>
                            <option value="3">3+ Stars</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Trainers Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6" id="trainersGrid">
                <?php foreach ($trainers as $trainer): ?>
                    <div class="trainer-card bg-gray-800/50 backdrop-blur-sm rounded-xl overflow-hidden border border-gray-700 hover:bg-gray-800/70 transition-all duration-300 group" 
                         data-specialty="<?php echo strtolower($trainer['specialty']); ?>" 
                         data-rating="<?php echo $trainer['rating']; ?>">
                        <div class="relative">
                            <img src="<?php echo $trainer['image']; ?>" alt="<?php echo htmlspecialchars($trainer['name']); ?>" class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute top-4 right-4 bg-<?php echo $trainer['availability'] === "Available" ? "green" : "red"; ?>-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                <?php echo $trainer['availability']; ?>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($trainer['name']); ?></h3>
                            <p class="text-orange-400 font-medium mb-2"><?php echo htmlspecialchars($trainer['specialty']); ?></p>
                            <div class="flex items-center space-x-1 mb-3">
                                <div class="flex text-yellow-400">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-gray-400 text-sm">(Random reviews)</span>
                            </div>
                            <p class="text-gray-400 text-sm mb-4"><?php echo htmlspecialchars($trainer['description']); ?></p>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-white font-bold text-lg">$<?php echo $trainer['price']; ?>/session</span>
                                <span class="text-gray-400 text-sm"><?php echo $trainer['duration']; ?> min</span>
                            </div>
                         <a href="user_booking.php?course_id=<?php echo $trainer['id']; ?>"
                        class="w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 text-center block">
                            View Details
                        </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
         
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeTrainers();
            setupEventListeners();
            updateNavigation();
        });

        function initializeTrainers() {
            const dateInput = document.getElementById('sessionDate');
            if (dateInput) {
                const today = new Date().toISOString().split('T')[0];
                dateInput.min = today;
            }
        }

        function setupEventListeners() {
            const searchInput = document.getElementById('searchTrainers');
            if (searchInput) {
                searchInput.addEventListener('input', filterTrainers);
            }
            
            const specialtyFilter = document.getElementById('specialtyFilter');
            const ratingFilter = document.getElementById('ratingFilter');
            
            if (specialtyFilter) {
                specialtyFilter.addEventListener('change', filterTrainers);
            }
            
            if (ratingFilter) {
                ratingFilter.addEventListener('change', filterTrainers);
            }
        }

        function filterTrainers() {
            const searchTerm = document.getElementById('searchTrainers').value.toLowerCase();
            const specialtyFilter = document.getElementById('specialtyFilter').value.toLowerCase();
            const ratingFilter = document.getElementById('ratingFilter').value;
            
            const trainerCards = document.querySelectorAll('.trainer-card');
            
            trainerCards.forEach(card => {
                const trainerName = card.querySelector('h3').textContent.toLowerCase();
                const trainerSpecialty = card.querySelector('.text-orange-400').textContent.toLowerCase();
                const trainerRating = parseInt(card.dataset.rating);
                const cardSpecialty = card.dataset.specialty.toLowerCase();
                
                let showCard = true;
                
                if (searchTerm && !trainerName.includes(searchTerm) && !trainerSpecialty.includes(searchTerm)) {
                    showCard = false;
                }
                
                if (specialtyFilter && !cardSpecialty.includes(specialtyFilter)) {
                    showCard = false;
                }
                
                if (ratingFilter && trainerRating < parseInt(ratingFilter)) {
                    showCard = false;
                }
                
                card.style.display = showCard ? 'block' : 'none';
            });
            
            showNoResultsMessage();
        }

        function showNoResultsMessage() {
            const visibleCards = document.querySelectorAll('.trainer-card[style="display: block;"]');
            const trainersGrid = document.getElementById('trainersGrid');
            
            const existingMessage = document.getElementById('noResultsMessage');
            if (existingMessage) {
                existingMessage.remove();
            }
            
            if (visibleCards.length === 0) {
                const noResultsMessage = document.createElement('div');
                noResultsMessage.id = 'noResultsMessage';
                noResultsMessage.className = 'col-span-full text-center py-12';
                noResultsMessage.innerHTML = `
                    <div class="text-gray-400">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="mx-auto mb-4 opacity-50">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">No trainers found</h3>
                        <p class="text-gray-500">Try adjusting your search criteria or filters</p>
                    </div>
                `;
                trainersGrid.appendChild(noResultsMessage);
            }
        }

        function bookTrainer(name, specialty, price) {
            window.selectedTrainer = { name, specialty, price };
            
            document.getElementById('modalTrainerName').textContent = name;
            document.getElementById('modalTrainerSpecialty').textContent = specialty;
            document.getElementById('modalTrainerPrice').textContent = `$${price}/session`;
            
            const modal = document.getElementById('bookingModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                clearBookingForm();
            }
        }

        function clearBookingForm() {
            document.getElementById('sessionDate').value = '';
            document.getElementById('sessionTime').value = '';
            document.getElementById('sessionNotes').value = '';
        }

        function confirmBooking() {
            const date = document.getElementById('sessionDate').value;
            const time = document.getElementById('sessionTime').value;
            const notes = document.getElementById('sessionNotes').value;
            
            if (!date) {
                showNotification('Please select a date', 'error');
                return;
            }
            
            if (!time) {
                showNotification('Please select a time', 'error');
                return;
            }
            
            const booking = {
                trainer: window.selectedTrainer,
                date: date,
                time: time,
                notes: notes,
                bookingId: 'BK' + Date.now(),
                status: 'confirmed'
            };
            
            saveBooking(booking);
            showNotification(`Session booked with ${window.selectedTrainer.name} for ${formatDate(date)} at ${formatTime(time)}`, 'success');
            closeBookingModal();
            addToRecentActivity(`Booked session with ${window.selectedTrainer.name}`, 'just now', 'booking');
        }

        function saveBooking(booking) {
            const existingBookings = JSON.parse(localStorage.getItem('trainerBookings') || '[]');
            existingBookings.push(booking);
            localStorage.setItem('trainerBookings', JSON.stringify(existingBookings));
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        }

        function formatTime(timeString) {
            const [hours, minutes] = timeString.split(':');
            const date = new Date(2000, 0, 1, hours, minutes);
            return date.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
            });
        }

        function addToRecentActivity(activity, time, type) {
            const recentActivity = JSON.parse(localStorage.getItem('recentActivity') || '[]');
            recentActivity.unshift({ activity, time, type });
            recentActivity.splice(10);
            localStorage.setItem('recentActivity', JSON.stringify(recentActivity));
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

        function updateNavigation() {
            const navLinks = document.querySelectorAll('.nav-link');
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
            
            [...navLinks, ...mobileNavLinks].forEach(link => {
                link.classList.remove('active', 'bg-orange-500', 'text-white', 'text-orange-500');
            });
            
            const trainerLinks = document.querySelectorAll('a[href="trainers.html"], a[href="#trainers"]');
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

        document.addEventListener('click', function(e) {
            const modal = document.getElementById('bookingModal');
            if (modal && e.target === modal) {
                closeBookingModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBookingModal();
            }
        });

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