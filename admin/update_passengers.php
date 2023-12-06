<?php
session_start();
require_once("../model/pdo.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $user_id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING);

    if (!empty($username) && !empty($email)) {
        $sql = "UPDATE passengers SET username = :username, email = :email WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ":email" => $email,
            ":id" => $user_id
        ]);

        $_SESSION["result"] = "Passenger with id:$user_id details updated";
       header("Location:manage_passengers.php");
        return;
    }
}



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Updation</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body class="admin">
    <h2>Update Passenger Record</h2>
    <div class="card">
        <?php
        require_once("../model/pdo.php");
        if (isset($_POST["id"])) {
            $id = $_POST["id"];
            $sql = "SELECT * FROM passengers WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([":id" => $id]);
            $usersData = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        ?>
        <form method="post" action="update_passengers.php">
            <input type="hidden" name="id" id="id" value="<?= $usersData["id"] ?>" required> <br>
            User Name:<br>
            <label for="busname"></label>
            <input type="text" name="username" id="username" value="<?= $usersData["username"] ?>" required> <br>
            Email:
            <input type="text" name="email" id="email" value="<?= $usersData["email"] ?>" required> <br>

            <input type="submit" name="update" value="Update">
        </form>
    </div>
    <a href="manage_passengers.php">Back to View Buses</a>
</body>

</html>
