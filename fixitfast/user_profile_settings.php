<?php
session_start();
include("connection.php");
include("functions.php");

$user_data = check_login($con);

$updateMessage = "";
$profilePicture = 'uploads/profile_images/default-profile.jpg'; // Default profile picture

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $fName = trim($_POST['first_name']);
    $lName = trim($_POST['last_name']);
    $county = trim($_POST['county']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $targetDir = "uploads/profile_images/";
        $imageFileType = pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
        $targetFile = $targetDir . $user_data['user_id'] . "." . $imageFileType;

        // Check if the file is an image
        if (getimagesize($_FILES["profile_picture"]["tmp_name"]) !== false) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
                // Update database to reflect the profile picture exists
                $stmt = $con->prepare("UPDATE users SET profile_pic=? WHERE user_id=?");
                $stmt->bind_param("si", $targetFile, $user_data['user_id']);
                $stmt->execute();
                $stmt->close();
            } else {
                $updateMessage = "Error uploading file.";
            }
        } else {
            $updateMessage = "The file is not an image.";
        }
    }

    // Update other profile information
    if (!empty($fName) && !empty($lName) && !empty($county)) {
        try {
            $stmt = $con->prepare("UPDATE users SET first_name=?, last_name=?, county=? WHERE user_id=?");
            $stmt->bind_param("sssi", $fName, $lName, $county, $user_data['user_id']);
            $stmt->execute();
            $stmt->close();

            if (!empty($password)) {
                if ($password === $confirm_password) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $con->prepare("UPDATE users SET password=? WHERE user_id=?");
                    $stmt->bind_param("si", $hashedPassword, $user_data['user_id']);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $updateMessage = "Passwords do not match.";
                }
            }

            $stmt = $con->prepare("SELECT * FROM users WHERE user_id=?");
            $stmt->bind_param("i", $user_data['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();
            $stmt->close();

            $_SESSION['user_data'] = $user_data;

            if (empty($updateMessage)) {
                $updateMessage = "Profile updated successfully!";
            }
        } catch (mysqli_sql_exception $e) {
            die("Database error: " . $e->getMessage());
        }
    } else {
        $updateMessage = "Please fill out all fields.";
    }
}

// Check if the user has a profile picture in the database
if (!empty($user_data['profile_pic']) && file_exists($user_data['profile_pic'])) {
    $profilePicture = $user_data['profile_pic'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Settings</title>
    <link rel="stylesheet" href="css/user_profile_settings.css">
</head>
<body>

<div class="container">
    <div class="sidebar">
        <div class="input-group">
            <a href="index.php" class="button back-button">Back to Menu</a>
        </div>

        <ul>
            <li class="active"><a href="user_profile_settings.php">Profile</a></li>
            <li><a href="user_listings.php">Listings</a></li>
            <li><a href="admin.php">Admin</a></li>
        </ul>
    </div>

    <div class="profile-section">
        <h1>User Profile</h1>

        <?php if (!empty($updateMessage)) { ?>
            <p class="success-message"><?php echo htmlspecialchars($updateMessage); ?></p>
        <?php } ?>

        <form method="post" enctype="multipart/form-data">

            <div class="profile-image">
                <img src="<?php echo $profilePicture; ?>" alt="Profile Picture">
                <p><?php echo htmlspecialchars($user_data['first_name'] . " " . $user_data['last_name']); ?></p>
            </div>

            <div class="uploadContainer">
                <label for="profile_picture">Upload a Profile Image (MAX 300x200)</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                <p id="errorMessage" class="error"></p>
            </div>

            <div class="input-group">
                <label for="first_name">Edit First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name']); ?>">
            </div>

            <div class="input-group">
                <label for="last_name">Edit Surname:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name']); ?>">
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
                        $selected = ($countyOption == $user_data['county']) ? 'selected' : '';
                        echo "<option value='$countyOption' $selected>$countyOption</option>";
                    }
                    ?>
                </select>
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

</body>
</html>
