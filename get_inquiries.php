<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

session_start();
include("connection.php");
include("functions.php");

$user_data = check_login($con);

if (!$user_data) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$currentUserId = $user_data['user_id'];
$isBusiness = $user_data['business'] == 1;

// Query depends on user type
if ($isBusiness) {
    // Get services owned by business
    $query = "
        SELECT i.*, u.user_id, s.service_id 
        FROM inquiries i
        JOIN users u ON i.user_id = u.user_id
        JOIN services s ON i.service_id = s.service_id
        WHERE s.business_id = ?
        ORDER BY i.created_at DESC
    ";
} else {
    // Customer view: see their own inquiries
    $query = "
        SELECT i.*, u.user_id, s.service_id 
        FROM inquiries i
        JOIN users u ON i.user_id = u.user_id
        JOIN services s ON i.service_id = s.service_id
        WHERE i.user_id = ?
        ORDER BY i.created_at DESC
    ";
}

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $currentUserId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$inquiries = [];
while ($row = mysqli_fetch_assoc($result)) {
    $inquiries[] = $row;
}

echo json_encode($inquiries);
exit;
