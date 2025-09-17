<?php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
file_put_contents('food_log_error.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}
$user_id = $_SESSION['user_id'];

// Database connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    error_log("DB Connection failed: " . $conn->connect_error);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['foodLog']) || !is_array($data['foodLog'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid food log data']);
    exit;
}

$foodLog = $data['foodLog'];

// Get current date
$currentDate = date('Y-m-d');
$userId = $_SESSION['user_id'];

// Delete existing entries for today
$deleteSql = "DELETE FROM food_log WHERE user_id = ? AND date = ?";
$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param("is", $userId, $currentDate);
$deleteStmt->execute();
$deleteStmt->close();

// Insert new food log entries
$success = true;
foreach ($foodLog as $food) {
    $stmt = $conn->prepare("INSERT INTO food_log (user_id, date, meal, calories, protein, carbs, fats, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        continue;
    }
    $stmt->bind_param(
        "issiiii",
        $user_id,
        $currentDate,
        $food['name'], // <-- This is the key change: use $food['name'] for 'meal'
        $food['calories'],
        $food['protein'],
        $food['carbs'],
        $food['fats']
    );
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
    }
    $stmt->close();
}

$conn->close();

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Food log saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error saving food log']);
}
?>