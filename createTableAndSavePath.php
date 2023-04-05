<?php

// Connect
$servername = "servername";
$username = "username";
$password = "pass";
$dbname = "test_db";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read
$csvFile = 'csvfile.location';
$csv = array_map('str_getcsv', file($csvFile));
$csv = array_slice($csv, 1); 

// Check and insert
foreach ($csv as $row) {
    $articleNumber = $row[2];
    $name = $row[3];

    // Escape the values to prevent SQL injection
    $articleNumber = mysqli_real_escape_string($conn, $articleNumber);
    $name = mysqli_real_escape_string($conn, $name);


    // Insert the data into the products table
    $sql = "INSERT INTO products (productCode, name) VALUES ('$articleNumber', '$name')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully: $articleNumber, $name<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close 
$conn->close();

?>
