<?php

require "commonDb.php";

// Allow requests from any origin
header("Access-Control-Allow-Origin: *");
// Allow the following methods
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// Allow the following headers
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

$onTitle = $_POST['onTitle'];
$onFees = $_POST['onFees'];
$onStartDate = $_POST['onStartDate'];
$onEndDate = $_POST['onEndDate'];
$onDescription = $_POST['onDescription'];
$id = $_POST['id'];

$uploadDir = 'images/onlinecourses/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Check if a file is uploaded
if (isset($_FILES["onImage"]) && $_FILES["onImage"]["error"] == UPLOAD_ERR_OK) {
    $filename = uniqid() . '_' . $_FILES["onImage"]["name"];
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($_FILES["onImage"]["tmp_name"], $destination)) {
        $staticFilePath = 'http://localhost/codeworld_by_cws_php';
        $imagePath = $staticFilePath . '/' . $destination;
    } else {
        echo json_encode(array("success" => false, "message" => "Error uploading image."));
        exit;
    }
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codeworld_by_cws";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if an image is uploaded
if (isset($imagePath)) {
    $stmt = $conn->prepare("UPDATE online_courses SET on_title = ?, on_fees = ?, on_start_date = ?, on_end_date = ?, on_description = ?, on_image = ?, on_image_path = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $onTitle, $onFees, $onStartDate, $onEndDate, $onDescription, $filename, $imagePath, $id);
} else {
    $stmt = $conn->prepare("UPDATE online_courses SET on_title = ?, on_fees = ?, on_start_date = ?, on_end_date = ?, on_description = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $onTitle, $onFees, $onStartDate, $onEndDate, $onDescription, $id);
}

if ($stmt->execute()) {
    echo json_encode(array("success" => true, "message" => "Image uploaded and inserted into database successfully.", "filename" => $filename ?? null, "file_path" => $imagePath ?? null));
} else {
    echo json_encode(array("success" => false, "message" => "Error inserting image into database."));
}

$stmt->close();
$conn->close();
?>
