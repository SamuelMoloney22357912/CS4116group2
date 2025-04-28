<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // show errors in browser
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

header('Content-Type: application/json');

session_start();
include("connection.php");

$currentUserId = $_SESSION['user_id'] ?? null;

if (!$currentUserId) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// Fetch all users except the current one
$query = "SELECT business_id, business_name FROM businesses WHERE business_id != ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $currentUserId);

if (mysqli_stmt_execute($stmt)) {
    $result = mysqli_stmt_get_result($stmt);
    $businesses = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $businesses[] = $row;
    }

    echo json_encode($businesses);
} else {
    error_log("Query failed: " . mysqli_error($con));
    echo json_encode(['error' => 'Query failed']);
}
exit;
