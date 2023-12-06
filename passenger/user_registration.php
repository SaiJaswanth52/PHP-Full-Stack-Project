
<?php
$registermessage="";
require_once("../model/pdo.php");


if(isset($_POST["Signin"]) && $_SERVER["REQUEST_METHOD"]=="POST"){

    if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])){

        $username = $_POST["username"];
        $password= $_POST["password"];
        $email=filter_var($_POST["email"],FILTER_VALIDATE_EMAIL);

        $checkuname = "SELECT username FROM passengers where username = :username"; 
        $statement = $pdo->prepare($checkuname);
        $statement->execute(array(':username'=>$username));
        $storedname= $statement->fetch();
        if($storedname){
            $registermessage = "Username Exists..Select another username";
            echo "<h2>".htmlentities($registermessage). "</h2>"."<br>";
            echo '<a href="registration.html">Back</a>'."<br>";
            
            //header("Location:registration.html");


        } else {
            $hashPwd = password_hash($password,PASSWORD_DEFAULT);
            
            $insert = "INSERT INTO passengers(username,password,email) VALUES(:username,:password,:email)";
            $insert= $pdo->prepare($insert);
            $insert->execute(array(
                ':username'=>$username,
                ':password'=>$hashPwd,
                ':email'=>$email));
           $registermessage="REGISTRATION SUCCESSFULL YOU CAN LOGIN NOW";
           echo "<p style=color:green>".htmlentities($registermessage)."</p>"."<br>";
           echo '<a href="../index.php">Home</a>';
          

        }
        



    }
}





?>