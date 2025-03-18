<?php
session_start();
include("connection.php");
include("functions.php");
//echo("HI");
$text = "Hi";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $uName = $_POST['uName'];
    $password = $_POST['password'];
    $fName =$_POST['fName'];
    $lName = $_POST['lName'];
    $county = $_POST['county'];

    if(!is_numeric($uName) && !empty($uName) && !empty($fName)){
        //save to database
        //$user_id = random_num(20);
        echo("inside if");

        $query = "insert into Users (first_name,last_name,user_name,password,county) values('$fName','$lName','$uName','$password','$county')";
        mysqli_query($con, $query);

        header("Location: Login.php");
        die();
    }else{
        $eMessage = "Plesse enter a valid name and password";
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
    <title>SignUp page</title>
</head>
<body>
    <H1>SignUp page</H1>
    <p><?php echo($text);?></p>

    <form action="" method = "post">
        <label for="fName"> Firts Name:</label><br>
        <input type="text" id="fName" name="fName" value="Enter"><br>
        <label for="lName"> Last Name:</label><br>
        <input type="text" id="lName" name="lName" value="Enter"><br>
        <label for="uName">User Name:</label><br>
        <input type="text" id="uName" name="uName" value="Enter"><br>
        <label for="county">County:</label><br>
        <input type="text" id="county" name="county" value="Enter"><br>
        <label for="password">Password:</label><br>
        <input type="text" id="password" name="password" value="Enter"><br>
        <label for="ConPasword">Confirm Password:</label><br>
        <input type="text" id="ConPasword" name="ConPasword" value="Enter"><br><br>
        
        <input type="submit" value="SignUp">

        <p class = "message"> <?php echo($eMessage); ?></p>
        
    </form>
    <a href="login.php"></a>

    <a href="index.php">Go home</a>
    
</body>
</html>