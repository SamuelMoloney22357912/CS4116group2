<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("connection.php");
include("functions.php");

$user_data = check_login($con);

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

$query = ""; 
$businesses = [];
$countyFilter = '';
$ratingFilter = '';
$priceFilter = '';
$categoryFilter = '';

if (isset($_GET['query'])) {
    $query = trim($_GET['query']);
}

if (isset($_GET['county'])) {
    $countyFilter = $_GET['county'];
}

if (isset($_GET['rating'])) {
    $ratingFilter = $_GET['rating'];
}

if (isset($_GET['price'])) {
    $priceFilter = $_GET['price'];
}

if (isset($_GET['category'])) {
    $categoryFilter = $_GET['category'];
}

$columnCheckQuery = "SHOW COLUMNS FROM businesses";
$columnResult = mysqli_query($con, $columnCheckQuery);

$validColumns = [];
while ($column = mysqli_fetch_assoc($columnResult)) {
    $validColumns[] = $column['Field'];
}

$categoryColumn = in_array('category', $validColumns) ? 'category' : (in_array('business_type', $validColumns) ? 'business_type' : null);

$queryFilter = "SELECT * FROM businesses WHERE 1";

$filters = [];
$filterValues = [];

if ($categoryColumn) {
    $queryFilter .= " AND (name LIKE ? OR $categoryColumn LIKE ?)";
    $filters[] = "%$query%";  
    $filters[] = "%$query%";  
} else {
    $queryFilter .= " AND name LIKE ?";
    $filters[] = "%$query%";
}


if ($countyFilter) {
    $queryFilter .= " AND county = ?";
    $filters[] = $countyFilter;
}

if ($ratingFilter) {
    $queryFilter .= " AND rating >= ?";
    $filters[] = $ratingFilter;
}

if ($priceFilter) {
    $queryFilter .= " AND price <= ?";
    $filters[] = $priceFilter;
}

if ($categoryFilter) {
    $queryFilter .= " AND $categoryColumn = ?";
    $filters[] = $categoryFilter;
}

