<?php
// Credentials to use in localhost, may variate localhost port
$host = "localhost:3305";
$username = "root";
$password = "";
$database = "star_wars";
// Open a new connection to the MySQL server
$conn = new mysqli($host, $username, $password, $database);
// Checking connection
if ($conn->connect_error) {
    die($conn->connect_error);
}
// Change character set to utf8
$conn->set_charset('utf8');
// Return the connection
return $conn;

?>