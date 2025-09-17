<?php
session_start();
header('Content-Type: application/json');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$date = date('Y-m-d');

// Database connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get food log for today
$sql = "SELECT meal, calories, protein, carbs, fats FROM food_log WHERE user_id = ? AND date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $date);
$stmt->execute();
$result = $stmt->get_result();

$foodLog = [];
while ($row = $result->fetch_assoc()) {
    $foodLog[] = [
        'name' => $row['meal'], // 'meal' from DB sent as 'name' to JS
        'calories' => (int)$row['calories'],
        'protein' => (int)$row['protein'],
        'carbs' => (int)$row['carbs'],
        'fats' => (int)$row['fats']
    ];
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'foodLog' => $foodLog]);
?>