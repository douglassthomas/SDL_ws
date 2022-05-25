<?php

    require "../constant_ws.php";

    header("Access-Control-Allow-Origin: *");
    header('Content-type: application/json');

    if($_SERVER["REQUEST_METHOD"]=="GET"){
        $db = new mysqli(HOST, USER, PASSWORD, DB);

        // get devices
        $query = "SELECT * FROM Devices";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $res = $stmt->get_result();
        $data = [];
        while ($row = $res->fetch_assoc()) {
            $data[] = ["id" => $row["id"]];
        }
        echo json_encode($data);

    }