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
        

        $query = "select * from Users where user_name = '$uName' limit 1";
        $result = mysqli_query($con, $query);

        if($result){
            echo("result");
            if($result && mysqli_num_rows($result) > 0){ 
                $user_data = mysqli_fetch_assoc($result);
                if($user_data['password']=== $password){

                    $_SESSION['user_id'] = $user_data['user_id'];

                    header("Location: index.php");
                    die();

                }
            }
            $incorectInfo = "Incorect username or password";
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

   
    
    <H1 class = "tiitle">Login page</H1>

    <form method = "post">
        <label for="uName">User Name:</label><br>
        <input type="text" id="uName" name="uName" value="Enter"><br>
        <label for="password">Password:</label><br>
        <input type="text" id="password" name="password" value="Enter"><br><br>
        <input type="submit" value="Login">
    </form>

    <p class = "errorMessage">
        <?php
        echo($emptyFields);
        echo($incorectInfo);
        ?>
    </p>

    <a href="SignUp.php">
         <button class = "signup_button" type = "button">SignUp</button>
    </a>

    <a href="businessSignup.php">
            <button class = "Bis_signup_button" type = "button">Become a seller</button>
    </a>

    

</div>
    <a href="SignUp.php"> Go to SignUp</a>

    <a href="index.html">Go home</a>
    
</body>
</html>