<?php
session_start();
include("connection.php"); // Connect to the database

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $businessName = $_POST['businessName']; // Get business name
    $comment = $_POST['comment']; // Get review comment
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0; // Convert rating to integer (0-4)

    // Sanitize input to prevent SQL injection
    $businessName = mysqli_real_escape_string($conn, $businessName);
    $comment = mysqli_real_escape_string($conn, $comment);

    // Insert review into database
    if(!empty($commet)){
        echo("inside if");

        try{
        
            $query = "insert into reviews (business_name, comment, rating) values ('$businessName', '$comment', '$rating')";
    
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Review submitted successfully!'); window.location.href='review.html';</script>";
            } else {
                echo "<script>alert('Error submitting review: " . mysqli_error($conn) . "');</script>";
            }
    
        }catch(mysqli_sql_exception $e){
                die("Database insert error". $e->getMessage());
        
        
    }

        }else{

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
        <select class="dropdown" name="businessName">
            <option value="" disabled selected>Choose an option</option>
            <option value="business1">Business 1</option>
            <option value="business2">Business 2</option>
            <option value="business3">Business 3</option>
        </select>
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
            });
        });
        document.getElementById('submitButton').addEventListener('click', function() {
        
        document.getElementById('thankYouMessage').style.display = 'block';
    });
    </script>


</body>
</html>


