<?php

if(isset($_GET['id'])) {
    
    header("content-type: text/plain");

    $id = $_GET['id'];

    $conn = new mysqli("sql112.epizy.com", "epiz_28817728", "gdTA6izrfFVzyA", "epiz_28817728_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT data FROM json WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result=$stmt->get_result();
    $row = $result->fetch_assoc();    
    
    echo $row['data'];
    
    $stmt->close();
    $conn->close();

}



?>