<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

function handleGetRequest() { 
    
}

function handlePostRequest() {
    $json = file_get_contents('php://input');
    $_POST = json_decode($json, true);

    $sfname = $_POST['sfname'];
    $slname = $_POST['slname'];
    $semail = $_POST['semail'];
    $spass = $_POST['spass'];
    $smobile = $_POST['smobile'];

        $servername = "localhost"; 
        $username = "root";
        $password = "";
        $dbname = "codeworld_by_cws";

        $conn = new mysqli($servername, $username, $password, $dbname);
     
        $stmt = $conn->prepare("INSERT INTO register (sfname, slname, semail, spass, smobile) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $sfname, $slname, $semail, $spass, $smobile);

        if ($stmt->execute()) {
            echo json_encode(array("status" => "yes", "message" => "account created successfully."));
        } else {
            echo json_encode(array("status" => "no", "message" => "Error while creating account"));
        }

        $stmt->close();
        $conn->close();   
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
    $stmt = $conn->prepare("DELETE FROM offline_courses WHERE id = ?");
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
