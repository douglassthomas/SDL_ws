<?php

    require "../constant_ws.php";

    header("Access-Control-Allow-Origin: *");
    header('Content-type: application/json');

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $body = json_decode(file_get_contents('php://input'));
        if(isset($body->id) && isset($body->pin) && isset($body->new_pin)){
            $id = $body->id;
            $pin = $body->pin;
            $new_pin = $body->new_pin;
            $db = new mysqli(HOST, USER, PASSWORD, DB);

            $query = "SELECT * FROM Devices
                        WHERE id=? AND pin=?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $id, $pin);
            $stmt->execute();
            $res = $stmt->get_result();
            if(mysqli_num_rows($res) == 1){
                // change pin
                $query = "UPDATE Devices SET pin=? WHERE id=? AND pin=?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("iii", $new_pin, $id, $pin);
                $stmt->execute();

                echo json_encode([
                    "success" => true,
                    "message" => "success change pin #".$id
                ]);
            }
            else{
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