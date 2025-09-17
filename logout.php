<?php
session_start();

// Destroy the session
session_unset();
session_destroy();

// Redirect to index page
header("Location: index.php");
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out - Rawfit</title>
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
<body class="bg-gray-900 font-inter text-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-700 p-6 text-center">
        <h1 class="text-2xl font-bold text-white mb-4">Logging Out...</h1>
        <p class="text-gray-400 mb-6">You are being logged out. Redirecting to the homepage...</p>
        <div class="animate-spin h-8 w-8 border-4 border-t-primary border-gray-700 rounded-full mx-auto"></div>
    </div>
</body>
</html>