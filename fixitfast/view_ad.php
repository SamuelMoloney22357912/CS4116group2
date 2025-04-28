<?php
session_start();
include("connection.php"); // Your DB connection
include("functions.php");  // Any custom functions

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid ad ID.";
    exit;
}

$service_id = intval($_GET['id']);

// Get ad data
$stmt = $con->prepare("SELECT * FROM services WHERE service_id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();
$ad = $result->fetch_assoc();

if (!$ad) {
    echo "<p>Ad not found.</p>";
    exit;
}

// Escape and sanitize fields
$ad['picture'] = !empty($ad['picture']) ? htmlspecialchars($ad['picture']) : './images/ad_placeholder.png';
$ad['name'] = !empty($ad['name']) ? htmlspecialchars($ad['name']) : 'Unnamed Service';
$ad['description'] = nl2br(htmlspecialchars($ad['description'] ?? ''));
$ad['price1'] = htmlspecialchars($ad['price1']);
$ad['price2'] = htmlspecialchars($ad['price2']);
$ad['price3'] = htmlspecialchars($ad['price3']);
$ad['description1'] = nl2br(htmlspecialchars($ad['description1']));
$ad['description2'] = nl2br(htmlspecialchars($ad['description2']));
$ad['description3'] = nl2br(htmlspecialchars($ad['description3']));

$business_id = intval($ad['business_id']); 

$business_stmt = $con->prepare("SELECT owner_name, phone_no FROM businesses WHERE business_id = ?");
$business_stmt->bind_param("i", $business_id);
$business_stmt->execute();
$business_result = $business_stmt->get_result();
$business = $business_result->fetch_assoc();

$business_name = $business ? htmlspecialchars($business['owner_name']) : 'Unknown Business';
$business_phone = $business ? htmlspecialchars($business['phone_no']) : 'No Phone Available';

$review_stmt = $con->prepare("SELECT user_id, rating, comment FROM reviews WHERE service_id = ? ORDER BY created_at DESC LIMIT 3");
$review_stmt->bind_param("i", $service_id);
$review_stmt->execute();
$review_result = $review_stmt->get_result();
$reviews = $review_result->fetch_all(MYSQLI_ASSOC);

$avg_stmt = $con->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE service_id = ?");
$avg_stmt->bind_param("i", $service_id);
$avg_stmt->execute();
$avg_result = $avg_stmt->get_result();
$avg_row = $avg_result->fetch_assoc();
$average_rating = round($avg_row['avg_rating'], 1);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $ad['name'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Header -->
<div class="bg-[#67AB9F] text-white text-xl font-bold p-4 flex justify-between items-center">
    <div class="flex items-center space-x-4">
        <a href="explore.php" class="text-white text-lg hover:underline">&larr; Back</a>
        <span><?= $ad['name'] ?></span>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-6xl mx-auto p-6 bg-white mt-6 rounded-lg shadow">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Image -->
        <div class="flex-shrink-0 w-full md:w-1/3 flex flex-col items-center">
            <img src="<?= $ad['picture'] ?>" alt="<?= $ad['name'] ?>" class="rounded-lg w-full h-auto object-cover mb-4">
            <a href="inquiries.php" class="bg-[#67AB9F] text-white px-4 py-2 rounded hover:bg-[#579c8f] transition">
               Inquire About This Service
            </a>

            <a href="review.php?service_id=<?php echo $service_id; ?>" class="bg-[#67AB9F] text-white px-4 py-2 rounded hover:bg-[#579c8f] transition"> Leave a Review</a>
        </div>

        <!-- Description + Prices -->
        <div class="flex-grow">
            <h2 class="text-2xl font-bold mb-4"><?= $ad['name'] ?></h2>
            <?php if ($average_rating > 0): ?>
                <div class="flex items-center mb-4">
                    <div class="text-yellow-500 text-lg mr-2">
                        
                    </div>
                    <div class="text-gray-700 text-sm">(Average Rating: <?= $average_rating ?>/5)</div>
                </div>
            <?php else: ?>
                <div class="text-gray-500 text-sm mb-4">(No ratings yet)</div>
            <?php endif; ?>
            <div class="text-gray-700 mb-4">
                <div><span class="font-semibold">Owner Name:</span> <?= $business_name ?></div>
                <div><span class="font-semibold">Phone:</span> <?= $business_phone ?></div>
            </div>
            <p class="text-gray-700 mb-6"><?= $ad['description'] ?></p>
            

            <!-- Prices -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h3 class="text-lg font-semibold mb-2">Prices</h3>
                <ul class="list-disc list-inside text-gray-800 space-y-1">
                    <li>Price 1: <?= $ad['price1'] ?></li>
                    <li>Price 2: <?= $ad['price2'] ?></li>
                    <li>Price 3: <?= $ad['price3'] ?></li>
                </ul>
            </div>

            <!-- Extra Descriptions -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-2">More Info</h3>
                <p class="mb-2"><?= $ad['description1'] ?></p>
                <p class="mb-2"><?= $ad['description2'] ?></p>
                <p><?= $ad['description3'] ?></p>
            </div>
        </div>
    </div>

    <?php if (!empty($reviews)): ?>
<div class="mt-8">
    <h3 class="text-2xl font-bold mb-4">Customer Reviews</h3>
    <div class="space-y-4">
        <?php foreach ($reviews as $review): ?>
            <div class="p-4 bg-gray-100 rounded-lg shadow">
                <div class="flex items-center justify-between mb-2">
                    <div class="font-semibold">User #<?= htmlspecialchars($review['user_id']) ?></div>
                    <div class="text-yellow-500">
                        <?= str_repeat('⭐', (int)$review['rating']) ?>
                    </div>
                </div>
                <p class="text-gray-700"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php else: ?>
<div class="mt-8">
    <h3 class="text-2xl font-bold mb-4">Customer Reviews</h3>
    <p class="text-gray-500">No reviews yet for this service.</p>
</div>
<?php endif; ?>
</div>

</body>
</html>
