<?php
    session_start();
    include("connection.php");
    include("functions.php");

    $user_data = check_login($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/index.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>

    <div class="container">
        <div class="row">
            <div class="col-auto">
                <a class="homeButton" href="index.php">Home</a>
            </div>
            <div class="col-auto">
                <a href="explore.php">Explore</a>
            </div>
            <div class="col-auto">
                <a href="info.php">Info</a>
            </div>
            <div class="col-auto">
                <img src="./images/fixitfast_logo.png" alt="logo">
            </div>
            <div class="col-auto">
                <a href="placeAd.php">
                    <button class="placeAdButton" type="button">Place Ad</button>
                </a>
            </div>
            <div class="col-auto">
                <a href="mail.php">
                    <img src="./images/mail_logo_navbar.png" alt="mail_logo">
                </a>
            </div>
            <div class="col-auto">
                <a href="inquires.php">
                    <img src="./images/inquires_logo_navbar.png" alt="inquires_logo">
                </a>
            </div>
            <div class="col-auto">
                <a href="settings.php">
                    <img src="./images/settings_logo_navbar.png" alt="settings_logo">
                </a>
            </div>
            <!-- Add the Profile Settings link here -->
            <div class="col-auto ms-auto"> <!-- Add ms-auto for right alignment -->
                <a href="user_profile_settings.php">
                    <button type="button" class="btn btn-link">Profile</button>
                </a>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-auto">
                <H1 class="heading_Home">Featured Businesses</H1>
            </div>
        </div>

        <div class="row">
            <div class="col-auto">
                <a href="">
                    <img src="./images/ad_placeholder.png" alt="">
                </a>
            </div>
            <div class="col-auto">
                <a href="">
                    <img src="./images/ad_placeholder.png" alt="">
                </a>
            </div>
            <div class="col-auto">
                <a href="">
                    <img src="./images/ad_placeholder.png" alt="">
                </a>
            </div>
        </div>

    </div>

    <H1>Home</H1>
    <a href="Login.php">Login</a>
    <br>
    <a href="SignUp.php">SignUp</a>
    <br>
    <a href="logout.php">Logout</a>

    <select>
        <option value="default" selected disabled>Select an option</option>
        <option value="apple">Apple</option>
        <option value="banana">Banana</option>
        <option value="orange">Orange</option>
    </select>

</body>
</html>
