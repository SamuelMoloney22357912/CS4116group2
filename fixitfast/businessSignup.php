<?php
session_start();
include("connection.php");
include("functions.php");
//echo("HI");
$text = "Hi";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $bName = $_POST['bName'];
    $oName = $_POST["oName"];
    $phoneN = $_POST['phoneN'];
    $county = $_POST['county'];
    $category = $_POST['category'];
    $dess = $_POST['dess'];
    $busUName = $_POST['busUName'];
    $password = $_POST['password'];
    $cPassword = $_POST['cPassword'];
    $oId = 1;
    $business = 1;
    //for test purposes.
    $lName = "";

    $checkUserMatchQuery = "SELECT * FROM businesses WHERE user_name = '$busUName' LIMIT 1";
    $checkUserMatch = "SELECT * FROM users WHERE user_name = '$busUName' LIMIT 1";


    if($password != $cPassword){
        $matchErr = "The passwords do not match";
        echo($matchErr);
    }else{
        if(!empty($bName) && !empty($oName) && !empty($phoneN) && !empty($dess) && !empty($busUName) && !empty($password)){
            $result2 = mysqli_query($con, $checkUserMatch);
            if(mysqli_num_rows($result2) > 0){
                $userEx = "Username is taken";
            }else{
                try{
                    $userQuery = "insert into Users (first_name,last_name,user_name,password,county,business) values('$oName','$lName','$busUName','$password','$county','$business')";
                    mysqli_query($con, $userQuery);
                }catch(mysqli_sql_exception $x){
                    die("Database insert error for user table: " . $x->getMessage());
                }
            }
            try{

                $ownerIdQuery = "SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1";
                $ownerIdResult = mysqli_query($con, $ownerIdQuery);
                $ownerRow = mysqli_fetch_assoc($ownerIdResult);
                $ownerId = $ownerRow['user_id'];
            sleep(1);

            }catch(mysqli_sql_exception $z){
                die("Error selecting user Id: " . $z->getMessage());

            }




            
            //save to database
            $result = mysqli_query($con, $checkUserMatchQuery);
            if(mysqli_num_rows($result) > 0){
                $userEx = "Username is taken";
                echo($userEx);
            }else{
                echo("inside if");
                try{
                    $query = "insert into businesses (owner_id,owner_name,business_name,county,category,description,phone_no,user_name,password) values('$ownerId','$oName','$bName','$county','$category','$dess','$phoneN','$busUName','$password')";
                    mysqli_query($con, $query);
                    echo("Sucseful");
                }catch(mysqli_sql_exception $e){
                    die("Database insert error: " . $e->getMessage());
            }
            
            sleep(1);
            header("Location: Login.php");
            die();
            }
            
            
        }else{
            $eMessage = "Plesse fill out all fields";
            echo($eMessage);
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
    <link rel="stylesheet" href="./css/businessSignup.css">
    <title>Business SignUp</title>
</head>
<body>

<div class = container>
    <H1>Business SignUp</H1>


    <form action="" method = "post">

    <table class = "fields">
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
                <select class = "dropdown1" id="county" name="county" required>
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
                <label for="category">Category:</label><br>
                <select class = "dropdown2" id="category" name="category" required>
                    <option value="" disabled selected>Select a Category</option>
                    <option value="Hand Man">Handy Man</option>
                    <option value="Electrical">Electrical</option>
                    <option value="Plubming">Plubming</option>
                    <option value="Carpenter">Carpenter</option>
                    <option value="Landscaping">Landscaping</option>
                </select>
            </td>
            <td rowspan = "2">
                <label for="dess">Description:</label><br>
                <input class = dess type="text" id="dess" name="dess" >
            </td>
        </tr>

        <tr>
            <td>
                <label for="busUName">User Name:</label><br>
                <input type="text" id="busUName" name="busUName" value="username">

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
                <label for="cPassword">Confirm Password:</label><br>
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

    <a  class = "backBtn" href="Login.php">
       Go back
    </a>

</div>

    
</body>
</html>