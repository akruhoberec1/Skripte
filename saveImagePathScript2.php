<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('log_errors', 1);
ini_set('error_log', './');
// Connect
$servername = "servername";
$username = "username";
$password = "pass  ";
$dbname = "test_db";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!file_exists('outputs')) {
    mkdir('outputs', 0777, true); // create the directory with full permissions
}
// directory of the images for the database, empty if script is in that folder
$imageDirectory = '';

// Loop through images
foreach (glob($imageDirectory . "*.jpg") as $imageFile) {
   /* ob_start();
    var_dump($imageFile);
    $output = ob_get_clean();
    file_put_contents('/outputs/output.txt', $output);*/
    // Get the product code and image number
    $fileName = basename($imageFile);
    $fileNameParts = explode("_", $fileName);
    $productCode = $fileNameParts[0];
    $imageNumber = intval($fileNameParts[1]);

    // Check if this is the first image of a product code
    if ($imageNumber == 1) {

        //Update if first
        $ext = pathinfo($imageFile, PATHINFO_EXTENSION);
        $sql = "UPDATE Products SET image = '{$fileName}', ext = '{$ext}' WHERE productCode = '{$productCode}'";
        $result = $conn->query($sql);

    } else {

        // Insert
        $productId = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM Products WHERE productCode = '{$productCode}'"))['id'];
        $imagePath = mysqli_real_escape_string($conn, realpath($imageFile) . $fileName);
        /*ob_start();
        var_dump($imagePath);
        $outputPath = ob_get_clean();
        file_put_contents('/outputs/outputPath.txt', $outputPath);*/
        $sql = "INSERT INTO Pictures (product_id, imagePath) VALUES ('{$productId}', '{$imagePath}')";
        $result = $conn->query($sql);

    }
}

// Close
$conn->close();
