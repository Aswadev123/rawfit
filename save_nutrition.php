<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit();
}

$user_id = $_SESSION['user_id'];
$age = intval($data['age']);
$gender = $data['gender'];
$weight = floatval($data['weight']);
$height = intval($data['height']);
$activity_level = $data['activity_level'];
$goal = $data['goal'];

// Connect to database
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Check if profile exists
$sql = "SELECT user_id FROM nutrition_profiles WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update existing profile
    $stmt->close();
    $sql = "UPDATE nutrition_profiles SET age=?, gender=?, weight=?, height=?, activity_level=?, goal=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isdiisi", $age, $gender, $weight, $height, $activity_level, $goal, $user_id);
} else {
    // Insert new profile
    $stmt->close();
    $sql = "INSERT INTO nutrition_profiles (user_id, age, gender, weight, height, activity_level, goal) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisdiis", $user_id, $age, $gender, $weight, $height, $activity_level, $goal);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save data']);
}

$stmt->close();
$conn->close();
?>
