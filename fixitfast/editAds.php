<?php
session_start();
include("connection.php");
include("functions.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);




$user_data = check_login($con);


$updateMessage = "";

$user_id = $user_data['user_id'];
try{
    // Prepare the statement
    $stmt = $con->prepare("SELECT business_id FROM businesses WHERE owner_id = ?");
    $stmt->bind_param("i", $user_id); // "i" = integer
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $business_id = $row['business_id'];
        //echo "Business ID: " . $business_id;
    } else {
        echo "No business found for this user.";
    }

    $stmt = $con->prepare("SELECT * FROM services WHERE business_id = ?");
    $stmt->bind_param("i", $business_id);
    $stmt->execute();

    $ads = [];
    $result1 = $stmt->get_result();
    while ($row1 = $result1->fetch_assoc()) {
        $ads[] = $row1;
        //print_r($ads);
    }

    $selected_ad = null;


        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['ads'])) {
            $selected_ad_id = $_POST['ads'];

            $stmt = $con->prepare("SELECT * FROM services WHERE service_id = ?");
            $stmt->bind_param("i", $selected_ad_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $selected_ad = $row; // this will be used to populate fields
            }

            
        }

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // Save form submission
            if (isset($_POST['save_changes']) && isset($_POST['service_id'])) {
                $service_id = $_POST['service_id'];
                $title = $_POST['title'];
                $county = $_POST['county'];
                $description = $_POST['description'];
                $price1 = $_POST['price1'];
                $des1 = $_POST['des1'];
                $price2 = $_POST['price2'];
                $des2 = $_POST['des2'];
                $price3 = $_POST['price3'];
                $des3 = $_POST['des3'];
            
                // Handle Image Upload
                $imageUploaded = false;
                $imagePath = "";
            
                if (isset($_FILES['adImage']) && $_FILES['adImage']['error'] == 0) {
                    $targetDir = "uploads/";
                    if (!file_exists($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
            
                    $imageFileType = strtolower(pathinfo($_FILES["adImage"]["name"], PATHINFO_EXTENSION));
                    $allowedTypes = ["jpg", "jpeg", "png", "gif"];
            
                    if (in_array($imageFileType, $allowedTypes)) {
                        $imagePath = $targetDir . uniqid("img_", true) . "." . $imageFileType;
                        if (move_uploaded_file($_FILES["adImage"]["tmp_name"], $imagePath)) {
                            $imageUploaded = true;
                        } else {
                            die("Error moving uploaded file.");
                        }
                    } else {
                        die("Invalid image type. Only JPG, JPEG, PNG, and GIF are allowed.");
                    }
                }
            
                try {
                    if ($imageUploaded) {
                        $stmt = $con->prepare("UPDATE services 
                            SET name = ?, county = ?, description = ?, price1 = ?, price2 = ?, price3 = ?, description1 = ?, description2 = ?, description3 = ?, picture = ?
                            WHERE service_id = ?");
                        $stmt->bind_param("ssssssssssi", $title, $county, $description, $price1, $price2, $price3, $des1, $des2, $des3, $imagePath, $service_id);
                    } else {
                        $stmt = $con->prepare("UPDATE services 
                            SET name = ?, county = ?, description = ?, price1 = ?, price2 = ?, price3 = ?, description1 = ?, description2 = ?, description3 = ?
                            WHERE service_id = ?");
                        $stmt->bind_param("sssssssssi", $title, $county, $description, $price1, $price2, $price3, $des1, $des2, $des3, $service_id);
                    }
            
                    if ($stmt->execute()) {
                        $updateMessage = "Ad updated successfully!";
                    } else {
                        $updateMessage = "Error updating ad.";
                    }
            
                } catch (mysqli_sql_exception $x) {
                    die("Database fetch error: " . $x->getMessage());
                }
            }elseif(isset($_POST['remove_ad']) && isset($_POST['service_id'])){
                $service_id = $_POST['service_id'];

                $stmt = $con->prepare("DELETE FROM services WHERE service_id = ?");
                $stmt->bind_param("i", $service_id);

                if ($stmt->execute()) {
                    $updateMessage = "Ad removed successfully!";
                    $selected_ad = null; // Clear the ad info so the form resets
                } else {
                    $updateMessage = "Failed to remove ad.";
                }

            }
        }
        


    
   

   

    


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
    <title>Edit Ad</title>
    <link rel="stylesheet" href="css/editAds.css">
</head>
<body>

    <div class="container">
        <div class="sidebar">
            <div class="input-group">
                <a href="index.php" class="button back-button">Back to Menu</a>
            </div>

            <ul>
                <li class="businessSet"><a href="user_profile_settings.php">Profile</a></li>
                <li class = "active"> <a href="editAds.php">Edit Listings</a></li>
               

            </ul>
        </div>

        

        <div class="editAds">
            <h1>Edit Ad</h1>
            <div class = "message">
                <p class = "thkMsg"><?php echo($updateMessage)?></p>
            </div>

            <div class = "selectAd">
                <form method = "post">
                <select name="ads" id="">
                <option value=""> Select an Ad </option>

                <?php foreach ($ads as $ad): ?>
                    <option value="<?php echo htmlspecialchars($ad['service_id']); ?>" >
                    <?php //echo (isset($selected_ad_id) && $selected_ad_id == $ad['service_id']) ? 'selected' : ''; ?>
                    <?php echo htmlspecialchars($ad['name']); ?>
                    </option>
                <?php endforeach; ?>
               
                </select>

                <button class = "getAds" type="submit">Find Ad</button>

                </form>

                

            </div>

            <div class = "adInfo">
                <form method = "post" enctype="multipart/form-data">
                    <input type="hidden" name="service_id" value="<?php echo isset($selected_ad) ? htmlspecialchars($selected_ad['service_id']) : ''; ?>">
                    <label class = "image-upload">
                        <img class = " AdPic" src="<?= !empty($selected_ad['picture']) ? htmlspecialchars($selected_ad['picture']) : './images/ad_placeholder.png' ?>" alt="Upload Image Button" >
                        <input class = "PicBtn" type="file" id="adImage" name="adImage" accept="image/*">
                    </label>
                    <label for="title">Edit Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo isset($selected_ad) ? htmlspecialchars($selected_ad['name']) : ''; ?>">
                    <label for="county">Edit County:</label>
                    <select name="county" id="county">
                        <option value="<?php echo isset($selected_ad) ? htmlspecialchars($selected_ad['county']) : ''; ?>"><?php echo isset($selected_ad) ? htmlspecialchars($selected_ad['county']) : ''; ?></option>
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

                    <label for="description">Edit Description:</label>
                    <textarea name="description" id="description" rows="10"><?php echo isset($selected_ad) ? htmlspecialchars($selected_ad['description']) : ''; ?></textarea>

                    <label for="price1">Edit price1:</label>
                    <input type="text" id="price1" name="price1" value="<?php echo isset($selected_ad) ? htmlspecialchars($selected_ad['price1']) : ''; ?>">
                    <label for="des1">Edit Description 1:</label>
                    <textarea name="des1" id="des1" rows="10"><?php echo isset($selected_ad) ? htmlspecialchars($selected_ad['description1']) : ''; ?></textarea>

                    <label for="price2">Edit price 2:</label>
                    <input type="text" id="price2" name="price2" value="<?php echo isset($selected_ad) ? htmlspecialchars($selected_ad['price2']) : ''; ?>">
                    <label for="des2">Edit Description 2:</label>
                    <textarea name="des2" id="des2" rows="10"><?php echo isset($selected_ad) ? htmlspecialchars($selected_ad['description2']) : ''; ?></textarea>

                    <label for="price3">Edit price 3:</label>
                    <input type="text" id="price3" name="price3" value="<?php echo isset($selected_ad) ? htmlspecialchars($selected_ad['price3']) : ''; ?>">
                    <label for="des3">Edit Description 3:</label>
                    <textarea name="des3" id="des3" rows="10"><?php echo isset($selected_ad) ? htmlspecialchars($selected_ad['description3']) : ''; ?></textarea>
                    
                    

                

            </div>

            <div class = "buttons">
                <div class = "submit">
                    <button class = "submit" type="submit" name = "save_changes">save Changes</button>
                </div>
        
                <div class = "Remove">
                    <button class = "remove" type="submit" name = "remove_ad" id = "removeAdBtn" >Remove ad</button>
                </div>

            </div>

            </form>

            

            
    </div>

    <script>
    document.getElementById("removeAdBtn").addEventListener("click", function(event) {
        const confirmed = confirm("Are you sure you want to delete this ad? This action cannot be undone.");
        if (!confirmed) {
            event.preventDefault(); // Stop form from submitting
        }
    });
</script>

</body>
</html>
