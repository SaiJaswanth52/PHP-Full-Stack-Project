<?php
require_once("../model/pdo.php");
session_start();

if (isset($_SESSION['user_id'])) {
    $passenger_id = $_SESSION['user_id'];

    if (isset($_POST["select"]) && isset($_POST["bus_id"]) && isset($_POST["num_seats"])) {
        $selected_bus_id = filter_input(INPUT_POST, "bus_id", FILTER_VALIDATE_INT);
        $num_seats = filter_input(INPUT_POST, "num_seats", FILTER_VALIDATE_INT);

        if ($selected_bus_id && $num_seats) {
            // Check if the selected number of seats is available
            $check_seats_query = "SELECT Total_seats, num_seats_booked FROM buses WHERE id = :selected_bus_id";
            $check_seats_stmt = $pdo->prepare($check_seats_query);
            $check_seats_stmt->execute(array('selected_bus_id' => $selected_bus_id));
            $seats_info = $check_seats_stmt->fetch(PDO::FETCH_ASSOC);

            $available_seats = $seats_info['Total_seats'];
            $num_seats_booked = $seats_info['num_seats_booked'];

            $available_seats = $available_seats - $num_seats_booked;

            if ($available_seats >= $num_seats) {
                // Seats are available
                $pdo->beginTransaction();

                $check_passenger_selection_query = "SELECT * FROM passenger_bus_selections WHERE passenger_id = :passenger_id AND bus_id = :selected_bus_id";
                $check_passenger_selection_stmt = $pdo->prepare($check_passenger_selection_query);
                $check_passenger_selection_stmt->execute(array('passenger_id' => $passenger_id, 'selected_bus_id' => $selected_bus_id));
                $existing_selection = $check_passenger_selection_stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing_selection) {
                    // Update the existing selection
                    $new_num_seats = $existing_selection['num_seats'] + $num_seats;

                    $update_selection_query = "UPDATE passenger_bus_selections SET num_seats = :new_num_seats WHERE passenger_id = :passenger_id AND bus_id = :selected_bus_id";
                    $update_selection_stmt = $pdo->prepare($update_selection_query);
                    $update_selection_stmt->execute(array('new_num_seats' => $new_num_seats, 'passenger_id' => $passenger_id, 'selected_bus_id' => $selected_bus_id));
                } else {
                    $query = "INSERT INTO passenger_bus_selections (passenger_id, bus_id, num_seats) VALUES (:passenger_id, :selected_bus_id, :num_seats)";
                    $statement = $pdo->prepare($query);
                    $statement->execute(array('passenger_id' => $passenger_id, 'selected_bus_id' => $selected_bus_id, 'num_seats' => $num_seats));
                }

                // Update the available seats and num_seats_booked in the buses table
                $new_available_seats = $available_seats - $num_seats;
                $new_num_seats_booked = $num_seats_booked + $num_seats;

                $update_seats_query = "UPDATE buses SET available_seats = :new_available_seats, num_seats_booked = :new_num_seats_booked WHERE id = :selected_bus_id";
                $update_seats_stmt = $pdo->prepare($update_seats_query);
                $update_seats_stmt->execute(array('new_available_seats' => $new_available_seats, 'new_num_seats_booked' => $new_num_seats_booked, 'selected_bus_id' => $selected_bus_id));

                $pdo->commit(); 

                $_SESSION['success'] = "Seats selection successful!";
                header("Location: select_buses.php");
                return;
                
            } else {
                $_SESSION['error'] = "Error: The selected number of seats exceeds the available seats or total seats limit.";
                header("Location: select_buses.php");
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
    
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Bus</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="user">
    <h2><u>SELECT A BUS</u></h2>

    <form method="post" action="select_buses.php">
        <label for="bus_id">Select a Bus:</label>
        <select name="bus_id" id="bus_id">
            <?php
            require_once("../model/pdo.php");
            $sql = "SELECT id, busname, `from`, `to`, timings, date, price, Total_seats, num_seats_booked,available_seats FROM buses";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<option value="' . htmlspecialchars($row['id']) . '">';
                echo 'BusName: ' . htmlspecialchars($row['busname']) . ' / ';
                echo 'From: ' . htmlspecialchars($row['from']) . ' / ';
                echo 'To: ' . htmlspecialchars($row['to']) . ' / ';
                echo 'Timings: ' . htmlspecialchars($row['timings']) . ' / ';
                echo 'Date: ' . htmlspecialchars($row['date']) . ' / ';
                echo 'Price: ' . "$".htmlspecialchars($row['price']) . ' / ';
                echo 'Total Seats: ' . htmlspecialchars($row['Total_seats']) . ' / ';
                echo 'Seats Booked: ' . htmlspecialchars($row['num_seats_booked']).' / ';
                echo 'Available Seats: ' . htmlspecialchars($row['available_seats']);
                echo '</option>';
            }
            ?>
        </select>
        <label for="num_seats">Number of Seats:</label>
        <input type="number" name="num_seats" id="num_seats" min="1" required>
        <button type="submit" name="select">Select Seats</button>
    </form>

    

    <a class="nav-link" style="color: red" href="passengers_dashboard.html" target="_self" id="back">Back</a>
</body>


</html>