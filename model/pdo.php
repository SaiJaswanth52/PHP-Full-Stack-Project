<?php
try{
 $pdo =new pdo("mysql:host=localhost; port=3306; dbname=project","sai","S@i123");
 $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 //echo "connection in progress";
}
 catch(PDOException $e){
    echo "No connection". $e->getMessage();

 }



?>
