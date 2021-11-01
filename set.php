<?php

header("content-type: text/html");

$data = file_get_contents('php://input');

$conn = new mysqli("sql112.epizy.com", "epiz_28817728", "gdTA6izrfFVzyA", "epiz_28817728_db");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO json(data) VALUES (?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$data);
$stmt->execute();

echo "New data save as id: " . $conn->insert_id;

$stmt->close();
$conn->close();


?>