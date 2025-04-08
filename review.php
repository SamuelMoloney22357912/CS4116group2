<?php
session_start();
include("connection.php");
include("functions.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $userId = 0;
    $serviceId = 1;
    $businessName = $_POST['business_name']; 
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0; 
    $comment = $_POST['comment'];

    
    $businessName = mysqli_real_escape_string($con, $businessName);
    $comment = mysqli_real_escape_string($con, $comment);

    if (!empty($businessName) && !empty($rating) && !empty($comment)) {
        try {
            $query = "INSERT INTO reviews (service_id, business_name, user_id, rating, comment) VALUES 
            ('$serviceId', '$businessName','$userId', '$rating', '$comment')";

        if (mysqli_query($con, $query)) {
            echo "<div id='submissionSuccess' style='display: none;'></div>";
        } else {
            echo "<script>alert('Error submitting review: " . mysqli_error($con) . "');</script>";
        }

        } catch (mysqli_sql_exception $e) {
            die("Database insert error: " . $e->getMessage());
        }
    } else {
        echo "<script>alert('Please fill out all fields.');</script>";
    }
}
?>











<!DOCTYPE html>
<html lang="en">
<head>
    <title>Leave a Review</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/review.css">
</head>

<body>
<div class="heading">
    <div class="backContainer">
        <button onclick="window.history.back();" class="backButton">Back</button>
    </div>
    <div class="headingCenter">
        <h1 class="reviewHeading">Review</h1>
    </div>
</div>
   

    <hr>
    <br>
<form method="post">
    <div class="selectBusiness">
    <div class="text">
        <h2>Select Business:</h2>
    </div>
    <div>
    <select class="dropdown" name="business_name">
    <option value="" disabled selected>Choose an option</option>
    <option value="business1">Business 1</option>
    <option value="business2">Business 2</option>
    <option value="business3">Business 3</option>
</select>

<input type="hidden" name="rating" id="ratingInput">

    </div>
</div>

<br>
<br>

<div class="selectedStars"> 
    <div>
        <h2>Rate this business: </h2>
    </div>

    <div class="stars" name="rating">
        <span class="star" data-value="5">★</span>
        <span class="star" data-value="4">★</span>
        <span class="star" data-value="3">★</span>
        <span class="star" data-value="2">★</span>
        <span class="star" data-value="1">★</span>
    </div>
</div>
<br>
<br>

<div class="comment">
    <div>
    <h2> Leave a comment: </h2>
    </div>
    <div>

    <textarea class="reviewBox" name="comment" placeholder="Tell us about your experience with this business..." ></textarea>
    </div>
    </div>

    <br>
    <br>
    <div class="submit">
    <input type="submit" value="submit">
    </div>

    <div id="thankYouMessage" class="thankYouMessage">
    <h3>Thank you for your review!</h3>
</div>





</form>

<script>
        const stars = document.querySelectorAll('.star');

stars.forEach((star, index) => {
    star.addEventListener('click', function() {
        stars.forEach(s => s.classList.remove('selected'));

        for (let i = 0; i <= index; i++) {
            stars[i].classList.add('selected');
        }

        
        document.getElementById('ratingInput').value = star.getAttribute('data-value');
    });
});

    </script>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const successFlag = document.getElementById('submissionSuccess');
        const thankYouMessage = document.getElementById('thankYouMessage');

        if (successFlag && thankYouMessage) {
            thankYouMessage.style.display = 'block';
        }
    });
</script>







</body>
</html>


