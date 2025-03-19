<?php

$hostName ="localhost";
$dbUser = "root"; 
$dbPassword = "root";
$dbName = "datatech";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);

if (!$conn){
    die("Something went wrong;");
}
?>
