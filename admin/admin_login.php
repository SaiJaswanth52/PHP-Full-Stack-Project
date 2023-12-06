<?php
session_start();
require_once("../model/pdo.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        
        $username = filter_input(INPUT_POST,"username",FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST,"password",FILTER_SANITIZE_STRING);
        

        $select = "SELECT password FROM admin WHERE username = :username";
        $stmt = $pdo->prepare($select);
        $stmt->execute(array(':username' => $username));
        $storedPwd = $stmt->fetchColumn();

        if ($storedPwd && password_verify($password, $storedPwd)) {
            $loginmessage = "Admin Logged in Successfully";
            echo htmlentities($loginmessage) . "<br>";
            header("Location: admin_dashboard.html"); 
            return;
        } else {
            $loginmessage = "Password not Matching..";
            echo "<p style=color:red>".htmlentities($loginmessage)."</p>";
            echo '<a href="../index.php">Home</a>';
        }
    }
}
?>
