<?php
session_start();
include("connection.php");
include("functions.php");

//$text = "Hi";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $uName = $_POST['uName'];
    $password = $_POST['password'];
    //$fName =$_POST['fName'];
    //$lName = $_POST['lName'];
    //$county = $_POST['county'];

    if(!is_numeric($uName) && !empty($uName)){
        //save to database
        //$user_id = random_num(20);
        

        $query = "SELECT * FROM users WHERE BINARY user_name = '$uName' LIMIT 1";
        $result = mysqli_query($con, $query);

        if($result){
            //echo("result");
            if($result && mysqli_num_rows($result) > 0){ 
                $user_data = mysqli_fetch_assoc($result);

                if($user_data['ban'] == '1'){
                    $bannedMessage = "You have been banned and cannot log in.";

                }else{
                    if(password_verify($password, $user_data['password'])){

                        $_SESSION['user_id'] = $user_data['user_id'];
    
                        header("Location: index.php");
                        die();
    
                    }else{
                        $incorectInfo = "Incorect username or password";
                    }

                }
                
            }else{
                $incorectInfo = "Incorect username or password";
            }
            
        }

        
    }else{
        $emptyFields = "Plesse enter a valid name and password";
        //echo "Plesse enter a valid name and password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login page</title>
    <link rel="stylesheet" href="./css/Login.css">
</head>
<body>
    
 <div class = "continer">

   
    
    <H1 class = "title">Login</H1>

    <form method = "post">
        <label for="uName">User Name:</label><br>
        <input type="text" id="uName" name="uName" placeholder="Enter"><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" placeholder="Enter"><br><br>
        <input class = "loginBtn" type="submit" value="Login">
    </form>

   

    <?php if (!empty($emptyFields)|| !empty($incorectInfo) || !empty($bannedMessage)): ?>
    <p class = "errorMessage"><?php echo($emptyFields.$incorectInfo.$bannedMessage); ?></p>
    <?php endif; ?>

    <div class = options>
        <a class = "signUpBtn" href="SignUp.php">
            SignUp
        </a>

        <a class = "bSellerBtn" href="businessSignup.php">
                Become a seller
        </a>
    </div>

    

</div>
   
    
</body>
</html>