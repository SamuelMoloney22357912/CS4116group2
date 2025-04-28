<?php
session_start();
$updateMessage = "";
if (isset($_SESSION['updateMessage'])) {
    $updateMessage = $_SESSION['updateMessage'];
    unset($_SESSION['updateMessage']);
}
include("connection.php");
include("functions.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_data = check_login($con);
$hideButton = ($user_data && $user_data['admin'] == 1);
$admin_id = $user_data['user_id'];

//$updateMessage = "";
try{

    $stmt = $con->prepare("
    SELECT b.business_id, b.business_name
    FROM businesses b
    JOIN users u ON b.owner_id = u.user_id
    WHERE u.ban = '0'");
    $stmt->execute();

        // Get result
    $result = $stmt->get_result();
    $businessOptions = "";
    while ($row = $result->fetch_assoc()) {
        $business_id = htmlspecialchars($row['business_id']);
        $business_name = htmlspecialchars($row['business_name']);
        $businessOptions .= "<option value='$business_id'>$business_name</option>";
    

    } 

    $stmt = $con->prepare("SELECT user_id , user_name FROM users WHERE business = '0' AND ban = '0'");
    $stmt->execute();
    $result1= $stmt->get_result();
    $userOptions = "";

    while ($row1 = $result1->fetch_assoc()) {
        $user_id = htmlspecialchars($row1['user_id']);
        $user_name = htmlspecialchars($row1['user_name']);
        $userOptions .= "<option value='$user_id'>$user_name</option>";
    
    }

    $stmt = $con->prepare("SELECT service_id , name FROM services");
    $stmt->execute();
    $result1= $stmt->get_result();
    $adOptions = "";

    while ($row1 = $result1->fetch_assoc()) {
        $ad_id = htmlspecialchars($row1['service_id']);
        $ad_name = htmlspecialchars($row1['name']);
        $adOptions .= "<option value='$ad_id'>$ad_name</option>";
    
    }

    $stmt = $con->prepare("SELECT review_id , comment FROM reviews");
    $stmt->execute();

        // Get result
    $result = $stmt->get_result();
    $reviewOptions = "";
    while ($row = $result->fetch_assoc()) {
        $review_id = htmlspecialchars($row['review_id']);
        $review_comment = htmlspecialchars($row['comment']);
        $reviewOptions .= "<option value='$review_id'>$review_comment</option>";
    

    } 

    $stmt = $con->prepare("SELECT message_id , content FROM message");
    $stmt->execute();

        // Get result
    $result = $stmt->get_result();
    $messageOptions = "";
    while ($row = $result->fetch_assoc()) {
        $message_id = htmlspecialchars($row['message_id']);
        $message_content = htmlspecialchars($row['content']);
        $messageOptions .= "<option value='$message_id'>$message_content</option>";
    

    } 
}catch(mysqli_sql_exception $e){
    die("Database Fetch Error: " . $e->getMessage());

}



if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['banBtnB'])){
        $business_id = $_POST['busName'];
        $stmt = $con->prepare("SELECT owner_id FROM businesses WHERE business_id =?");
        $stmt->bind_param("i", $business_id);
        $stmt->execute();
        $stmt->bind_result($owner_id);
        if ($stmt->fetch()) {
            
            $stmt->close();
    
            if(!empty($business_id)){

            
                $banStmt = $con->prepare("UPDATE users SET ban = '1' WHERE user_id = ?");
                $banStmt->bind_param("i", $owner_id);
                $banStmt->execute();
                //$banStmt->close();
        
                $_SESSION['updateMessage'] = "Business user banned successfully.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
                } else {
                    
                    $updateMessage = "No user was updated. Check if user ID is correct.";
                }
            }else{
                $updateMessage = "Please select a user to ban.";
            }



        


    }

    if (isset($_POST['banBtnU'])){
        $ban = 1;
        $user_id = $_POST['uName'];
        if (!empty($user_id)) {
            $stmt = $con->prepare("UPDATE users SET ban = ? WHERE user_id = ?");
            $stmt->bind_param("ii", $ban, $user_id);
            $stmt->execute();
    
            if ($stmt->affected_rows > 0) {
                $_SESSION['updateMessage'] = "User banned successfully.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $updateMessage = "No user was updated. Check if user ID is correct. User might already be baned";
            }
    
            
        } else {
            $updateMessage = "Please select a user to ban.";
        }

    }

    if (isset($_POST['removeAdBtn'])){
        $service_id = $_POST['adName'];
        $targetType = "ad";
        $type = "removed";
        $reason = "Inappropriate content";
        try{
            $stmt = $con->prepare("INSERT INTO admin_action(admin_id,target_id,target_type,action_taken,reason) VALUES (?,?,?,?,?)");
            $stmt->bind_param("iisss", $admin_id, $service_id,$targetType,$type,$reason );
            $stmt->execute();
            $stmt->close();

            $deleteStmt = $con->prepare("DELETE FROM services WHERE service_id = ?");
            $deleteStmt->bind_param("i", $service_id);
            $deleteStmt->execute();
            if ($deleteStmt->execute()) {
                $_SESSION['updateMessage'] = "Ad removed and action logged successfully.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $updateMessage = "Error removing ad.";
            }
            $deleteStmt->close();



        }catch(mysqli_sql_exception $e){
            die("Database insert error: " . $e->getMessage());
        }
        
    }

    if (isset($_POST['removeRevBtn'])){
        $review_id = $_POST['review'];
        $targetType = "review";
        $type = "removed";
        $reason = "Inappropriate content";

        try{
            $stmt = $con->prepare("INSERT INTO admin_action(admin_id,target_id,target_type,action_taken,reason) VALUES (?,?,?,?,?)");
            $stmt->bind_param("iisss", $admin_id, $review_id,$targetType,$type,$reason );
            $stmt->execute();
            $stmt->close();

            $deleteStmt = $con->prepare("DELETE FROM reviews WHERE review_id = ?");
            $deleteStmt->bind_param("i", $review_id);
            $deleteStmt->execute();
            if ($deleteStmt->execute()) {
                $_SESSION['updateMessage'] = "Review removed and action logged successfully.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
                //header("Location: " . $_SERVER['PHP_SELF']);
                //exit;
               
                
            } else {
                echo "Error removing Review.";
            }
            $deleteStmt->close();


        }catch(mysqli_sql_exception $x){
            
            die("Database insert error: " . $x->getMessage());
        }

    }

    if (isset($_POST['removeMessBtn'])){
        $message_id = $_POST['message'];
        $targetType = "message";
        $type = "removed";
        $reason = "Inappropriate content";

        try{
            $stmt = $con->prepare("INSERT INTO admin_action(admin_id,target_id,target_type,action_taken,reason) VALUES (?,?,?,?,?)");
            $stmt->bind_param("iisss", $admin_id, $message_id,$targetType,$type,$reason );
            $stmt->execute();
            $stmt->close();

            $deleteStmt = $con->prepare("DELETE FROM message WHERE message_id = ?");
            $deleteStmt->bind_param("i", $message_id);
            $deleteStmt->execute();
            if ($deleteStmt->execute()) {
                $_SESSION['updateMessage'] = "Message removed and action logged successfully.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
                //header("Location: " . $_SERVER['PHP_SELF']);
                //exit;
               
                
            } else {
                $updateMessage = "Error removing Message.";
            }
            $deleteStmt->close();


        }catch(mysqli_sql_exception $y){
            
            die("Database insert error: " . $y->getMessage());
        }

    }

    
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="css/adminDashboard.css">
</head>
<body>

    <div class="container">
        <div class="sidebar">
            <div class="input-group">
                <a href="index.php" class="button back-button">Back to Home</a>
            </div>

            <ul>
                <li class="profileByn"><a href="user_profile_settings.php">Profile</a></li>
                <?php if ($hideButton): ?>
                <li class="active"><a href="adminDashboard.php">Admin</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <div class="adminActions">
            <h1>Admin Dashboard</h1>

            <div class = "message">
                <p><?php echo($updateMessage);?></p>
            </div>

            <form method = "post" >

            <div class = "selBusiness">
                <select name="busName" id="busName">
                    <option value=""> Select Business </option>
                    <?php echo $businessOptions; ?>
                </select>

                <button class = "banBtn" type = submit name = "banBtnB">Ban</button>

            </div>

            <div class = "selusers">
                <select name="uName" id="uName">
                    <option value=""> Select User </option>
                    <?php echo $userOptions; ?>
                </select>

                <button class = "banBtn" type = submit name = "banBtnU">Ban</button>

            </div>

            <div class = "selAd">
                <select name="adName" id="adName">
                    <option value=""> Select Ad </option>
                    <?php echo $adOptions; ?>
                </select>

                <button class = "removeAdBtn" type = submit name = "removeAdBtn">Remove</button>


            </div>

            <div class = "selRev">
                <select name="review" id="review">
                    <option value=""> Select Review </option>
                    <?php echo $reviewOptions; ?>
                </select>

                <button class = "removeRevBtn" type = submit name = "removeRevBtn">Remove</button>


            </div>

            <div class = "selMessage">
                <select name="message" id="message">
                    <option value=""> Select Message</option>
                    <?php echo $messageOptions; ?>
                </select>

                <button class = "removeMessBtn" type = submit name = "removeMessBtn">Remove</button>


            </div>

            </form>

            

            
        </div>
    </div>

</body>
</html>
