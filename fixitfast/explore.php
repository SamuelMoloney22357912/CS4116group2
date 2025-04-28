<?php
    session_start();
    include("connection.php");
    include("functions.php");

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $user_data = check_login($con);
    $hideButton = ($user_data && $user_data['business'] == 1);
    $settings_link = ($user_data && $user_data['business'] == 1) ? "businessProfileSettings.php" : "user_profile_settings.php";

    $categoryOptions = "";
    try {
        $catQuery = "SELECT category_id, category_name FROM service_categories";
        $catResult = mysqli_query($con, $catQuery);
        
    } catch (mysqli_sql_exception $e) {
        die("Error fetching categories: " . $e->getMessage());
    }


    $query = "SELECT service_id, picture, name FROM services";


    try{
        $result = mysqli_query($con, $query);

        if (!$result) {
            die("Query failed: " . mysqli_error($con));
        }

        $services = array();

        // Fetch each row and add it to the array
        while ($row = mysqli_fetch_assoc($result)) {
            $services[] = $row; // $row will be an associative array with keys 'picture' and 'name'
        }

        // Optionally, you can print the array to check the result
        //echo "<pre>";
       // print_r($services);
        //echo "</pre>";
    }catch(mysqli_sql_exception $e){
        die("Fetch error". $e->getMessage());

    }

    $search = isset($_GET['search']) ? trim($_GET['search']) : "";
    $county = isset($_GET['county']) ? trim($_GET['county']) : "";
    $category = isset($_GET['category']) ? trim($_GET['category']) : "";
    $price = isset($_GET['price']) ? trim($_GET['price']) : "";
    $rating = isset($_GET['rating']) ? trim($_GET['rating']) : "";

    // Start SQL query
    //$sql = "SELECT * FROM services WHERE 1=1"; 
    $sql = "
    SELECT s.*, ROUND(AVG(r.rating)) as avg_rating
    FROM services s
    LEFT JOIN reviews r ON s.service_id = r.service_id
    WHERE 1=1";
    $params = [];
    $types = "";

    // Apply search filter (if needed)
    if (!empty($search)) {
        $sql .= " AND (name LIKE ? OR description LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ss";
    }

    // Apply county filter
    if (!empty($county)) {
        $sql .= " AND county = ?";
        $params[] = $county;
        $types .= "s";
    }

    // Apply category filter
    if (!empty($category)) {
        $sql .= " AND category_id = ?";
        $params[] = $category;
        $types .= "s";
    }

    // Apply price filter
    if (!empty($price)) {
        switch ($price) {
            case "0-100":
                $sql .= " AND (price1 BETWEEN 0 AND 100 OR price2 BETWEEN 0 AND 100 OR price3 BETWEEN 0 AND 100)";
                break;
            case "100-200":
                $sql .= " AND (price1 BETWEEN 100 AND 200 OR price2 BETWEEN 100 AND 200 OR price3 BETWEEN 100 AND 200)";
                break;
            case "200-300":
                $sql .= " AND (price1 BETWEEN 200 AND 300 OR price2 BETWEEN 200 AND 300 OR price3 BETWEEN 200 AND 300)";
                break;
            case "300-400":
                $sql .= " AND (price1 BETWEEN 300 AND 400 OR price2 BETWEEN 300 AND 400 OR price3 BETWEEN 300 AND 400)";
                break;
            case "400-500":
                $sql .= " AND (price1 BETWEEN 400 AND 500 OR price2 BETWEEN 400 AND 500 OR price3 BETWEEN 400 AND 500)";
                break;
            case "500+":
                $sql .= " AND (price1 > 500 OR price2 > 500 OR price3 > 500)";
                break;
        }
    }

    $sql .= " GROUP BY s.service_id";

    if (!empty($rating)) {
        $sql .= " AND ROUND(rating) = ?";
        $params[] = $rating;
        $types .= "i";
    }
    

    // Prepare and execute the statement
    $stmt = $con->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Store results in an array
    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }

    

    
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/explore.css">
    
    <title>Explore</title>

  
    

    
</head>
<body>

