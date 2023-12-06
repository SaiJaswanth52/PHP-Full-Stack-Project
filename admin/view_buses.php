<?php
session_start();
require_once("../model/pdo.php");

if (isset($_POST["delete"]) && isset($_POST["id"])) {
    $bus_id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
    if ($bus_id) {
        $delete_passenger_records = "DELETE FROM passenger_bus_selections WHERE bus_id = :bus_id";
        $delete_passenger_stmt = $pdo->prepare($delete_passenger_records);
        $delete_passenger_stmt->execute(array('bus_id' => $bus_id));

        $query = "DELETE from buses where id = :id";
        $statement = $pdo->prepare($query);
        $result = $statement->execute(array('id' => $bus_id));
        if ($result) {
            $_SESSION['result'] = "Selected bus data has deleted";
            header("Location: view_buses.php");
            return; 
        }
    }
}
?>
<?php
 if(isset($_SESSION['result'])){
    echo "<p style='color:blue'>".$_SESSION['result'] ."<p>";
    unset($_SESSION['result']);
 }

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Buses</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div>
        <h2><u>LIST OF "BUSES"</u></h2>
        <div class="admin">
            <div>
                <a class="nav-link" style="color: red" href="../index.php" target="_self" id="logout">Logout</a>
            </div>
            <div>
                <table border="3">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>BusName</td>
                            <td>From</td>
                            <td>To</td>
                            <td>Timings</td>
                            <td>Date</td>
                            <td>Price</td>
                            <td>Total Seats</td>
                            <td>Seats Booked</td>
                            <td>Available Seats</td>
                            <td>Update</td>
                            <td>Delete</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, busname, `from`, `to`, timings, date, price, Total_seats, num_seats_booked,available_seats FROM buses";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['busname']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['from']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['to']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['timings']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>" ."$". htmlspecialchars($row['price']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Total_seats']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['num_seats_booked']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['available_seats']) . "</td>";
                            echo "<td>";
                            echo '<form method="post" action="update.php">';
                            echo '<input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">';
                            echo '<input type="submit" value="Update" name="update">';
                            echo '</form>';
                            echo "</td>";
     
     
                            echo "<td>";
                            echo '<form method="post" action="view_buses.php" onsubmit="return confirm(\'Are you sure you want to delete this bus?\');">';
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
    <a class="nav-link" style="color: red" href="manage_buses.php" target="_self" id="logout">Back</a>

</body>

</html>

