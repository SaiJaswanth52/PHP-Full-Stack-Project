<?php
require_once("../model/pdo.php");
session_start();

if (isset($_SESSION['user_id'])) {
    $passenger_id = $_SESSION['user_id'];

    // Query the database to retrieve the user's bus bookings
    $sql = "SELECT p.id AS selection_id, p.num_seats, p.payment_status, p.bus_id, b.busname, b.from, b.to, b.timings, b.date, b.price
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

    echo "<table border=1>"; // Adjusted the border attribute to 1 for better styling
    echo "<thead>";
    echo "<tr>";
    echo "<th>Bus Name</th>"; // Changed 'td' to 'th' for table header cells
    echo "<th>From</th>";
    echo "<th>To</th>";
    echo "<th>Timings</th>";
    echo "<th>Date</th>";
    echo "<th>Price</th>";
    echo "<th>Selected Seats</th>";
    echo "<th>Payment Status</th>";
    echo "<th>Amount to be Paid</th>";
    echo "<th>Action</th>"; // Added an action column
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $price = $row['price'];
        $numSeats = $row['num_seats'];
        $paymentStatus = $row['payment_status'];
        $amountToBePaid = $price * $numSeats; // Calculate the amount to be paid
        if ($paymentStatus === 'Paid') {
            // If payment status is 'Paid', set amount to be paid to zero
            $amountToBePaid = 0.00;
        } else {
            $amountToBePaid = $price * $numSeats; // Calculate the amount to be paid
        }


        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['busname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['from']) . "</td>";
        echo "<td>" . htmlspecialchars($row['to']) . "</td>";
        echo "<td>" . htmlspecialchars($row['timings']) . "</td>";
        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
        echo "<td>$" . number_format($price, 2) . "</td>"; // Format price to show two decimal places
        echo "<td>" . htmlspecialchars($numSeats) . "</td>";
        echo "<td>" . htmlspecialchars($row['payment_status']) . "</td>";
        echo "<td>$" . number_format($amountToBePaid, 2) . "</td>"; // Format amountToBePaid

        echo "<td>";
        echo '<form method="post" action="payment_form.php" onsubmit="return confirm(\'Are you sure you want to Pay total amount?\');">';
        echo '<input type="hidden" name="bus_id" value="' . htmlspecialchars($row['bus_id']) . '">';
        echo '<input type="hidden" name="busname" value="' . htmlspecialchars($row['busname']) . '">';
        echo '<input type="hidden" name="from" value="' . htmlspecialchars($row['from']) . '">';
        echo '<input type="hidden" name="to" value="' . htmlspecialchars($row['to']) . '">';
        echo '<input type="hidden" name="timings" value="' . htmlspecialchars($row['timings']) . '">';
        echo '<input type="hidden" name="date" value="' . htmlspecialchars($row['date']) . '">';
        echo '<input type="hidden" name="TotalPrice" value="' . number_format($amountToBePaid, 2) . '">';
        echo '<input type="submit" value="Pay Here" name="payment">';
        echo '</form>';
        echo "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";

    echo "<a class='nav-link' style='color: red' href='passengers_dashboard.html' target='_self'>Back</a>";
    echo "</body></html>";
} else {
    echo "You are not logged in. Please log in to continue.";
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