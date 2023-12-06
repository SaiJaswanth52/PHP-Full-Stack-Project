<?php
session_start();
require_once("../model/pdo.php");

if (isset($_SESSION['user_id'])) {
    header("Location: passengers_dashboard.html");
    exit();
}

if (isset($_POST["Submit"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);

        $query = "SELECT id, password FROM passengers WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array(':username' => $username));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $hashpwd = $row['password'];
            if (password_verify($password, $hashpwd)) {
                $user_id = $row['id'];

                $access_query = "SELECT access_count FROM user_access_counts WHERE user_id = :user_id";
                $access_stmt = $pdo->prepare($access_query);
                $access_stmt->execute([':user_id' => $user_id]);
                $access_row = $access_stmt->fetch(PDO::FETCH_ASSOC);

                if ($access_row) {
                    $access_count = $access_row['access_count'];
                    $access_count++;
                } else {
                    $access_count = 1;
                }

                // changing the user access count in the database
                $updateQuery = "INSERT INTO user_access_counts (user_id, access_count, last_access_time) 
                                VALUES (:user_id, :access_count, NOW()) 
                                ON DUPLICATE KEY UPDATE access_count = :access_count, last_access_time = NOW()";

                $updateStatement = $pdo->prepare($updateQuery);
                $updateStatement->execute([
                    ':user_id' => $user_id,
                    ':access_count' => $access_count
                ]);

                $_SESSION['user_id'] = $user_id;

                header("Location: passengers_dashboard.html");
                exit();
            }
        }
    }
}

$loginmessage = "Password not Matching...Try again";
echo "<p style=color:red>".htmlentities($loginmessage)."</p>";
echo '<a href="passengers_login.html">Back</a>';

?>
