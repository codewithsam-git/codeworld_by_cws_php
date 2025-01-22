<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

function handleGetRequest() { 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codeworld_by_cws";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}  
    $sql = "SELECT * FROM online_courses";
    $result = $conn->query($sql);    
    if ($result->num_rows > 0) {
        $courses = array();
        while($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        echo json_encode($courses);
    } else {
        echo "0 results";
    }
    $conn->close();
}

function handlePostRequest() {

    $onTitle = $_POST['onTitle'];
    $onFees = $_POST['onFees'];
    $onStartDate = $_POST['onStartDate'];
    $onEndDate = $_POST['onEndDate'];
    $onDescription = $_POST['onDescription'];

    $uploadDir = 'images/onlinecourses/';

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = uniqid() . '_' . $_FILES["onImage"]["name"];
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($_FILES["onImage"]["tmp_name"], $destination)) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "codeworld_by_cws";

        $conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
        $staticFilePath = 'http://localhost/codeworld_by_cws_php';
        $imagePath = $staticFilePath . '/' . $destination;        
        $stmt = $conn->prepare("INSERT INTO online_courses (on_title, on_fees, on_start_date, on_end_date, on_description ,on_image, on_image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $onTitle, $onFees, $onStartDate, $onEndDate, $onDescription, $filename, $imagePath);

        if ($stmt->execute()) {
            echo json_encode(array("success" => true, "message" => "Image uploaded and inserted into database successfully.", "filename" => $filename, "file_path" => $imagePath));
        } else {
            echo json_encode(array("success" => false, "message" => "Error inserting image into database."));
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(array("success" => false, "message" => "Error uploading image."));
    }
}

function handleDeleteRequest() {

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "codeworld_by_cws";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    

    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        http_response_code(405);
        echo json_encode(array("success" => false, "message" => "Method Not Allowed"));
        exit;
    }
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "ID parameter is missing"));
        exit;
    }

    $id = $_GET['id'];   
    $stmt = $conn->prepare("DELETE FROM online_courses WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(array("success" => true, "message" => "Record deleted successfully"));
    } else {
        echo json_encode(array("success" => false, "message" => "Failed to delete record"));
    }

    $stmt->close();
    $conn->close();
}

function handleUnsupportedMethod() {
    http_response_code(405);
    echo json_encode(array("success" => false, "message" => "Method Not Allowed"));
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        handleGetRequest();
        break;
    case 'POST':
        handlePostRequest();
        break;
    case 'DELETE':
        handleDeleteRequest();
        break;
    default:
        handleUnsupportedMethod();
        break;
}
?>
