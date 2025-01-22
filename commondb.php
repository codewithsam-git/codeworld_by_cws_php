<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codeworld_by_cws";

$conn = new mysqli($servername, $username, $password, $dbname);

if(mysqli_connect_error()){
    echo "Cannot Connect to Daatabase";
}

?>