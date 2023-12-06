<?php
session_start();
require_once("../model/pdo.php");

if (isset($_POST["cancel"]) && isset($_POST["id"])) {
    $user_id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
    
    // Fetch bus_id and num_seats from the canceled booking
    $sql = "SELECT bus_id, num_seats FROM passenger_bus_selections WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $user_id]);
    $bookingData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($bookingData) {
        $bus_id = $bookingData['bus_id'];
        $num_seats = $bookingData['num_seats'];
        
        // Update the buses table
        $update_buses_sql = "UPDATE buses SET available_seats = available_seats + :num_seats, num_seats_booked = num_seats_booked - :num_seats WHERE id = :bus_id";
        $update_stmt = $pdo->prepare($update_buses_sql);
        $update_stmt->execute([
            ':num_seats' => $num_seats,
            ':bus_id' => $bus_id
        ]);

        // cancel the booking
        $cancel_passenger_records = "DELETE FROM passenger_bus_selections WHERE id = :id";
        $cancel_passenger_stmt = $pdo->prepare($cancel_passenger_records);
        $result = $cancel_passenger_stmt->execute(['id' => $user_id]);
        
        if ($result) {
            $_SESSION['result'] = "Booking id: $user_id with Bus id:".$bus_id . " is cancelled.";
            header("Location: manage_bookings.php");
            return;
            
        } 
    }
}


?>

<?php
if(isset($_SESSION["result"])){
    echo "<h3 style = 'color:pink'>".htmlentities($_SESSION['result']) ."</h3>". "<br>";
    unset($_SESSION["result"]);

}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div>
        <h2><u>LIST OF "Bookings"</u></h2>
        <div class="admin">
            <div>
                <a class="nav-link" style="color: red" href="../index.php" target="_self" id="logout">Logout</a>
            </div>
            <div>
                <table border="3">
                    <thead>
                        <tr>
                            <td>Passenger-ID</td>
                            <td>Bus-ID</td>
                            <td>Date</td>
                            <td>Num-Seats</td>
                            <td>Payment-Status</td>
                            <td>Amount Paid</td>
                            <td>Cancel</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM passenger_bus_selections";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['passenger_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['bus_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['selection_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['num_seats']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['payment_status']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['payment_amount']) . "</td>";

                           
                            echo "<td>";
                            echo '<form method="post" action="manage_bookings.php" onsubmit="return confirm(\'Are you sure you want to cancel this user bookings?\');">';
                            echo '<input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">';

                            echo '<input type="submit" value="cancel" name="cancel" >';
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
