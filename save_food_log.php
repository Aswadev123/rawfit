<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$foodLog = $input['foodLog'] ?? [];

// Database connection
$conn = new mysqli("localhost", "root", "", "rawfit");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

try {
    // Start transaction for atomicity
    $conn->begin_transaction();

    // Delete existing logs for this user and today to avoid duplicates
    $sql = "DELETE FROM food_log WHERE user_id = ? AND log_date = CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Insert each new food item
    foreach ($foodLog as $food) {
        $food_name = $food['name'] ?? '';
        $calories = (int)($food['calories'] ?? 0);
        $protein = (float)($food['protein'] ?? 0);
        $carbs = (float)($food['carbs'] ?? 0);
        $fats = (float)($food['fats'] ?? 0);

        // Validate required fields
        if (empty($food_name)) {
            throw new Exception('Invalid food data');
        }

        $sql = "INSERT INTO food_log (user_id, food_name, calories, protein, carbs, fats) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isiddd", $user_id, $food_name, $calories, $protein, $carbs, $fats);
        $stmt->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
?>