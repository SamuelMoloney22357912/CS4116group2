<?php
    session_start();
    include("connection.php");
    include("functions.php");

    $user_data = check_login($con);
    $hideButton = ($user_data && $user_data['business'] == 1);
    $settings_link = ($user_data && $user_data['business'] == 1) ? "busSettingsTest .php" : "user_profile_settings.php";

    

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
        <a class = "messageBtn" href="message.php">
            <img src="./images/mail_logo_navbar.png" alt="">
        </a>

        <a class = "inquireBtn" href="inquire.php">
            <img src="./images/inquires_logo_navbar.png" alt="">
        </a>
        <a class = "settingsBtn" href= "<?php echo $settings_link; ?>">
            <img src="./images/settings_logo_navbar.png" alt="Settings">
        </a>

    </div>
</div>

    <div class = "feturedAds">
        <div class = "Heading">
            <H1 class = AdHeading>Fetured Business</H1>

        </div>
        <div class = "ads">
            <div class = "ad1">
                <a href="message.php">
                    <img class = "imAd1"src="./images/Picture 1.png" alt="">
                </a>
            </div>
            <div class = "ad2">
                <a  href="message.php">
                    <img class = "imAd2"src="./images/ad_placeholder.png" alt="">
                </a>

            </div>
            <div class = "ad3">
                <a  href="message.php">
                    <img class = "imAd3"src="./images/ad_placeholder.png" alt="">
                </a>

            </div>
        </div>
        <div class = "moreBtn">
            <button class ="seeMoreBtn">
                <a href="explore.php">See More</a>
            </button>
        </div>
    </div>






    

        
        <a href="logout.php">Logout</a>


    
    

   
    
</body>
</html>