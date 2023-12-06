<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Buses</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin">
    <div>
        <h2>ADD BUSES</h2>
        <div>
            <a class="nav-link" style="color: red" href="../index.php" target="_self">Logout</a>
        </div>
        <div class="card">
            <form method="post" action="manage_buses.php">
                <label for="busname">BusName:</label>
                <input type="text" name="busname" id="busname" required> <br>
                <u>BUS ROUTE:</u><br>
                FROM:
                <input type="text" name="from" id="from" required> <br>
                To:
                <input type="text" name="to" id="to" required> <br>
                Timings:
                <input type="time" name="timings" id="timings" required> <br>
                Date:
                <input type="date" name="date" id="date" required min="<?= date('Y-m-d') ?>"> <br>
                Price:
                <input type="text" name="price" id="price" required> <br>
                Total Seats:
                <input type="number" name="Total_seats" id="Total_seats" required min="1"> <br>
                <input type="hidden" name="available_seats" id="available_seats" value="0">
                <button type="submit" name="submit" id="addbus">Add Bus</button>
            </form>
            <form method="post" action="view_buses.php">
                <button type="submit" name="view">View Buses</button>
            </form>
            
        </div>
        <a class="nav-link" style="color: red" href="admin_dashboard.html" target="_self" id="back">Back</a>

        <?php
        session_start();
        require_once("../model/pdo.php");
        if (isset($_POST["busname"]) && isset($_POST["from"]) && isset($_POST["to"]) && isset($_POST["timings"]) && isset($_POST["date"]) && isset($_POST["price"]) && isset($_POST["Total_seats"])) {
            $busname = filter_input(INPUT_POST, "busname", FILTER_SANITIZE_STRING);
            $from = filter_input(INPUT_POST, "from", FILTER_SANITIZE_STRING);
            $to = filter_input(INPUT_POST, "to", FILTER_SANITIZE_STRING);
            $timings = $_POST["timings"];
            $date = $_POST["date"];
            $price = $_POST["price"];
            $Total_seats = $_POST["Total_seats"];
            $available_seats=$_POST["Total_seats"];
            $checkbusname = "SELECT busname FROM buses WHERE busname = :busname";
            $stmt = $pdo->prepare($checkbusname);
            $stmt->execute(array(':busname' => $busname));
            $samebusname = $stmt->fetch();

            if ($samebusname) {
                //$registrationMessage = "Bus Already exists. Register with another Bus.";
                //echo "<br>" . htmlspecialchars($registrationMessage);
                $_SESSION['message'] = "Bus Already exists. Register with another Bus.";
                header('Location:manage_buses.php');
                return;

            } else {
                $sql = "INSERT INTO buses (busname, `from`, `to`, timings, date, price, Total_seats, num_seats_booked, available_seats) 
                        VALUES (:busname, :from, :to, :timings, :date, :price, :Total_seats, 0, :Total_seats)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array(
                    ':busname' => $busname,
                    ':from' => $from,
                    ':to' => $to,
                    ':timings' => $timings,
                    ':date' => $date,
                    ':price' => $price,
                    ':Total_seats' => $Total_seats
                ));
                $registrationMessage = "New Bus Added";
                echo htmlspecialchars($registrationMessage);
                $_SESSION['message'] = $registrationMessage;
                header('Location:manage_buses.php');
                return;


            }
        }
        ?>
        <?php 
                   if(isset($_SESSION['message'])){
                    echo "<h3>". htmlspecialchars($_SESSION['message']). "</h3>";
                    unset($_SESSION['message']);
                   }
            ?>

    </body>
</html>
