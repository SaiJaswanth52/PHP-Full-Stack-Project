<?php
session_start();
require_once("../model/pdo.php");


if (isset($_POST["delete"]) && isset($_POST["selection_id"])) {
    $selection_id = filter_input(INPUT_POST, "selection_id", FILTER_VALIDATE_INT);
    if ($selection_id) {
        $get_bus_id_query = "SELECT bus_id, num_seats FROM passenger_bus_selections WHERE id = :selection_id";
        $get_bus_id_stmt = $pdo->prepare($get_bus_id_query);
        $get_bus_id_stmt->execute(array('selection_id' => $selection_id));
        $bus_info = $get_bus_id_stmt->fetch(PDO::FETCH_ASSOC);

        if ($bus_info) {
            $bus_id = $bus_info['bus_id'];
            $num_seats = $bus_info['num_seats'];

            // Delete the booking
            $delete_query = "DELETE FROM passenger_bus_selections WHERE id = :selection_id";
            $delete_stmt = $pdo->prepare($delete_query);
            $result = $delete_stmt->execute(array('selection_id' => $selection_id));

            if ($result) {
                // Updation of available seats and booked seats in the  bus
                $update_bus_seats_query = "UPDATE buses SET available_seats = available_seats + :num_seats, num_seats_booked = num_seats_booked - :num_seats WHERE id = :bus_id";
                $update_bus_seats_stmt = $pdo->prepare($update_bus_seats_query);
                $update_bus_seats_stmt->execute(array('num_seats' => $num_seats, 'bus_id' => $bus_id));
                
                
                $_SESSION['success'] = "Deletion successful!";
                header("Location: show_bookings.php");
                return;
                
            }
            else {
                $_SESSION['error'] = "Deletion failed";
                header("Location: show_bookings.php");
                return;
            }
        }
    }
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