<?php
// Allow cross-origin requests (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the raw POST data
    $data = file_get_contents("php://input");
    
    // Parse JSON data
    $json_data = json_decode($data);
    

    if ($json_data !== null) {
        // Check if formData values exist
        if (isset($json_data->aemail) && isset($json_data->apass)) {
            $aemail = $json_data->aemail;
            $apass = $json_data->apass;

            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "codeworld_by_cws";
    
            $conn = mysqli_connect($servername, $username, $password, $dbname);            
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql = "SELECT * FROM admin WHERE aemail = '$aemail' AND apass = '$apass'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                
                if ($row['aemail'] != $aemail && $row['apass'] != $apass) {
                    $response = array("status" => "no", "message" => "Both email and password are incorrect");
                    echo json_encode($response);
                } elseif ($row['aemail'] != $aemail) {
                    $response = array("status" => "no", "message" => "Email is incorrect");
                    echo json_encode($response);
                } elseif ($row['apass'] != $apass) {
                    $response = array("status" => "no", "message" => "Password is incorrect");
                    echo json_encode($response);
                } else {
                    // $encrypted_email = openssl_encrypt($aemail, 'aes-256-cbc', $key, 0, "1234567890123456");
                    $response = array("status" => "yes", "message" => "Data Exist, Logged in successfully","session"=>$aemail);
                    echo json_encode($response);
                }                   
                

            } else {
                $response = array("status" => "not exist", "message" => "Data Not Exist");
                echo json_encode($response);
            }

            $conn->close();
        } else {
            // Values do not exist
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
