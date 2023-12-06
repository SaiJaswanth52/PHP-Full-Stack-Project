<?php
require_once("../model/pdo.php");
session_start();

if (isset($_SESSION['user_id'])) {
    $passenger_id = $_SESSION['user_id'];

    $sql = "SELECT p.id AS selection_id, p.num_seats, p.payment_status, b.id AS bus_id, b.busname, b.from, b.to, b.timings, b.date, b.price, b.Total_seats, b.available_seats
        FROM passenger_bus_selections AS p
        INNER JOIN buses AS b ON p.bus_id = b.id
        WHERE p.passenger_id = :passenger_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':passenger_id' => $passenger_id));

    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Show Bookings</title>
        <link rel='stylesheet' href='style.css' />
    </head>
    <body>
        <h2><u>YOUR BUS BOOKINGS</u></h2>
        <div>";

    echo "<table border=3>";
    echo "<thead>";
    echo "<tr>";
    echo "<td>Bus Name</td>";
    echo "<td>From</td>";
    echo "<td>To</td>";
    echo "<td>Timings</td>";
    echo "<td>Date</td>";
    echo "<td>Price</td>";
    echo "<td>Total Seats</td>";
    echo "<td>Seats Booked</td>";
    echo "<td>Seats Available</td>";
    echo "<td>Selected Seats</td>";
    echo "<td>Delete</td>";
    echo "<td>Payment Status</td>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo  "<td>" . htmlspecialchars($row['busname']) . "</td>";
        echo  "<td>" . htmlspecialchars($row['from']) . "</td>";
        echo  "<td>" . htmlspecialchars($row['to']) . "</td>";
        echo  "<td>" . htmlspecialchars($row['timings']) . "</td>";
        echo  "<td>" . htmlspecialchars($row['date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Total_seats']) . "</td>";
        echo "<td>" . htmlspecialchars($row['num_seats']) . "</td>";
        echo "<td>" . htmlspecialchars($row['available_seats']) . "</td>";

        echo "<td>";
        echo '<form method="post" action="show_bookings.php" onsubmit="return confirm(\'Are you sure you want to update the number of seats?\');">';
        echo '<input type="hidden" name="selection_id" value="' . htmlspecialchars($row['selection_id']) . '">';
        echo '<input type="number" name="new_num_seats" min="1" value="' . htmlspecialchars($row['num_seats']) . '">';
        echo '<input type="hidden" name="bus_id" value="' . htmlspecialchars($row['bus_id']) . '">';
        echo '<input type="submit" value="Update" name="update" >';
        echo '</form>';
        echo "</td>";
        echo "<td>";
        echo '<form method="post" action="user_booking_deletion.php" onsubmit="return confirm(\'Are you sure you want to delete this booking?\');">';
        echo '<input type="hidden" name="selection_id" value="' . htmlspecialchars($row['selection_id']) . '">';
        echo '<input type="submit" value="Delete" name="delete" >';
        echo '</form>';
        echo "</td>";

        echo "<td>" . htmlspecialchars($row['payment_status']) . "</td>";
        echo "</tr>";

        //  Seats updation 
        if (isset($_POST["update"]) && isset($_POST["selection_id"]) && isset($_POST["new_num_seats"]) && isset($_POST["bus_id"])) {
            $selection_id = filter_input(INPUT_POST, "selection_id", FILTER_VALIDATE_INT);
            $new_num_seats = filter_input(INPUT_POST, "new_num_seats", FILTER_VALIDATE_INT);
            $bus_id = filter_input(INPUT_POST, "bus_id", FILTER_VALIDATE_INT);
           
            $get_booking_query = "SELECT num_seats FROM passenger_bus_selections WHERE id = :selection_id";
            $get_booking_stmt = $pdo->prepare($get_booking_query);
            $get_booking_stmt->execute(array('selection_id' => $selection_id));
            $booking_data = $get_booking_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($selection_id && $new_num_seats && $bus_id && $booking_data) {
                $current_seats = $booking_data['num_seats']; // current seats booked 10
                
                $bus_info_query = "SELECT available_seats, Total_seats, num_seats_booked FROM buses WHERE id = :bus_id";
                $bus_info_stmt = $pdo->prepare($bus_info_query);
                $bus_info_stmt->execute(array('bus_id' => $bus_id));
                $bus_info = $bus_info_stmt->fetch(PDO::FETCH_ASSOC);
                
                $available_seats = $bus_info['available_seats'];  //40
                $total_seats = $bus_info['Total_seats'];   //50
                $old_num_seats_booked  = $bus_info['num_seats_booked'];
            
                // Calculate the difference in seats
                $diff = $current_seats - $new_num_seats; // 59-69=-10
                
        
                    $new_available_seats = $available_seats + $diff;
                    if($new_available_seats>=0){
                    $update_query = "UPDATE passenger_bus_selections SET num_seats = :new_num_seats WHERE id = :selection_id";
                    $update_stmt = $pdo->prepare($update_query);
                    $result = $update_stmt->execute(array('new_num_seats' => $new_num_seats, 'selection_id' => $selection_id));
                    
                    if ($result) {
                        
                         $new_num_seats = $old_num_seats_booked - ($diff);
                        
                        $update_seats_query = "UPDATE buses SET available_seats = :new_available_seats, num_seats_booked = :new_num_seats   WHERE id = :bus_id";
                        $update_seats_stmt = $pdo->prepare($update_seats_query);
                        $update_seats_stmt->execute(array('new_available_seats' => $new_available_seats, 'bus_id' => $bus_id, 'new_num_seats' => $new_num_seats));
                       
                        $_SESSION['success'] = "Seats updation successful!";
                        header("Location: show_bookings.php");
                        return;
                    }
                }  else {
                    $_SESSION['error'] = "Error: The selected number of seats exceeds the available seats or total seats limit.";
                    header("Location: show_bookings.php");
                    return;
                    
                }
            
            }
        }
        
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";

    echo "<a class='nav-link' style='color: red' href='passengers_dashboard.html' target='_self'>Back</a>";
    echo "</body></html>";
} else {
    echo htmlentities("You are not logged in. Please log in to continue.");
}
?>
<?php
if (isset($_SESSION['error'])) {
    echo "<h3 style='color:red'>" . htmlentities($_SESSION['error']) . "</h3>" . "<br>";
    unset($_SESSION['error']);
} else if (isset($_SESSION['success'])) { 
    echo "<h3 style='color:pink'>" . htmlentities($_SESSION['success']) . "</h3>" . "<br>";
    unset($_SESSION['success']);
}
?>