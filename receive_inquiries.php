<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

session_start();
include("connection.php");

header('Content-Type: application/json');

$currentUserId = $_SESSION['user_id'] ?? null;

if (!$currentUserId) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$query = "
    SELECT i.inquiry_id, i.content, i.status, i.created_at, i.user_id, s.business_id, s.service_id
    FROM inquiries i
    JOIN services s ON i.service_id = s.service_id
    WHERE i.user_id = ? OR s.business_id = ?
    ORDER BY i.created_at ASC
";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ii", $currentUserId, $currentUserId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$inquiries = [];
while ($row = mysqli_fetch_assoc($result)) {
    $inquiries[] = $row;
}

echo json_encode($inquiries);
exit;
