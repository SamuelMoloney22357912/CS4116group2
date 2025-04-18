<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

session_start();
include("connection.php");

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$sender_id = $data['sender_id'] ?? null;
$recipient_id = $data['recipient_id'] ?? null;
$message = $data['message'] ?? '';

if (!$sender_id || !$recipient_id || !$message) {
    echo json_encode(['error' => 'Missing sender, recipient, or message']);
    exit;
}

$query = "INSERT INTO message (sender_id, recipient_id	, content, sent_at) VALUES (?, ?, ?, NOW())";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "iis", $sender_id, $recipient_id, $message);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    error_log("DB Insert Failed: " . mysqli_error($con));
    echo json_encode(['error' => 'Message send failed']);
}
exit;
