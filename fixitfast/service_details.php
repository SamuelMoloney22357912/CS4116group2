<?php
include("connection.php");

if (isset($_GET['service_id']) && ctype_digit($_GET['service_id'])) { 
    $id = (int)$_GET['service_id']; // Convert to integer for safety

    // Prepare and execute the query
    $query = "SELECT * FROM services WHERE service_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if service was found
    if ($service = $result->fetch_assoc()) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= htmlspecialchars($service['name']) ?></title>
        </head>
        <body>
            <h1><?= htmlspecialchars($service['name']) ?></h1>
            <img src="<?= !empty($service['picture']) ? htmlspecialchars($service['picture']) : './images/ad_placeholder.png' ?>" 
                 alt="<?= htmlspecialchars($service['name']) ?>">
            <p><?= htmlspecialchars($service['description'] ?? 'No description available.') ?></p>
        </body>
        </html>
        <?php
    } else {
        echo "<h2>Service not found.</h2>"; // If no service found with this ID
    }
} else {
    echo "<h2>Invalid request.</h2>"; // If ID is missing or invalid
}
?>
