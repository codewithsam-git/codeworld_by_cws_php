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
    $sql = "SELECT * FROM enrollments";
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
    $json = file_get_contents('php://input');
    $_POST = json_decode($json, true);

    $on_course_id = $_POST['on_course_id'];
    $on_course_name = $_POST['on_course_name'];
    $stud_id = $_POST['stud_id'];
    $stud_display_name = $_POST['stud_display_name'];
    $stud_mobile = $_POST['stud_mobile'];
    $transaction_id = $_POST['transaction_id'];
    $payment = $_POST['payment'];

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "codeworld_by_cws";

        $conn = new mysqli($servername, $username, $password, $dbname);
     
        $stmt = $conn->prepare("INSERT INTO enrollments ( on_course_id,on_course_name,stud_id, stud_display_name, stud_mobile, transaction_id, payment) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isissss", $on_course_id, $on_course_name, $stud_id, $stud_display_name, $stud_mobile,$transaction_id,$payment);

        if ($stmt->execute()) {
            echo json_encode(array("status" => "yes", "message" => "Enrolled successfully."));
        } else {
            echo json_encode(array("status" => "no", "message" => "Error while enrollment"));
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
    $stmt = $conn->prepare("DELETE FROM enrollments WHERE id = ?");
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
