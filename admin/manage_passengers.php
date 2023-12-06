<?php
session_start();
require_once("../model/pdo.php");

if (isset($_POST["delete"]) && isset($_POST["id"])) {
    $user_id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
    if ($user_id) {
        $delete_passenger_records = "DELETE FROM passenger_bus_selections WHERE passenger_id = :id";
        $delete_passenger_stmt = $pdo->prepare($delete_passenger_records);
        $delete_passenger_stmt->execute(array('id' => $user_id));

        $query = "DELETE FROM passengers WHERE id = :id";
        $statement = $pdo->prepare($query);
        $result = $statement->execute(array('id' => $user_id));

        if ($result) {
            header("Location: manage_passengers.php");
            return; 
        }
    }
}

?>

<?php
if(isset($_SESSION["result"])){
    echo "<h3 style = 'color:blue'>".htmlentities($_SESSION["result"]) ."</h3>". "<br>";
    unset($_SESSION["result"]);

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Passengers</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div>
        <h2><u>LIST OF "Passengers"</u></h2>
        <div class="admin">
            <div>
                <a class="nav-link" style="color: red" href="index.php" target="_self" id="logout">Logout</a>
            </div>
            <div>
                <table border="3">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>UserName</td>
                            <td>Email</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, username, email FROM passengers";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>";
                            echo '<form method="post" action="update_passengers.php">';
                            echo '<input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">';
                            echo '<input type="submit" value="Update" name="update">';
                            echo '</form>';
                            echo "</td>";
                            echo "<td>";
                            echo '<form method="post" action="manage_passengers.php" onsubmit="return confirm(\'Are you sure you want to delete this user?\');">';
                            echo '<input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">';
                            echo '<input type="submit" value="Delete" name="delete" >';
                            echo '</form>';
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <a class="nav-link" style="color: red" href="admin_dashboard.html" target="_self" id="logout">Back</a>

</body>

</html>
