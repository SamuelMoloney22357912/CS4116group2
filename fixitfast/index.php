<?php
    session_start();
    include("connection.php");
    include("functions.php");

    $user_data = check_login($con);

    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

  
    

    
</head>
<body>
    
   

        <H1>Login</H1>
        <a href="Login.php">Login</a>
        <br>
        <a href="SignUp.php">SignUp</a>
        <br>
        <a href="logout.php">Logout</a>


    <select>
        <option value="default" selected disabled>Select an option</option>
        <option value="apple">Apple</option>
        <option value="banana">Banana</option>
        <option value="orange">Orange</option>
    </select>
    

   
    
</body>
</html>