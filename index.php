<?php
// Create a session
session_start();
// Use connection
include("./conn/conn.php");
// Select all columns
$sql = "SELECT * FROM characters";
// Parsing the requested path
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
// Perform query in database
if ($result = mysqli_query($conn, $sql)) {
    $total_characters = mysqli_num_rows($result); // Number of rows
    $valid_path = false; // Set if a path is valid with this boolean
    // Testing in XAMPP if ($uri === "/swapi") {
    if ($uri === "/") {
        $valid_path = true; // Valid path
        require __DIR__ . "/views/home.php";
    }
    // Loop throughout rows
    for ($i = 0; $i <= $total_characters + 1; $i++) {
        // Testing in XAMPP $path = "/swapi/character/id/" . $i;
        $path = "/character/id/" . $i; // Path of id, plus id number
        if ($uri === $path) {
            $valid_path = true; // Valid path
            require __DIR__ . "/views/character.php";
        }
        $_SESSION['id'] = $i + 1; // Since i starts at 0, set session id plus 1 to match row ids
    }
    // If path has not been set as valid, path will take you to a Not Found page
    if (!$valid_path) {
        require __DIR__ . "/views/not_found.php";
        die(); // Terminate the current script
    }
}

?>