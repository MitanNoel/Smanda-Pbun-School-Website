<?php
ini_set('display_errors', '0'); // Do not display errors to the end user

$servername = "localhost:3306";;
$dbusername = "cvmmxzym_admin";
$dbpassword = "Smandaluarbiasa2525";
$dbname = "cvmmxzym_smanda";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
