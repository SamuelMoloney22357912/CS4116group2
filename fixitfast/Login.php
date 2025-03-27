<?php
session_start();
include("connection.php");
include("functions.php");


if($_SERVER['REQUEST_METHOD'] == "POST"){
    $uName = trim($_POST['uName']);
    $password = $_POST['password'];

    if(!is_numeric($uName) && !empty($uName)){
        $query = "SELECT * FROM Users WHERE user_name = ? LIMIT 1";
        
        if ($stmt = $con->prepare($query)) {
            $stmt->bind_param("s", $uName);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows > 0){ 
                $user_data = $result->fetch_assoc();

                if(password_verify($password, $user_data['password'])){
                    $_SESSION['user_id'] = $user_data['user_id'];
                    header("Location: index.php");
                    exit();
                }
            }

            $incorectInfo = "Incorrect username or password";
        }
    } else {
        $emptyFields = "Please enter a valid username and password";
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
