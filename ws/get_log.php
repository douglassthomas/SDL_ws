<?php

    require "../constant_ws.php";

    header("Access-Control-Allow-Origin: *");
    header('Content-type: application/json');

    if($_SERVER["REQUEST_METHOD"]=="GET"){
        $id = $_GET["id"];
        $db = new mysqli(HOST, USER, PASSWORD, DB);

        // get devices
        $query = "SELECT * 
                    FROM DeviceLogs 
                    WHERE device_id = ?
                    ORDER BY createdAt DESC";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $data = [];
        
        while ($row = $res->fetch_assoc()) {
            $row["pic_path"] = "http://".$_SERVER["HTTP_HOST"]."/storage/".$row["pic_path"];
            $data[] = $row;
        }
        echo json_encode($data);

    }