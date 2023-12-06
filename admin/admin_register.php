<?php
session_start();
require_once("../model/pdo.php");

if (isset($_POST["Signin"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])) {
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);


        $checkuname = "SELECT username FROM admin where username = :username";
        $statement = $pdo->prepare($checkuname);
        $statement->execute(array(':username' => $username));
        $storedname = $statement->fetch();

        if ($storedname) {
            $_SESSION['error'] = "Usename exists.Provide another adminname";
            header("Location:admin_register.php");
            return;
            echo '<a href="registration.html">Back</a>' . "<br>";
        } else {
            $hashPwd = password_hash($password, PASSWORD_DEFAULT);

            $insert = "INSERT INTO admin(username, password, email) VALUES(:username, :password, :email)";
            $insert = $pdo->prepare($insert);
            $insert->execute(array(
                ':username' => $username,
                ':password' => $hashPwd,
                ':email' => $email
            ));

            $_SESSION['sucess'] = "REGISTRATION SUCCESSFUL. YOU CAN LOGIN NOW";
            header("Location:admin_register.php");
            return;
        }
    }
}
?>

<?php
if (isset($_SESSION['error'])) {
    echo "<h3 style = 'color:red'>" . htmlentities($_SESSION['error']) . "</h3>" . "<br>";
    unset($_SESSION['error']);
} else if (isset($_SESSION['sucess'])) {
    echo "<h3 style = 'color:pink'>" . htmlentities($_SESSION['sucess']) . "</h3>" . "<br>";
    unset($_SESSION['sucess']);
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
</head>

<body class="user">
    <link rel="stylesheet" href="style.css">
    <div>
        <a href="../index.php">Home</a>
    </div>
    <div class="card">
        <div>
            <h1>Admin Registration</h1>
        </div>
        <form action="admin_register.php" method="post" autocomplete="off" onsubmit=" return checkAdmin()">
            <label>UserName</label> <br>
            <input type="text" id="username" name="username" placeholder="Enter UserName" required><br>
            <label>Password</label><br>
            <input type="password" id="password" name="password" placeholder="Enter Password" required><br>
            <label>Email</label><br>
            <input type="email" id="email" name="email" placeholder="Enter Email" required><br>
            <button type="submit" id="Signin" name="Signin" class="button">Register</button>
        </form>
    </div>

    <p id='msg'></p>

<script src="script.js"> </script>

<script type="text/javascript" src="jquery-3.7.1.js"></script>
<script type="text/javascript">

$(document).ready(function () {

    $("#username").on('blur', function () {
        var username = $(this).val();
        console.log(username);

        $.getJSON("getjson_admin.php", function (data) {
            if (data.length === 0) {
        console.log("No data received from getjson.php");
        return;
    }
            var samename = false;

            for (var i = 0; i < data.length; i++) {
                if (data[i].username === username) {
                    samename = true;
                    break;
                }
            }

            if (samename) {
                $("#msg").css("color", "red").text("Same Username Exist! Please choose another.");
            } else {
                $("#msg").css("color", "blue").text("You can use the username");
            }
        });
    });
});
</script>

</body>

</html>