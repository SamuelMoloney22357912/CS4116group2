<?php
    session_start();
    include("connection.php");
    include("functions.php");

    $user_data = check_login($con);
    $hideButton = ($user_data && $user_data['business'] == 1);
    $settings_link = ($user_data && $user_data['business'] == 1) ? "businessProfileSettings.php" : "user_profile_settings.php";

    try{
        $topRatedQuery = "
        SELECT s.*, AVG(r.rating) as avg_rating 
        FROM services s
        LEFT JOIN reviews r ON s.service_id = r.service_id
        GROUP BY s.service_id
        ORDER BY avg_rating DESC
        LIMIT 3";
        $topRatedResult = mysqli_query($con, $topRatedQuery);
        $topRatedAds = mysqli_fetch_all($topRatedResult, MYSQLI_ASSOC);


    }catch(mysqli_sql_exception $e){
        die("Database fetch error: " . $e->getMessage());
    }
    
    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/index.css">
    
    <title>Home</title>

  
    

    
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
        <a class = "settingsBtn" href= "<?php echo $settings_link; ?>">
            <img src="./images/settings_logo_navbar.png" alt="Settings">
        </a>

    </div>
</div>

    <div class = "feturedAds">
        <div class = "Heading">
            <H1 class = AdHeading>Featured Business</H1>

        </div>
        <div class = "ads">
            <div class="ad1">
                <?php if (!empty($topRatedAds[0])): ?>
                    <div class="ad-card">
                        <a href="view_ad.php?id=<?= htmlspecialchars($topRatedAds[0]['service_id']) ?>">
                        <img src="<?= !empty($topRatedAds[0]['picture']) ? htmlspecialchars($topRatedAds[0]['picture']) : './images/ad_placeholder.png' ?>" 
                            alt="<?= htmlspecialchars($topRatedAds[0]['name']) ?>">

                        </a>
                        <h2><?= htmlspecialchars($topRatedAds[0]['name']) ?></h2>
                    </div>
                <?php endif; ?>
            </div>
            <div class="ad2">
                <?php if (!empty($topRatedAds[1])): ?>
                    <div class="ad-card">
                        <a href="view_ad.php?id=<?= htmlspecialchars($topRatedAds[1]['service_id']) ?>">
                        <img src="<?= !empty($topRatedAds[1]['picture']) ? htmlspecialchars($topRatedAds[1]['picture']) : './images/ad_placeholder.png' ?>" 
                            alt="<?= htmlspecialchars($topRatedAds[1]['name']) ?>">

                        </a>
                        <h2><?= htmlspecialchars($topRatedAds[1]['name']) ?></h2>
                    </div>
                <?php endif; ?>
            </div>
            <div class="ad3">
                <?php if (!empty($topRatedAds[2])): ?>
                    <div class="ad-card">
                        <a href="view_ad.php?id=<?= htmlspecialchars($topRatedAds[2]['service_id']) ?>">
                        <img src="<?= !empty($topRatedAds[2]['picture']) ? htmlspecialchars($topRatedAds[2]['picture']) : './images/ad_placeholder.png' ?>" 
                            alt="<?= htmlspecialchars($topRatedAds[2]['name']) ?>">
                        </a>
                        <h2><?= htmlspecialchars($topRatedAds[2]['name']) ?></h2>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class = "moreBtn">
            <button class ="seeMoreBtn">
                <a href="explore.php">See More</a>
            </button>
        </div>
    </div>

    <div class = slogan>
        

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