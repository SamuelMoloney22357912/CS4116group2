<?php



$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "fixitfast";

if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname)){
   
    die("Failed to connect to the database");

}