<div class = "navbar">
    <div class = "pageOpt">
        <button class ="home">
            <a href="index.php">Home</a>
        </button>

        <button class ="explore">
            <a href="explore.php">Explore</a>
        </button>

        <button class ="info">
            <a href="info.php">Info</a>
        </button>
    </div>
    <div class = "logo">
        <a href="index.php">
            <img src="./images/fixitfast_logo.png" alt="">
        </a>

    </div>
    <div class = "rightBtn">
    <?php if ($hideButton): ?>
        <button class = "placeAdBtn">
            <a href="businessPlaceAd.php">Place Ad</a>
        </button>
    <?php endif; ?>
        <a class = "messageBtn" href="messages.php">
            <img src="./images/mail_logo_navbar.png" alt="">
        </a>

        <a class = "inquireBtn" href="inquire.php">
            <img src="./images/Inquires_logo_navbar.png" alt="">
        </a>
        <a class = "settingsBtn" href= "<?php echo ($settings_link); ?>">
            <img src="./images/settings_logo_navbar.png" alt="Settings">
        </a>

    </div>
</div>

<div class = "filters">
    <div class = "serach" >
        <form method="GET">
        <input class = "serachbar" type="text" name="search" placeholder="Search ads..." value="<?= htmlspecialchars($search) ?>">
        <button class = "serachBtn" type="submit">Search</button>

        </form>

    </div>

    <div class = "dropdowns">
        <form method = 'GET'>
        <select name="county" id="">
            <option value="" >County</option>
            <option value="Antrim" <?= ($county == "Antrim") ? 'selected' : '' ?>>Antrim</option>
            <option value="Armagh" <?= ($county == "Armagh") ? 'selected' : '' ?>>Armagh</option>
            <option value="Carlow" <?= ($county == "Carlow") ? 'selected' : '' ?>>Carlow</option>
            <option value="Cavan" <?= ($county == "Cavan") ? 'selected' : '' ?>>Cavan</option>
            <option value="Clare" <?= ($county == "Clare") ? 'selected' : '' ?>>Clare</option>
            <option value="Cork" <?= ($county == "Cork") ? 'selected' : '' ?>>Cork</option>
            <option value="Derry" <?= ($county == "Derry") ? 'selected' : '' ?>>Derry</option>
            <option value="Donegal" <?= ($county == "Donegal") ? 'selected' : '' ?>>Donegal</option>
            <option value="Down" <?= ($county == "Down") ? 'selected' : '' ?>>Down</option>
            <option value="Dublin" <?= ($county == "Dublin") ? 'selected' : '' ?>>Dublin</option>
            <option value="Fermanagh" <?= ($county == "Fermanagh") ? 'selected' : '' ?>>Fermanagh</option>
            <option value="Galway" <?= ($county == "Galway") ? 'selected' : '' ?>>Galway</option>
            <option value="Kerry" <?= ($county == "Kerry") ? 'selected' : '' ?>>Kerry</option>
            <option value="Kildare" <?= ($county == "Kildare") ? 'selected' : '' ?>>Kildare</option>
            <option value="Kilkenny" <?= ($county == "Kilkenny") ? 'selected' : '' ?>>Kilkenny</option>
            <option value="Laois" <?= ($county == "Laois") ? 'selected' : '' ?>>Laois</option>
            <option value="Leitrim" <?= ($county == "Leitrim") ? 'selected' : '' ?>>Leitrim</option>
            <option value="Limerick" <?= ($county == "Limerick") ? 'selected' : '' ?>>Limerick</option>
            <option value="Longford" <?= ($county == "Longford") ? 'selected' : '' ?>>Longford</option>
            <option value="Louth" <?= ($county == "Louth") ? 'selected' : '' ?>>Louth</option>
            <option value="Mayo" <?= ($county == "Mayo") ? 'selected' : '' ?>>Mayo</option>
            <option value="Meath" <?= ($county == "Meath") ? 'selected' : '' ?>>Meath</option>
            <option value="Monaghan" <?= ($county == "Monaghan") ? 'selected' : '' ?>>Monaghan</option>
            <option value="Offaly" <?= ($county == "Offaly") ? 'selected' : '' ?>>Offaly</option>
            <option value="Roscommon" <?= ($county == "Roscommon") ? 'selected' : '' ?>>Roscommon</option>
            <option value="Sligo" <?= ($county == "Sligo") ? 'selected' : '' ?>>Sligo</option>
            <option value="Tipperary" <?= ($county == "Tipperary") ? 'selected' : '' ?>>Tipperary</option>
            <option value="Tyrone" <?= ($county == "Tyrone") ? 'selected' : '' ?>>Tyrone</option>
            <option value="Waterford" <?= ($county == "Waterford") ? 'selected' : '' ?>>Waterford</option>
            <option value="Westmeath" <?= ($county == "Westmeath") ? 'selected' : '' ?>>Westmeath</option>
            <option value="Wexford" <?= ($county == "Wexford") ? 'selected' : '' ?>>Wexford</option>
            <option value="Wicklow" <?= ($county == "Wicklow") ? 'selected' : '' ?>>Wicklow</option>
        </select>

        <select name="rating" id="">
        <option value="" >Rating</option>
        <option value="1" <?= ($rating == "1") ? 'selected' : '' ?>>1 star</option>
        <option value="2" <?= ($rating == "2") ? 'selected' : '' ?>>2 star</option>
        <option value="3" <?= ($rating == "3") ? 'selected' : '' ?>>3 star</option>
        <option value="4" <?= ($rating == "4") ? 'selected' : '' ?>>4 star</option>
        <option value="5" <?= ($rating == "5") ? 'selected' : '' ?>>5 star</option>

        </select>

        <select name="price">
            <option value="">Price</option>
            <option value="0-100" <?= ($price == "0-100") ? 'selected' : '' ?>>0 - 100</option>
            <option value="100-200" <?= ($price == "100-200") ? 'selected' : '' ?>>100 - 200</option>
            <option value="200-300" <?= ($price == "200-300") ? 'selected' : '' ?>>200 - 300</option>
            <option value="300-400" <?= ($price == "300-400") ? 'selected' : '' ?>>300 - 400</option>
            <option value="400-500" <?= ($price == "400-500") ? 'selected' : '' ?>>400 - 500</option>
            <option value="500+" <?= ($price == "500+") ? 'selected' : '' ?>>Greater than 500</option>
        </select>

        <select name="category" id="">
        <option value="" >Category</option>
        <?php while ($row = mysqli_fetch_assoc($catResult)): ?>
                    <option value="<?php echo htmlspecialchars($row['category_id']); ?>" <?php if ($row['category_id'] == $category) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($row['category_name']); ?>
                    </option>
        <?php endwhile; ?>

        </select>

        <button class = "applyFil" type="submit">Apply Filters</button>

        </form>

    </div>

