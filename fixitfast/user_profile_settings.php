<?php
session_start();
include("connection.php");
include("functions.php");

$user_data = check_login($con);

$status = isset($_GET['status']) ? $_GET['status'] : '';


if($_SERVER['REQUEST_METHOD'] == "POST"){
    $fName = $_POST['first_name'];
    $lName = $_POST['last_name'];
    $county = $_POST['county'];
    $password = $_POST['password'];

    if(!empty($fName) && !empty($lName)){

        try{
            $query = "UPDATE users SET first_name='$fName', last_name='$lName', county='$county'  WHERE user_id='{$user_data['user_id']}'";
            mysqli_query($con, $query);

            $Update = "Updated";
        }catch(mysqli_sql_exception $e){
            die("Database insert error: " . $e->getMessage());
    }

    }else{
        echo("Plese fill out all info");

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Settings</title>
    <link rel="stylesheet" href="css/profile_settings.css">
</head>
<body>

    <div class="container">
        <div class="sidebar">
            <ul>
                <li class="active"><a href="user_profile_settings.php">Profile</a></li>
                <li><a href="user_listings.php">Listings</a></li>
                <li><a href="admin.php">Admin</a></li>
            </ul>
        </div>

        <div class="profile-section">
            <h1>User Profile</h1>
            
            <?php
            if ($status == 'success') {
                echo '<p class="success-message">Profile updated successfully!</p>';
            }
            ?>
            
            <div class="profile-edit">
                <div class="profile-image">
                    <img src="images/<?php echo $user_data['profile_picture']; ?>" alt="Profile Picture">
                    <a href="edit_picture.php">Edit Profile Picture</a>
                </div>

                <form method="post">
                    <div class="input-group">
                        <label for="first_name">Edit First Name:</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo $user_data['first_name']; ?>">
                    </div>
                    <div class="input-group">
                        <label for="last_name">Edit Surname:</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo $user_data['last_name']; ?>">
                    </div>
                    <div class="input-group">
                        <label for="county">Edit County:</label>
                        <input type="text" id="county" name="county" value="<?php echo $user_data['county']; ?>">
                    </div>
                    <div class="input-group">
                        <label for="password">Edit Password:</label>
                        <input type="password" id="password" name="password">
                    </div>
                    <div class="input-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password">
                    </div>
                    <div class="save-btn">
                        <input type="submit" value="Save Changes">
                    </div>
                </form>

                    <p><?php echo($Update); ?></p>
            </div>
        </div>
    </div>

</body>
</html>
