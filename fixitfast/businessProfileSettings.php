
<?php
session_start();
include("connection.php");
include("functions.php");

$user_data = check_login($con);
//echo '<pre>';
//print_r($business_data);
//echo '</pre>';

$stmt = $con->prepare("SELECT * FROM businesses WHERE owner_id=?");
$stmt->bind_param("i", $user_data['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$business_data = $result->fetch_assoc();
//print_r($business_data);

//echo '<pre>';
//print_r($business_data);
//echo '</pre>';

//echo($business_data['description']);

$updateMessage = "";

$categoryOptions = "";
try {
    $catQuery = "SELECT category_id, category_name FROM service_categories";
    $catResult = mysqli_query($con, $catQuery);
    
} catch (mysqli_sql_exception $e) {
    die("Error fetching categories: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $ownerName = trim($_POST['owner_name']);
    $businessName = trim($_POST['business_name']);
    $county = trim($_POST['county']);
    $category = trim($_POST['category']);
    $descr = trim($_POST['description']);
    $phoneNumber = trim($_POST['phone_no']);
    $userName = trim($_POST['user_name']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (!empty($ownerName) && !empty($businessName) && !empty($county) && !empty($category) && !empty($descr) 
        && !empty($phoneNumber) && !empty($userName)) {
        try {
            
            if (!empty($password)) {
                if ($password !== $confirmPassword) {
                    $updateMessage = "Passwords do not match.";
                }
            }

            if (empty($updateMessage)) {
                
                $stmt = $con->prepare("UPDATE businesses SET owner_name=?, business_name=?, county=?, category=?,
                description=?, phone_no=?, user_name=? WHERE business_id=?");
                
                $stmt->bind_param("sssssssi", $ownerName, $businessName, $county, $category, $descr,
                $phoneNumber, $userName, $business_data['business_id']);
                $stmt->execute();
                $stmt->close();

                // Update user_name, first_name, and county in users table
                $stmt = $con->prepare("UPDATE users SET   user_name=?, first_name=?, county=? WHERE user_id=?");
                $stmt->bind_param("sssi", $userName, $ownerName, $county, $business_data['owner_id']);
                $stmt->execute();
                $stmt->close();



                
                if (!empty($password)) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $con->prepare("UPDATE businesses SET password=? WHERE business_id=?");
                    $stmt->bind_param("si", $hashedPassword, $business_data['business_id']);
                    $stmt->execute();
                    $stmt->close();

                    // Update password in users table
                    $stmt = $con->prepare("UPDATE users SET password=? WHERE user_id=?");
                    $stmt->bind_param("si", $hashedPassword, $business_data['owner_id']);
                    $stmt->execute();
                    $stmt->close();
                }

                
                $stmt = $con->prepare("SELECT * FROM businesses WHERE business_id=?");
                $stmt->bind_param("i", $business_data['business_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $business_data = $result->fetch_assoc();
                $stmt->close();

                $_SESSION['business_data'] = $business_data;
                $updateMessage = "Profile updated successfully!";
            }
        } catch (mysqli_sql_exception $e) {
            die("Database error: " . $e->getMessage());
        }
    } else {
        $updateMessage = "Please fill out all fields.";
    }
}
?>
            
            
            
            
    

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Profile Settings</title>
    <link rel="stylesheet" href="css/businessProfileSettings.css">
</head>
<body>

    <div class="container">
        <div class="sidebar">
            <div class="input-group">
                <a href="index.php" class="button back-button">Back to Home</a>
            </div>

            <ul>
                <li class="active"><a href="businessProfileSettings.php">Profile</a></li>
                <li><a href="editAds.php">Listings</a></li>
                <li><a href="logout.php">Logout</a></li>
             
            </ul>
        </div>

        <div class="profile-section">
            

            

            <div class="profile-edit">
            <h1>Business Profile</h1>
            <?php if (!empty($updateMessage)) { ?>
                <p class="success-message"><?php echo htmlspecialchars($updateMessage); ?></p>
            <?php } ?>
                

                <form method="post">
                    <div class="input-group">
                        <label for="business_name">Edit Business Name:</label>
                        <input type="text" id="business_name" name="business_name" value="<?php echo htmlspecialchars($business_data['business_name']); ?>">
                    </div>

                    <div class="input-group">
                        <label for="owner_name">Edit Owner Name:</label>
                        <input type="text" id="owner_name" name="owner_name" value="<?php echo htmlspecialchars($business_data['owner_name']); ?>">
                    </div>

    

                    <div>
                        <label for="county">County:</label><br>
                        <select class="dropdown" id="county" name="county" required>
                            <option value="" disabled>Select a County</option>
                            <?php
                            $counties = [
                                "Antrim", "Armagh", "Carlow", "Cavan", "Clare", "Cork", "Derry", "Donegal", 
                                "Down", "Dublin", "Fermanagh", "Galway", "Kerry", "Kildare", "Kilkenny", 
                                "Laois", "Leitrim", "Limerick", "Longford", "Louth", "Mayo", "Meath", "Monaghan", 
                                "Offaly", "Roscommon", "Sligo", "Tipperary", "Tyrone", "Waterford", "Westmeath", 
                                "Wexford", "Wicklow"
                            ];

                            foreach ($counties as $countyOption) {
                                // Check if the county is the one the user has currently selected
                                $selected = ($countyOption == $business_data['county']) ? 'selected' : '';
                                echo "<option value='$countyOption' $selected>$countyOption</option>";
                            }
                            ?>
                        </select>
                    </div>


                    <div class="input-group">
                        <label for="phone_no">Edit Phone Numer:</label> 
                        <input type="text" id="phone_no" name="phone_no" value="<?php echo htmlspecialchars($business_data['phone_no']); ?>">
                    </div>


                    <div>
                        <label for="category">Edit Category:</label> 
                        <select class="dropdown" id="category" name="category" required>
                            <option value="" disabled>Select a Category</option>
                            <?php while ($row = mysqli_fetch_assoc($catResult)): ?>
                                <?php
                                    $selected = ($row['category_id'] == $business_data['category']) ? 'selected' : '';
                                ?>
                                <option value="<?php echo htmlspecialchars($row['category_id']); ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($row['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>


                    <div class="input-group">
                        <label for="user_name">Edit Username:</label> 
                        <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($business_data['user_name']); ?>">
                    </div>

                    <div class="desc">
                        <label for="description"> Edit Description</label> 
                        <textarea  id="description" name="description" placeholder="Change your description..."
                        ><?php echo htmlspecialchars($business_data['description']); ?></textarea>


                    </div>




                    <div class="input-group">
                        <label for="password">Edit Password:</label>
                        <input type="password" id="password" name="password" placeholder="*****">
                    </div>

                    <div class="input-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="*****">
                    </div>

                    <div class="input-group">
                        <input type="submit" value="Save Changes" class="button">
                    </div>

                    

                    
                </form>
            </div>
        </div>
    </div>

                  

</body>
</html>
