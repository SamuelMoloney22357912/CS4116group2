<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "fixitfast_db";

try {
   
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    
    $con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    

} catch (mysqli_sql_exception $e) {
    
    die("Database connection failed: " . $e->getMessage());
}