<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

session_start();
include("connection.php");
include("functions.php");

// Check if logged in
$user_data = check_login($con);
if (!$user_data || $user_data['business'] != 1) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$inquiry_id = $input['inquiry_id'] ?? null;
$status = $input['status'] ?? null;

$allowedStatuses = ['Approved', 'Rejected'];
if (!$inquiry_id || !in_array($status, $allowedStatuses)) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$currentUserId = $user_data['user_id'];

// Check if this inquiry belongs to a service owned by this business
$query = "
    SELECT i.inquiry_id
    FROM inquiries i
    JOIN services s ON i.service_id = s.service_id
    WHERE i.inquiry_id = ? AND s.business_id = ?
";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ii", $inquiry_id, $currentUserId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo json_encode(['error' => 'Not authorized or inquiry not found']);
    exit;
}

// Perform update
$updateQuery = "UPDATE inquiries SET status = ? WHERE inquiry_id = ?";
$updateStmt = mysqli_prepare($con, $updateQuery);
mysqli_stmt_bind_param($updateStmt, "si", $status, $inquiry_id);

if (mysqli_stmt_execute($updateStmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to update status']);
}
