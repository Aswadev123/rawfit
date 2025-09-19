<?php
// -----------------------------
// COURSE DOWNLOAD HANDLER
// -----------------------------

// Enable output buffering
ob_start();

// Set error reporting (remove in production)
ini_set('display_errors', 0); // Disable display for production
error_reporting(E_ALL);

// Start session
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    header("Location: login.php?error=" . urlencode("You must be logged in to download this file"));
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    ob_end_clean();
    $course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
    header("Location: user_booking.php?course_id=$course_id&error=" . urlencode("Database connection failed"));
    exit();
}

// Get course_id from URL
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
if ($course_id <= 0) {
    ob_end_clean();
    header("Location: user_booking.php?error=" . urlencode("Invalid course ID"));
    $conn->close();
    exit();
}

// Fetch course file details
$sql = "SELECT id, title, doc_path FROM trainer_courses WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    ob_end_clean();
    header("Location: user_booking.php?course_id=$course_id&error=" . urlencode("Query preparation failed"));
    $conn->close();
    exit();
}

$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $file_path = $row['doc_path'];
    $file_name = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $row['title']) . '.pdf'; // Sanitize filename

    // Log file path for debugging
    error_log("Attempting to download file: $file_path");

    // Check if file_path is not empty
    if (!empty($file_path)) {
        // Construct absolute path (adjust base directory as needed)
        $base_dir = __DIR__ . '/uploads/'; // Ensure this matches your file storage directory
        $file_path = $base_dir . basename($file_path); // Prevent directory traversal
        $absolute_path = realpath($file_path);
        error_log("Resolved absolute path: " . ($absolute_path !== false ? $absolute_path : "Invalid path"));

        // Check if file exists and is readable
        if ($absolute_path !== false && file_exists($absolute_path) && is_readable($absolute_path)) {
            // Clear output buffer
            ob_end_clean();

            // Set headers for file download
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($absolute_path));

            // Read the file
            readfile($absolute_path);
            $stmt->close();
            $conn->close();
            exit;
        } else {
            // File not found or not readable
            $error_msg = "Course file not found or inaccessible. Path: $file_path";
            if ($absolute_path === false) {
                $error_msg .= " (Invalid path)";
            } elseif (!file_exists($absolute_path)) {
                $error_msg .= " (File does not exist)";
            } elseif (!is_readable($absolute_path)) {
                $error_msg .= " (File is not readable)";
            }
            error_log($error_msg);
            ob_end_clean();
            header("Location: user_booking.php?course_id=$course_id&error=" . urlencode($error_msg));
        }
    } else {
        // File path is empty
        error_log("No file path specified for course ID: $course_id");
        ob_end_clean();
        header("Location: user_booking.php?course_id=$course_id&error=" . urlencode("No file path specified for this course"));
    }
} else {
    // Course not found in database
    error_log("No course found for course_id: $course_id");
    ob_end_clean();
    header("Location: user_booking.php?course_id=$course_id&error=" . urlencode("Course not found"));
}

// Clean up
$stmt->close();
$conn->close();
exit();
?>