<?php

session_start();
include("connection.php");
include("functions.php");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    
    $businessId = "";
    $categoryId = "";
    $title = $_POST['title'];
    $county = $_POST['county'];
    $description =$_POST['description'];
    $price1 = $_POST['p1'];
    $price2 = $_POST['p2'];
    $price3 = $_POST['p3'];
    $description1 = $_POST['description1'];
    $description2 = $_POST['description2'];
    $description3 = $_POST['description3'];
    $picture = "";

    if(!is_numeric($title) && !empty($county) && !empty($description) && !empty($price1)
        && !empty($price2) && !empty($price3) && !empty($description1) && !empty($description2) && !empty($description3)) {
        //save to database
        //$user_id = random_num(20);
        echo("inside if");
        try{

        
        $query = "insert into services (busines_id, category_id, name,location, description, price1, price2, price3, description1, description2, description3, picture)
         values('$businessId', '$categoryId', '$title','$county','$description','$price1','$price2','$price3','$description1', '$description2', '$description3', '$picture')";
        mysqli_query($con, $query);

        //header("Location: businessPlaceAd.php");
        
        }catch(mysqli_sql_exception $e) {
            die("Database insert error: " . $e->getMessage());

        }

        die();
    }else{
        $eMessage = "Make sure all boxes are filled";
        //echo "Plesse enter a valid name and password";
    }
    
    
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place your ad</title>
    <link rel="stylesheet" href="./css/businessPlaceAd.css">
</head>



<body>
    <div class="headings">
        <div class="leftHeadings">
            <div class="homepage"><a href="home.php"> Home</a></div>
            <div class="explore"> <a href="explore.php"> Explore</a></div>
            <div class="info"> <a href="info.php"> Info</a></div>
        </div>

        <div class="mainHeading">
            <div> <h1>Place Ad</h1></div>
        </div>
        <div class="rightHeadings">
            <div><button onclick="window.location.href='messages.php';" class="messagesButton">Messages</button></div>
            <div><button onclick="window.location.href='inquiries.php';" class="inquiriesButton">Inquiries </button></div>
            <div><button onclick="window.location.href='settings.php';" class="settingsButton">Settings</button></div>
        </div>
    </div>
    <hr>

    <div class="uploadContainer">
        <label for="imageUpload">Upload a photo (MAX 300x200)</label>
        <input type="file" id="imageUpload" accept="image/*">
        <p id="errorMessage" class="error"></p>
    </div>

    <br>

    <form method="post">

    <div class="firstFields">
        <div>
            <label for="title"> Title</label>
            <input type="text" id="title" name="title" placeholder="Enter the name of your business...">
        </div>
        <div>
            <label for="county">County</label>
            <select id="county" name="county">
        <option value="" disabled selected>Select a county</option>
        <option value="Antrim">Antrim</option>
        <option value="Armagh">Armagh</option>
        <option value="Carlow">Carlow</option>
        <option value="Cavan">Cavan</option>
        <option value="Clare">Clare</option>
        <option value="Cork">Cork</option>
        <option value="Derry">Derry</option>
        <option value="Donegal">Donegal</option>
        <option value="Down">Down</option>
        <option value="Dublin">Dublin</option>
        <option value="Fermanagh">Fermanagh</option>
        <option value="Galway">Galway</option>
        <option value="Kerry">Kerry</option>
        <option value="Kildare">Kildare</option>
        <option value="Kilkenny">Kilkenny</option>
        <option value="Laois">Laois</option>
        <option value="Leitrim">Leitrim</option>
        <option value="Limerick">Limerick</option>
        <option value="Longford">Longford</option>
        <option value="Louth">Louth</option>
        <option value="Mayo">Mayo</option>
        <option value="Meath">Meath</option>
        <option value="Monaghan">Monaghan</option>
        <option value="Offaly">Offaly</option>
        <option value="Roscommon">Roscommon</option>
        <option value="Sligo">Sligo</option>
        <option value="Tipperary">Tipperary</option>
        <option value="Tyrone">Tyrone</option>
        <option value="Waterford">Waterford</option>
        <option value="Westmeath">Westmeath</option>
        <option value="Wexford">Wexford</option>
        <option value="Wicklow">Wicklow</option>
    </select>
        </div>
    </div>

    <br>

     <div class="descriptionContainer">
        <label for="description">Description</label>
        <textarea id="description" name="description" placeholder="Tell us about your business..." rows="10"></textarea>
     </div>      


     <div class="services">
        <div>
            <div><label for="service1">Service 1</label></div>
            <div><textarea id="price1" name="price1" placeholder="price (in €)"></textarea></div>
        </div>
        <div>
            <div><label for="service2">Service 2</label></div>
            <div><textarea id="price2" name="price2" placeholder="price (in €)"></textarea></div>
        </div>
        <div>
            <div><label for="service3">Service 3</label></div>
            <div><textarea id="price3" name="price3" placeholder="price (in €)"></textarea></div>
        </div>
     </div>

    
        <div class="descriptions">

            <div class="desc1">
            <label for="desc1">Service 1 Description:</label>
            <textarea id="description1" name="description1" placeholder="Describe this service..."></textarea>
        </div>

        <div class="desc2">
            <label for="desc2">Service 2 Description:</label>
            <textarea id="description2" name="description2" placeholder="Describe this service..."></textarea>
        </div>

        <div class="desc3">
            <label for="desc3">Service 3 Description:</label>
            <textarea id="description3" name="description3" placeholder="Describe this service..."></textarea>
        </div>
     </div>


     <div class="post">
         <button class="postButton">Post now</button>
     </div>



 </form>















   
<script>
    document.getElementById("imageUpload").addEventListener("change", function(event) {
    const file = event.target.files[0];
    const errorMessage = document.getElementById("error-message");

    if (file) {
        const img = new Image();
        img.src = URL.createObjectURL(file);
        
        img.onload = function() {
            if (img.width > 300 || img.height > 300) {
                errorMessage.textContent = "Image must be 300x300px or smaller.";
                event.target.value = ""; // Clear file input
            } else {
                errorMessage.textContent = ""; // Clear error
            }
        };
    }
});
</script>

</body>
</html>