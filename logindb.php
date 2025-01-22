<?php
// Allow cross-origin requests (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = file_get_contents("php://input");
    $json_data = json_decode($data);
    

    if ($json_data !== null) {  
        if (isset($json_data->semail) && isset($json_data->spass)) {
            $semail = $json_data->semail;
            $spass = $json_data->spass;

            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "codeworld_by_cws";
    
            $conn = mysqli_connect($servername, $username, $password, $dbname);            
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql = "SELECT * FROM register WHERE semail = '$semail' AND spass = '$spass'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $studId = $row["id"];
                if ($row['semail'] != $semail && $row['spass'] != $spass) {
                    $response = array("status" => "no", "message" => "Both email and password are incorrect");
                    echo json_encode($response);
                } elseif ($row['semail'] != $semail) {
                    $response = array("status" => "no", "message" => "Email is incorrect");
                    echo json_encode($response);
                } elseif ($row['spass'] != $spass) {
                    $response = array("status" => "no", "message" => "Password is incorrect");
                    echo json_encode($response);
                } else {
                    $response = array("status" => "yes", "message" => "Data Exist, Logged in successfully","session"=>$semail,"studId"=>$studId);
                    echo json_encode($response);
                }                                   
            } else {
                $response = array("status" => "not exist", "message" => "Data Not Exist");
                echo json_encode($response);
            }
            $conn->close();
        } else {
            echo json_encode(array("status" => "error", "message" => "Form data values do not exist"));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Invalid JSON data"));
    }

} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("status" => "error", "message" => "Method Not Allowed"));
}
?>
