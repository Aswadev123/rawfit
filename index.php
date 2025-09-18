<?php
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$courses = [];
$sql = "SELECT id, trainer_id, title, description, category, duration, status, start_date, end_date, image_path, created_at FROM trainer_courses ORDER BY created_at DESC LIMIT 3";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RawFit</title>
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
                    <a href="#home" class="nav-link active flex items-center space-x-2 px-3 py-2 rounded-lg bg-orange-500 text-white transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9,22 9,12 15,12 15,22"/>
                        </svg>
                        <span>Home</span>
                    </a>
                    <a href="#about" class="nav-link flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7v10c0 5.55 3.84 9.74 9 11 5.16-1.26 9-5.45 9-11V7l-10-5z"/>
                        </svg>
                        <span>About</span>
                    </a>
                    <a href="#programs" class="nav-link flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22,4 12,14.01 9,11.01"/>
                        </svg>
                        <span>Programs</span>
                    </a>
                  
                    <a href="#trainers" class="nav-link flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span>Trainers</span>
                    </a>
                    <a href="#contact" class="nav-link flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <span>Contact</span>
                    </a>
                </div>

                <!-- Navigation Actions -->
        <div class="flex items-center space-x-4">
    <a href="auth.php" class="text-gray-300 hover:text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors">Login</a>
    <a href="trainerlogin.php" class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all">Trainer Login</a>
    <a href="adminlogin.php" class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all">Admin Login</a>
    <a href="login_owner.php" class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all">Gym Owner</a>

    <div class="md:hidden hamburger flex flex-col space-y-1 cursor-pointer" id="hamburger">
        <span class="w-6 h-0.5 bg-gray-300"></span>
        <span class="w-6 h-0.5 bg-gray-300"></span>
        <span class="w-6 h-0.5 bg-gray-300"></span>
    </div>
