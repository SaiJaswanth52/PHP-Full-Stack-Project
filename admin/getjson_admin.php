
<?php
require_once("../model/pdo.php");
header('Content-Type: application/json; charset=utf-8');
// Add your code here
$query = "SELECT username from admin";
$stmt = $pdo->prepare($query);
$stmt->execute();
$rows = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $rows[] = $row;

}

echo json_encode($rows, JSON_PRETTY_PRINT);
?>






