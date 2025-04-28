<?php
session_start();
include("connection.php");
include("functions.php");


$user_data = check_login($con);
//$service_id = $_GET['service_id'];
$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;
//echo($service_id);

try{
    $nameQuery = "SELECT name FROM services WHERE service_id = '$service_id'";
    $result = mysqli_query($con, $nameQuery);
    $row = mysqli_fetch_assoc($result);
    $service_name = $row['name'];

}catch(mysqli_sql_exception $e){
    die("Database fecth error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $userId = $user_data['user_id'];
    
    //$serviceId = 1;
    $businessName = $_POST['business_name']; 
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0; 
    $comment = $_POST['comment'];

    
    $businessName = mysqli_real_escape_string($con, $businessName);
    $comment = mysqli_real_escape_string($con, $comment);

    $verifyQuery = "SELECT * FROM verified_users WHERE user_id = '$userId' AND service_id = '$service_id'";
    $verifyResult = mysqli_query($con, $verifyQuery);

    if (mysqli_num_rows($verifyResult) > 0){

    

        if (!empty($rating) && !empty($comment)) {
            try {
                $query = "INSERT INTO reviews (service_id, user_id, rating, comment) VALUES 
                ('$service_id','$userId', '$rating', '$comment')";

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

    }else{
        echo "<script>alert('You are not verified to review this service. You must have used the service to leave a review.');</script>";
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
        <a href="view_ad.php?id=<?php echo $service_id; ?>">
        <button class="backButton">Back</button>
        </a>
    </div>
    <div class="headingCenter">
        <h1 class="reviewHeading">Review</h1>
    </div>
</div>
   

    
    <br>
<form method="post">
    <div class="selectBusiness">
    <div class="text">
        <h2>Review for <?php echo($service_name);?>:</h2>
    </div>
    
    

<input type="hidden" name="rating" id="ratingInput">

    
</div>

<br>
<br>

<div class="selectedStars"> 
    <div>
        <h2>Rate this business: </h2>
    </div>

    <div class="stars" name="rating">
        <span class="star" data-value="1">★</span>
        <span class="star" data-value="2">★</span>
        <span class="star" data-value="3">★</span>
        <span class="star" data-value="4">★</span>
        <span class="star" data-value="5">★</span>
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