</div>

<div class="ads">
    <div class="Heading"></div>
    <div class="ads-container">
        <?php if (empty($services)): ?>
            <div class = "noResDiv">
                <p class = "noResult">No ads found</p>
            </div>
        <?php else: ?>
            <?php foreach ($services as $ad): 
                if (empty($ad['picture'])) {
                    $ad["picture"] = "./images/ad_placeholder.png";
                }
            ?>
                <div class="ad-card">
                    <a href="view_ad.php?id=<?= isset($ad['service_id']) ? htmlspecialchars($ad['service_id']) : '' ?>">
                        <img src="<?= !empty($ad['picture']) ? htmlspecialchars($ad['picture']) : './images/ad_placeholder.png' ?>" 
                            alt="<?= !empty($ad['name']) ? htmlspecialchars($ad['name']) : 'No Name' ?>">
                    </a>
                    <h2><?= !empty($ad['name']) ? htmlspecialchars($ad['name']) : 'Unnamed Service' ?></h2>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>


<footer>
     <div class="footer-content">
         <div class="socials">
             <a href="https://twitter.com" target="_blank"><img src="images/twitter-x-logo-F7DCE5534C-seeklogo.com.png" alt="X"></a>
             <a href="https://instagram.com" target="_blank"><img src="images/Insta_Logo.webp" alt="Instagram"></a>
             <a href="https://youtube.com" target="_blank"><img src="images/YouTube_play_button_square_(2013-2017).svg.png" alt="YouTube"></a>
         </div>
 
         <div class="contact-info">
             <h3>Contact Us</h3>
             <p><strong>General Enquiries:</strong> info@fixitfast.com</p>
             <p><strong>Service Enquiries:</strong> services@fixitfast.com</p>
             <p><strong>Office Location:</strong> 123 Main Street, Limerick</p>
             <p><strong>Office Number:</strong> +353 1 234 5678</p>
         </div>
     </div>
 
     <div class="footer-bottom">
         <p>&copy; 2025 FixItFast. All rights reserved.</p>
     </div>
 </footer>
 
 </div> 
   
    
</body>
</html>