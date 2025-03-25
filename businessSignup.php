<?php
session_start();
include("connection.php");
include("functions.php");
//echo("HI");
$text = "Hi";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $bName = $_POST['bName'];
    $oName = $_POST["oName"];
    $phoneN = $_POST['phoneN']
    $county = $_POST['county'];
    $category = $_POST['category'];
    $dess = $_POST['dess']
    $uName = $_POST['uName'];
    $password = $_POST['password'];
    //$fName =$_POST['fName'];
    //$lName = $_POST['lName'];
    

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
    <title>Business SignUp</title>
</head>
<body>
    <H1>Business SignUp</H1>


    <form action="" method = "post">

    <table>
        <tr>
            <td>
                <label for="bName"> Business Name:</label><br>
                <input type="text" id="bName" name="bName" value="Enter">
            </td>
            <td>
                 <label for="oName"> Owner Name:</label><br>
                    <input type="text" id="oName" name="oName" value="Enter">
            </td>
            <td>
                <label for="phoneN">phone Number:</label><br>
                <input type="text" id="phoneN" name="phoneN" value="Enter">

            </td>
        </tr>

        <tr>
            
                
            <td>
                <label for="county">County:</label><br>
                <input type="text" id="county" name="county" value="Enter">
            </td>
            <td>
                <label for="category">Category:</label><br>
                <input type="text" id="category" name="category" value="Enter">
            </td>
            <td rowspan = "2">
                <label for="dess">Description:</label><br>
                <input type="text" id="dess" name="dess" value="Enter">
            </td>
        </tr>

        <tr>
            <td>
                <label for="busUName">User Name:</label><br>
                <input type="text" id="uName" name="busUNname" value="">

            </td>
            <td>
                <label for="password">Password:</label><br>
                <input type="text" id="password" name="password" value="">

            </td>
            <td>
                
            </td>
        </tr>
        <tr>
            <td>

            </td>
            <td>
                <label for="password">Confirm Password:</label><br>
                <input type="text" id="cPassword" name="cPassword" value="">
            </td>
        </tr>
        <tr>
            <td>

            </td>
            <td>
                <input type="submit" value="SignUp">
            </td>
        </tr>
        
        
        
       
       
       
        
       
        
        

        
    </table>
    </form>

    <a href="Login.php">
        <button class = "loginBtn" type = "button">Login</button>
    </a>

    
</body>
</html>