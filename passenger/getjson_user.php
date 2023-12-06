<?php
require_once("../model/pdo.php");
header('Content-Type: application/json; charset=utf-8');
// Add your code here
$query = "SELECT username from passengers";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $result[] = $row;

}

echo json_encode($result, JSON_PRETTY_PRINT);
?>