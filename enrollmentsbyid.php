<?php

require "commonDb.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (!isset($_GET['stud_id']) || !is_numeric($_GET['stud_id'])) {
    echo json_encode(array("success" => false, "message" => "ID not provided or invalid"));
    exit;
}

$id = $_GET['stud_id'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codeworld_by_cws";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT * FROM enrollments WHERE stud_id = ?");
$stmt->bind_param("i", $id); 
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $courses = array();
    while($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    echo json_encode($courses);
} else {
    echo json_encode(array("success" => false, "message" => "No student found for the given ID"));
}

$stmt->close();
$conn->close();

?>
