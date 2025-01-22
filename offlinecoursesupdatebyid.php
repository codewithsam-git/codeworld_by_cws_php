<?php

// Allow requests from any origin
header("Access-Control-Allow-Origin: *");
// Allow specific methods
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
// Allow specific headers
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

$offTitle = $_POST['offTitle'];
$offFees = $_POST['offFees'];
$offDuration = $_POST['offDuration'];
$offDescription = $_POST['offDescription'];
$id = $_POST['id'];

$uploadDir = 'images/offlinecourses/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Check if a file is uploaded
if (isset($_FILES["offImage"]) && $_FILES["offImage"]["error"] == UPLOAD_ERR_OK) {
    $filename = uniqid() . '_' . $_FILES["offImage"]["name"];
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($_FILES["offImage"]["tmp_name"], $destination)) {
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
    $stmt = $conn->prepare("UPDATE offline_courses SET off_title = ?, off_fees = ?, off_duration = ?, off_description = ?, off_image = ?, off_image_path = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $offTitle, $offFees, $offDuration, $offDescription, $filename, $imagePath, $id);
} else {
    $stmt = $conn->prepare("UPDATE offline_courses SET off_title = ?, off_fees = ?, off_duration = ?, off_description = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $offTitle, $offFees, $offDuration, $offDescription, $id);
}

if ($stmt->execute()) {
    echo json_encode(array("success" => true, "message" => "Image uploaded and inserted into database successfully.", "filename" => $filename ?? null, "file_path" => $imagePath ?? null));
} else {
    echo json_encode(array("success" => false, "message" => "Error inserting image into database."));
}

$stmt->close();
$conn->close();
?>
