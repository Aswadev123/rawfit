<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Database connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Fetch today's logs
$sql = "SELECT food_name as name, calories, protein, carbs, fats 
        FROM food_log 
        WHERE user_id = ? AND log_date = CURDATE()
        ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$foodLog = [];
while ($row = $result->fetch_assoc()) {
    // Ensure types match JS expectations
    $row['calories'] = (int)$row['calories'];
    $row['protein'] = (float)$row['protein'];
    $row['carbs'] = (float)$row['carbs'];
    $row['fats'] = (float)$row['fats'];
    $foodLog[] = $row;
}

echo json_encode(['success' => true, 'foodLog' => $foodLog]);

$stmt->close();
$conn->close();
?>