if ($stmt = $con->prepare($queryFilter)) {
    $types = str_repeat("s", count($filters)); 

    if (!empty($filters)) {
        $stmt->bind_param($types, ...$filters);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $businesses[] = $row;
    }

    $stmt->close();
} else {
    die("Statement preparation failed: " . $con->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Businesses</title>
    <link rel="stylesheet" href="./css/explorePage.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" 
          integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" 
          crossorigin="anonymous">
</head>
<body>

<div class="container">

    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>
                <a class="navbar-brand" href="index.php">Home</a>
                <a class="navbar-brand" href="explorePage.php">Explore</a>
                <a class="navbar-brand" href="info.php">Info</a>
            </div>
            <div class="text-center">
                <h1 class="navbar-brand mb-0">FIXITFAST</h1>
                <h5 class="navbar-brand mb-0" style="font-size: 0.8rem;">Repair Services</h5>
            </div>
            <div class="d-flex align-items-center">
                <a href="businessPlaceAd.php">
                    <button class="btn btn-success me-3">Place Ad</button>
                </a>
                <a href="user_profile_settings.php">
                    <img src="./images/settings_icon.png" alt="Settings" width="30" height="30" class="rounded-circle">
                </a>
            </div>
        </div>
    </nav>

    <hr>

    <form action="explorePage.php" method="GET" class="search-form">
        <input type="text" name="query" class="form-control" placeholder="Search for businesses..." required value="<?php echo htmlspecialchars($query); ?>">
        <button type="submit" class="btn btn-primary mt-2">Search</button>
    </form>

    <hr>

    <div class="filters-section">
        <form action="explorePage.php" method="GET">
            <div class="row">
                <div class="col-md-3">
                    <label for="county">County</label>
                    <select name="county" class="form-control" id="county">
    <option value="">Select County</option>
    <option value="Antrim" <?php echo $countyFilter == 'Antrim' ? 'selected' : ''; ?>>Antrim</option>
    <option value="Armagh" <?php echo $countyFilter == 'Armagh' ? 'selected' : ''; ?>>Armagh</option>
    <option value="Carlow" <?php echo $countyFilter == 'Carlow' ? 'selected' : ''; ?>>Carlow</option>
    <option value="Cavan" <?php echo $countyFilter == 'Cavan' ? 'selected' : ''; ?>>Cavan</option>
    <option value="Clare" <?php echo $countyFilter == 'Clare' ? 'selected' : ''; ?>>Clare</option>
    <option value="Cork" <?php echo $countyFilter == 'Cork' ? 'selected' : ''; ?>>Cork</option>
    <option value="Derry" <?php echo $countyFilter == 'Derry' ? 'selected' : ''; ?>>Derry</option>
    <option value="Donegal" <?php echo $countyFilter == 'Donegal' ? 'selected' : ''; ?>>Donegal</option>
    <option value="Down" <?php echo $countyFilter == 'Down' ? 'selected' : ''; ?>>Down</option>
    <option value="Dublin" <?php echo $countyFilter == 'Dublin' ? 'selected' : ''; ?>>Dublin</option>
    <option value="Fermanagh" <?php echo $countyFilter == 'Fermanagh' ? 'selected' : ''; ?>>Fermanagh</option>
    <option value="Galway" <?php echo $countyFilter == 'Galway' ? 'selected' : ''; ?>>Galway</option>
    <option value="Kerry" <?php echo $countyFilter == 'Kerry' ? 'selected' : ''; ?>>Kerry</option>
    <option value="Kildare" <?php echo $countyFilter == 'Kildare' ? 'selected' : ''; ?>>Kildare</option>
    <option value="Kilkenny" <?php echo $countyFilter == 'Kilkenny' ? 'selected' : ''; ?>>Kilkenny</option>
    <option value="Laois" <?php echo $countyFilter == 'Laois' ? 'selected' : ''; ?>>Laois</option>
    <option value="Leitrim" <?php echo $countyFilter == 'Leitrim' ? 'selected' : ''; ?>>Leitrim</option>
    <option value="Limerick" <?php echo $countyFilter == 'Limerick' ? 'selected' : ''; ?>>Limerick</option>
    <option value="Longford" <?php echo $countyFilter == 'Longford' ? 'selected' : ''; ?>>Longford</option>
    <option value="Louth" <?php echo $countyFilter == 'Louth' ? 'selected' : ''; ?>>Louth</option>
    <option value="Mayo" <?php echo $countyFilter == 'Mayo' ? 'selected' : ''; ?>>Mayo</option>
    <option value="Meath" <?php echo $countyFilter == 'Meath' ? 'selected' : ''; ?>>Meath</option>
    <option value="Monaghan" <?php echo $countyFilter == 'Monaghan' ? 'selected' : ''; ?>>Monaghan</option>
    <option value="Offaly" <?php echo $countyFilter == 'Offaly' ? 'selected' : ''; ?>>Offaly</option>
    <option value="Roscommon" <?php echo $countyFilter == 'Roscommon' ? 'selected' : ''; ?>>Roscommon</option>
    <option value="Sligo" <?php echo $countyFilter == 'Sligo' ? 'selected' : ''; ?>>Sligo</option>
    <option value="Tipperary" <?php echo $countyFilter == 'Tipperary' ? 'selected' : ''; ?>>Tipperary</option>
    <option value="Tyrone" <?php echo $countyFilter == 'Tyrone' ? 'selected' : ''; ?>>Tyrone</option>
    <option value="Waterford" <?php echo $countyFilter == 'Waterford' ? 'selected' : ''; ?>>Waterford</option>
    <option value="Westmeath" <?php echo $countyFilter == 'Westmeath' ? 'selected' : ''; ?>>Westmeath</option>
    <option value="Wexford" <?php echo $countyFilter == 'Wexford' ? 'selected' : ''; ?>>Wexford</option>
    <option value="Wicklow" <?php echo $countyFilter == 'Wicklow' ? 'selected' : ''; ?>>Wicklow</option>
</select>

                </div>

                <div class="col-md-3">
                    <label for="rating">Rating</label>
                    <select name="rating" class="form-control" id="rating">
                        <option value="">Select Rating</option>
                        <option value="1" <?php echo $ratingFilter == '1' ? 'selected' : ''; ?>>1 Star</option>
                        <option value="2" <?php echo $ratingFilter == '2' ? 'selected' : ''; ?>>2 Stars</option>
                        <option value="3" <?php echo $ratingFilter == '3' ? 'selected' : ''; ?>>3 Stars</option>
                        <option value="4" <?php echo $ratingFilter == '4' ? 'selected' : ''; ?>>4 Stars</option>
                        <option value="5" <?php echo $ratingFilter == '5' ? 'selected' : ''; ?>>5 Stars</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="price">Max Price (€)</label>
                    <input type="number" name="price" class="form-control" id="price" value="<?php echo $priceFilter; ?>" min="0">
                </div>

                <div class="col-md-3">
                    <label for="category">Category</label>
                    <select name="category" class="form-control" id="category">
                        <option value="">Select Category</option>
                        <option value="Plumbing" <?php echo $categoryFilter == 'Plumbing' ? 'selected' : ''; ?>>Plumbing</option>
                        <option value="Electrical" <?php echo $categoryFilter == 'Electrical' ? 'selected' : ''; ?>>Electrical</option>
                        <option value="Roofing" <?php echo $categoryFilter == 'Roofing' ? 'selected' : ''; ?>>Roofing</option>
                        <option value="Carpentry" <?php echo $categoryFilter == 'Carpentry' ? 'selected' : ''; ?>>Carpentry</option>
                        <option value="Gardening" <?php echo $categoryFilter == 'Gardening' ? 'selected' : ''; ?>>Gardening</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Apply Filters</button>
        </form>
    </div>

    <hr>

    <h2>Search Results</h2>
    <div class="business-list">
        <?php if (!empty($businesses)): ?>
            <?php foreach ($businesses as $business): ?>
                <div class="card mt-3">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo htmlspecialchars($business['name']); ?></h3>
                        <p class="card-text"><strong>Category:</strong> <?php echo htmlspecialchars($business[$categoryColumn] ?? 'N/A'); ?></p>
                        <p class="card-text"><?php echo htmlspecialchars($business['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No businesses found.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
