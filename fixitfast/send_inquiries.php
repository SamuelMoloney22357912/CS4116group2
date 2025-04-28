<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

session_start();
include("connection.php");

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['sender_id'] ?? null;
$service_id = $data['service_id'] ?? null;
$content = $data['inquiry'] ?? '';

if (!$user_id || !$service_id || !$content) {
    echo json_encode(['error' => 'Missing sender, service, or inquiry']);
    exit;
}

$query = "INSERT INTO inquiries (user_id, service_id, content, created_at) VALUES (?, ?, ?, NOW())";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "iis", $user_id, $service_id, $content);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    error_log("DB Insert Failed: " . mysqli_error($con));
    echo json_encode(['error' => 'Message send failed']);
}
exit;
