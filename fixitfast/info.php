<?php

session_start();

include("connection.php");
include("functions.php");
$user_data = check_login($con);
$hideButton = ($user_data && $user_data['business'] == 1);
$settings_link = ($user_data && $user_data['business'] == 1) ? "businessProfileSettings.php" : "user_profile_settings.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About FixItFast</title>
    <link rel="stylesheet" href="./css/info.css">
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
            <img src="./images/inquires_logo_navbar.png" alt="">
        </a>
        <a class = "settingsBtn" href= "<?php echo $settings_link; ?>">
            <img src="./images/settings_logo_navbar.png" alt="Settings">
        </a>

    </div>
</div>

    <main>
        <section class="hero">
            <h1>About FixItFast</h1>
            <p>Your go-to platform for trusted, reliable home repair professionals.</p>

        </section>

        <section class="content">
            <h2>Who We Are</h2>
            <p>FixItFast connects homeowners with experienced repair professionals for all types of home maintenance, including plumbing, electrical, roofing, carpentry, and gardening services.</p>

            <h2>Our Mission</h2>
            <p>Our mission is to make home repairs easy, quick, and worry-free. We believe in supporting local businesses while providing customers with the quality services they deserve.</p>

            <h2>Why Choose Us?</h2>
            <ul>
                <li>Trusted and verified professionals</li>
                <li>Quick booking and inquiry system</li>
                <li>Wide range of home services</li>
                <li>Safe, reliable, and easy to use</li>
            </ul>

            <h2>How It Works</h2>
            <ol>
                <li>Explore services listed by local businesses</li>
                <li>Send inquiries or request quotes</li>
                <li>Receive responses and choose your preferred service</li>
                <li>Get your repair job done quickly and efficiently</li>
            </ol>
        </section>
    </main>

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

</body>
</html>
