<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); 
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log'); // Logs will go here

header('Content-Type: application/json');

session_start();
include("connection.php");

$currentUserId = $_SESSION['user_id'] ?? null;

if (!$currentUserId) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$query = "
    SELECT DISTINCT u.user_id, u.user_name 
    FROM users u
    INNER JOIN message m 
    ON (u.user_id = m.sender_id OR u.user_id = m.recipient_id)
    WHERE u.user_id != ? AND (m.sender_id = ? OR m.recipient_id = ?)
";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "iii", $currentUserId, $currentUserId, $currentUserId);

if (mysqli_stmt_execute($stmt)) {
    $result = mysqli_stmt_get_result($stmt);
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    echo json_encode($users);
} else {
    error_log("Query failed: " . mysqli_error($con));
    echo json_encode(['error' => 'Query failed']);
}
exit;