</div>


            <!-- Mobile Navigation -->
            <div class="md:hidden flex items-center justify-around py-3 border-t border-gray-800 hidden" id="mobile-nav">
                <a href="#home" class="mobile-nav-link active flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-orange-500">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9,22 9,12 15,12 15,22"/>
                    </svg>
                    <span class="text-xs">Home</span>
                </a>
                <a href="#about" class="mobile-nav-link flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7v10c0 5.55 3.84 9.74 9 11 5.16-1.26 9-5.45 9-11V7l-10-5z"/>
                    </svg>
                    <span class="text-xs">About</span>
                </a>
                <a href="#programs" class="mobile-nav-link flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22,4 12,14.01 9,11.01"/>
                    </svg>
                    <span class="text-xs">Programs</span>
                </a>
                <a href="calculator.php" class="mobile-nav-link flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/>
                        <path d="M12 18h.01"/>
                    </svg>
                    <span class="text-xs">Calculator</span>
                </a>
                <a href="#trainers" class="mobile-nav-link flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span class="text-xs">Trainers</span>
                </a>
                <a href="#contact" class="mobile-nav-link flex flex-col items-center space-y-1 px-3 py-2 rounded-lg text-gray-400">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <span class="text-xs">Contact</span>
                </a>
            </div>
        </div>
    </nav>
        <br><br><br>
    <!-- Hero Section -->
  <section id="home" class="pt-20 min-h-screen bg-gray-900 relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('https://images.pexels.com/photos/1552242/pexels-photo-1552242.jpeg?auto=compress&cs=tinysrgb&w=1260')] bg-cover bg-center bg-no-repeat opacity-30"></div>
    <div class="absolute inset-0 bg-gradient-to-br from-gray-900/90 via-gray-900/70 to-gray-800/90"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-orange-500/20 to-red-500/20"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative py-16">
        <div class="flex flex-col items-center text-center">
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-white mb-6 tracking-tight animate-fade-in-down">
                Transform Your Body,
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-500 drop-shadow-lg">Transform Your Life</span>
            </h1>
            <p class="text-gray-300 text-lg sm:text-xl lg:text-2xl max-w-3xl mb-10 leading-relaxed animate-fade-in-up">
                Join RawFit to unlock your strongest self. With cutting-edge facilities, expert trainers, and a vibrant community, your fitness goals are within reach.
            </p>
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 animate-fade-in">
                <a href="auth.php?action=register" class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-8 py-4 rounded-lg font-semibold flex items-center space-x-2 hover:from-orange-600 hover:to-red-600 hover:scale-105 transition-all duration-300 shadow-lg">
                    <span>Start Your Journey</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="#programs" class="bg-transparent border-2 border-orange-500 text-orange-500 px-8 py-4 rounded-lg font-semibold hover:bg-orange-500/10 hover:text-orange-400 hover:scale-105 transition-all duration-300">
                    Explore Programs
                </a>
            </div>
            <div class="mt-12 flex justify-center space-x-4 animate-pulse">
                <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                <div class="w-2 h-2 rounded-full bg-orange-500"></div>
            </div>
        </div>
    </div>
    <style>
        @keyframes fade-in-down {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down {
            animation: fade-in-down 0.8s ease-out;
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out 0.2s both;
        }
        .animate-fade-in {
            animation: fade-in-up 0.8s ease-out 0.4s both;
        }
    </style>
</section>

    <!-- About Section -->
    <section id="about" class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-4">Why Choose RawFit?</h2>
                    <p class="text-gray-400 text-lg mb-6">
                       we're your fitness family. Our mission is to provide 
                        an inclusive, motivating digital environment where everyone can achieve their health and explore.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700 hover:bg-gray-800/70 transition-all">
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center mb-4">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white">
                                    <path d="M12 2L2 7v10c0 5.55 3.84 9.74 9 11 5.16-1.26 9-5.45 9-11V7l-10-5z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">Cardio calculator</h3>
                            <p class="text-gray-400">Find your ideal heart rate for more effective, personalized workouts.</p>
                        </div>
                        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700 hover:bg-gray-800/70 transition-all">
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center mb-4">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">Expert Trainers</h3>
                            <p class="text-gray-400">Certified professionals dedicated to your success</p>
                        </div>
                        
                        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700 hover:bg-gray-800/70 transition-all">
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 flex items-center justify-center mb-4">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                    <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2">Nutrition Guidance</h3>
                            <p class="text-gray-400">Personalized meal plans and expert advice to fuel your fitness journey</p>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <img src="https://images.pexels.com/photos/1552242/pexels-photo-1552242.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Modern gym interior" class="rounded-xl object-cover w-full h-full" />
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-900/50 to-gray-800/50 rounded-xl"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section id="programs" class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-white mb-4">Our Course</h2>
            <p class="text-gray-400 text-lg mb-8">
                Choose from our diverse range of fitness courses designed to meet your specific goals
            </p>
            <div class="grid md:grid-cols-3 gap-6">
                <?php foreach ($courses as $course): ?>
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700 hover:bg-gray-800/70 hover:scale-105 transition-all duration-300">
                        <img src="<?php echo $course['image_path'] ? 'uploads/' . htmlspecialchars($course['image_path']) : 'https://via.placeholder.com/400x300'; ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" class="rounded-lg mb-4 object-cover w-full h-48" />
                        <h3 class="text-xl font-semibold text-white mb-2"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p class="text-gray-400 mb-4"><?php echo htmlspecialchars($course['description']); ?></p>
                        <ul class="text-gray-400 text-sm mb-4 space-y-2">
                            <li class="flex items-center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2"><path d="M5 13l4 4L19 7"/></svg>Category: <?php echo htmlspecialchars($course['category']); ?></li>
                            <li class="flex items-center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2"><path d="M5 13l4 4L19 7"/></svg>Duration: <?php echo htmlspecialchars($course['duration']); ?> weeks</li>
                            <li class="flex items-center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2"><path d="M5 13l4 4L19 7"/></svg>Status: <?php echo htmlspecialchars($course['status']); ?></li>
                        </ul>
                        <button class="bg-transparent border border-orange-500 text-orange-500 px-4 py-2 rounded-lg hover:bg-orange-500 hover:text-white transition-all">Learn More</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Trainers Section -->
    <section id="trainers" class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-white mb-4">Meet Our Trainers</h2>
            <p class="text-gray-400 text-lg mb-8">
                Our certified trainers are here to guide, motivate, and help you achieve your fitness goals
            </p>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700 hover:bg-gray-800/70 hover:scale-105 transition-all duration-300">
                    <img src="https://images.pexels.com/photos/1431282/pexels-photo-1431282.jpeg?auto=compress&cs=tinysrgb&w=400" alt="Sarah Johnson" class="rounded-lg mb-4 object-cover w-full h-64" />
                    <h3 class="text-xl font-semibold text-white mb-2">Sarah Johnson</h3>
                    <p class="text-orange-500 text-sm mb-2">Strength & Conditioning</p>
                    <p class="text-gray-400 mb-4">10+ years helping clients build strength and confidence</p>
                    <div class="flex space-x-4 mb-4">
                        <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="m16 11.37-4-4-4 4"/><path d="M21 16.5l-4-4-4 4"/></svg>
                        </a>
                    </div>
                    <button class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all">Learn More</button>
                </div>
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700 hover:bg-gray-800/70 hover:scale-105 transition-all duration-300">
                    <img src="https://images.pexels.com/photos/1552106/pexels-photo-1552106.jpeg?auto=compress&cs=tinysrgb&w=400" alt="Mike Rodriguez" class="rounded-lg mb-4 object-cover w-full h-64" />
                    <h3 class="text-xl font-semibold text-white mb-2">Mike Rodriguez</h3>
                    <p class="text-orange-500 text-sm mb-2">HIIT & Cardio</p>
                    <p class="text-gray-400 mb-4">Former athlete specializing in high-intensity training</p>
                    <div class="flex space-x-4 mb-4">
                        <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="m16 11.37-4-4-4 4"/><path d="M21 16.5l-4-4-4 4"/></svg>
                        </a>
                    </div>
                    <button class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all">Learn More</button>
                </div>
            </div>
        </div>
    </section>
<!-- Contact Section -->
<section id="contact" class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <h2 class="text-3xl font-bold text-white mb-4">Get In Touch</h2>
                <p class="text-gray-400 text-lg mb-6">
                    Ready to start your fitness journey? Contact us today for a free consultation and gym tour.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 flex items-center justify-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">Location</h4>
                            <p class="text-gray-400">123 Fitness Street, Gym City, GC 12345</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 flex items-center justify-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">Phone</h4>
                            <p class="text-gray-400">(555) 123-4567</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 flex items-center justify-center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-white">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">Email</h4>
                            <p class="text-gray-400">info@fitcoregym.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
                <form method="POST" action="contact.php" class="space-y-4">
                    <div>
                        <input type="text" id="name" name="name" placeholder="Your Name" required class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <input type="email" id="email" name="email" placeholder="Your Email" required class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <input type="tel" id="phone" name="phone" placeholder="Your Phone" class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <textarea id="message" name="message" placeholder="Your Message" rows="5" required class="w-full bg-gray-700/50 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

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
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="text-gray-400 space-y-2">
                        <li><a href="#home" class="hover:text-orange-500 transition-colors">Home</a></li>
                        <li><a href="#about" class="hover:text-orange-500 transition-colors">About</a></li>
                        <li><a href="#programs" class="hover:text-orange-500 transition-colors">Programs</a></li>
                        <li><a href="#trainers" class="hover:text-orange-500 transition-colors">Trainers</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Programs</h4>
                    <ul class="text-gray-400 space-y-2">
                        <li><a href="#" class="hover:text-orange-500 transition-colors">Strength Training</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">Cardio Fitness</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">Yoga Classes</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">Personal Training</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Contact Info</h4>
                    <ul class="text-gray-400 space-y-2">
                        <li>123 Fitness Street</li>
                        <li>Gym City, GC 12345</li>
                        <li>(555) 123-4567</li>
                        <li>info@fitcoregym.com</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-700 flex flex-col sm:flex-row justify-between items-center">
                <p class="text-gray-400">&copy; 2024 RawFit Gym. All rights reserved.</p>
                <div class="flex space-x-4 mt-4 sm:mt-0">
                    <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hamburger menu toggle
            const hamburger = document.getElementById('hamburger');
            const mobileNav = document.getElementById('mobile-nav');

            if (hamburger && mobileNav) {
                hamburger.addEventListener('click', function() {
                    mobileNav.classList.toggle('hidden');
                });
            }

            // Update navigation active states
            updateNavigation();
        });

        function updateNavigation() {
            const currentHash = window.location.hash || '#home';
            const navLinks = document.querySelectorAll('.nav-link');
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

            // Remove active classes
            [...navLinks, ...mobileNavLinks].forEach(link => {
                link.classList.remove('active', 'bg-orange-500', 'text-white', 'text-orange-500');
                link.classList.add('text-gray-300', 'hover:text-white', 'hover:bg-gray-800');
            });

            // Add active class to current section link
            const activeLinks = document.querySelectorAll(`a[href="${currentHash}"]`);
            activeLinks.forEach(link => {
                if (link.classList.contains('mobile-nav-link')) {
                    link.classList.add('active', 'text-orange-500');
                    link.classList.remove('text-gray-400');
                } else {
                    link.classList.add('active', 'bg-orange-500', 'text-white');
                    link.classList.remove('text-gray-300', 'hover:text-white', 'hover:bg-gray-800');
                }
            });
        }

        // Update navigation on hash change
        window.addEventListener('hashchange', updateNavigation);
    </script>
</body>
</html>

<?php
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$courses = [];
$sql = "SELECT id, trainer_id, title, description, category, duration, status, start_date, end_date, image_path, created_at FROM trainer_courses ORDER BY created_at DESC LIMIT 3";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

$conn->close();
?>