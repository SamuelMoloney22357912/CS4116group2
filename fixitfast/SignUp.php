<?php
session_start();
include("connection.php");
include("functions.php");
//echo("HI");
//$text = "Hi";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $uName = $_POST['uName'];
    $password = $_POST['password'];
    $fName =$_POST['fName'];
    $lName = $_POST['lName'];
    $county = $_POST['county'];
    $cPassword = $_POST['ConPassword'];

    $checkQuery = "SELECT * FROM users WHERE user_name = '$uName' LIMIT 1";
    

    if($password != $cPassword){
        $matchErr = "Passwords don't match";
        echo($matchErr);
    }else{


        if(!empty($uName) && !empty($fName) && !empty($lName) && !empty($password)){

            $result = mysqli_query($con, $checkQuery);
            if(mysqli_num_rows($result) > 0){
                $userEx = "Username is taken";
                echo($userEx);
            }else{
                echo("inside if");
                try{
                    $query = "insert into Users (first_name,last_name,user_name,password,county) values('$fName','$lName','$uName','$password','$county')";
                    mysqli_query($con, $query);

                    

                }catch(mysqli_sql_exception $e){
                    die("Database insert error: " . $e->getMessage());
                }
                header("Location: Login.php");
                    die();
                
    
              

            }
            //save to database
            //$user_id = random_num(20);
            
        }else{
            $eMessage = "Plesse fill out all fields";
            //echo "Plesse enter a valid name and password";
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
    <link rel="stylesheet" href="./css/signup.css">
    <title>SignUp page</title>
</head>
<body>


<div class = container>
    <H1>SignUp page</H1>
    

    <form action="" method = "post">

        <table class = "fields">
            <tr>
                <td>
                    <label for="fName"> First Name:</label><br>
                    <input type="text" id="fName" name="fName" value="Enter">

                </td>
                <td>
                    <label for="lName"> Last Name:</label><br>
                    <input type="text" id="lName" name="lName" value="Enter">

                </td>
            </tr>

            <tr>
                <td>
                    <label for="uName">User Name:</label><br>
                    <input type="text" id="uName" name="uName" value="Enter">

                </td>
                <td>
                    <label for="password">Password:</label><br>
                    <input type="text" id="password" name="password" value="Enter">

                </td>
            </tr>

            <tr>
                <td>
                    <label for="county">County:</label><br>
                            <select class = "dropdown" id="county" name="county" required>
                                <option value="" disabled selected>Select a County</option>
                                <option value="Antrim">Antrim</option>
                                <option value="Armagh">Armagh</option>
                                <option value="Carlow">Carlow</option>
                                <option value="Cavan">Cavan</option>
                                <option value="Clare">Clare</option>
                                <option value="Cork">Cork</option>
                                <option value="Derry">Derry</option>
                                <option value="Donegal">Donegal</option>
                                <option value="Down">Down</option>
                                <option value="Dublin">Dublin</option>
                                <option value="Fermanagh">Fermanagh</option>
                                <option value="Galway">Galway</option>
                                <option value="Kerry">Kerry</option>
                                <option value="Kildare">Kildare</option>
                                <option value="Kilkenny">Kilkenny</option>
                                <option value="Laois">Laois</option>
                                <option value="Leitrim">Leitrim</option>
                                <option value="Limerick">Limerick</option>
                                <option value="Longford">Longford</option>
                                <option value="Louth">Louth</option>
                                <option value="Mayo">Mayo</option>
                                <option value="Meath">Meath</option>
                                <option value="Monaghan">Monaghan</option>
                                <option value="Offaly">Offaly</option>
                                <option value="Roscommon">Roscommon</option>
                                <option value="Sligo">Sligo</option>
                                <option value="Tipperary">Tipperary</option>
                                <option value="Tyrone">Tyrone</option>
                                <option value="Waterford">Waterford</option>
                                <option value="Westmeath">Westmeath</option>
                                <option value="Wexford">Wexford</option>
                                <option value="Wicklow">Wicklow</option>
                            </select>

                </td>
                <td>
                    <label for="ConPassword">Confirm Password:</label><br>
                    <input type="text" id="ConPassword" name="ConPassword" value="Enter">

                </td>
            </tr>
            <tr>

            </tr>
            <tr>
                <td>
                    <input type="submit" value="SignUp" class = "signupBtn">
                </td>
            </tr>
        </table>
        
        
       
       
       
        
        
        

        <p class = "message"> <?php echo($eMessage.$userEx.$matchErr); 
         ?></p>


        
    </form>

    <div class = "navBtn">
        <a href="login.php">Login</a><br>

         <a href="businessSignup.php">become a seller</a>
    </div>
    

</div>
    
    
</body>
</html>