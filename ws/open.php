<?php

    require "../constant_ws.php";

    header("Access-Control-Allow-Origin: *");
    header('Content-type: application/json');

    function upload_to_storage_from_base64($data, $ext){
        $today = date("Y-m-d H:i:s");
        $filename = md5($data.$today).".".$ext;
        $full_path = DOCUMENT_BASE_PATH.$filename;
        file_put_contents($full_path, $data);
    
        return $filename;
    
    }

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $body = json_decode(file_get_contents('php://input'));
        if(isset($body->id) && isset($body->pin) && isset($body->picture)){
            $id = $body->id;
            $pin = $body->pin;
            $picture = $body->picture;
            $db = new mysqli(HOST, USER, PASSWORD, DB);

            $query = "SELECT * FROM Devices
                        WHERE id=? AND pin=?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $id, $pin);
            $stmt->execute();
            $res = $stmt->get_result();
            
            //upload files here
            $str = explode(",", $picture)[1];
            $data = base64_decode($str);
            $ext = explode('/', @mime_content_type($picture))[1];
            $path = upload_to_storage_from_base64($data, $ext);
                
            if(mysqli_num_rows($res) == 1){
                $success = 1;
                
                //make log
                $query = "INSERT INTO DeviceLogs(device_id, pic_path, success, createdAt) 
                            VALUES(?,?,?,?)";
                $stmt = $db->prepare($query);
                $today = date("Y-m-d H:i:s");
                $stmt->bind_param("isis", $id, $path, $success, $today);
                $stmt->execute();

                echo json_encode([
                    "success" => true,
                    "message" => "success open door #".$id
                ]);
            }
            else{
                $success = 0;

                //make log
                $query = "INSERT INTO DeviceLogs(device_id, pic_path, success, createdAt) 
                            VALUES(?,?,?,?)";
                $stmt = $db->prepare($query);
                $today = date("Y-m-d H:i:s");
                $stmt->bind_param("isis", $id, $path, $success, $today);
                $stmt->execute();

                echo json_encode([
                    "success" => false,
                    "message" => "failed"
                ]);
            }
        }
        else{
            echo json_encode([
                "success" => false,
                "message" => "body not match"
            ]);
        }
        
        
    }