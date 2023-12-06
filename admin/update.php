
<?php
    session_start();
    require_once("../model/pdo.php");

   if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
       $bus_id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
       $busname = filter_input(INPUT_POST, "busname", FILTER_SANITIZE_STRING);
       $newFrom = filter_input(INPUT_POST, "from", FILTER_SANITIZE_STRING);
       $newTo = filter_input(INPUT_POST, "to", FILTER_SANITIZE_STRING);
       $newTimings = filter_input(INPUT_POST, "timings", FILTER_SANITIZE_STRING);
       $newDate = filter_input(INPUT_POST, "date", FILTER_SANITIZE_STRING);
       $newPrice = filter_input(INPUT_POST, "price", FILTER_SANITIZE_STRING);
       $Total_seats = filter_input(INPUT_POST, "Total_seats", FILTER_VALIDATE_INT);
   
       // Retrieve the old total seats and available seats
       $sql = "SELECT Total_seats, available_seats FROM buses WHERE id = :id";
       $stmt = $pdo->prepare($sql);
       $stmt->execute([":id" => $bus_id]);
       $busData = $stmt->fetch(PDO::FETCH_ASSOC);
       
       // Calculate the difference in total seats
       $totalSeatsDifference = $Total_seats - $busData["Total_seats"];
   
       // Calculate the new available seats
       $newAvailableSeats = $busData["available_seats"] + $totalSeatsDifference;
   
       if (!empty($busname) && !empty($newFrom) && !empty($newTo) && !empty($newTimings) && !empty($newDate) && !empty($newPrice) && !empty($Total_seats)) {
           try {
               $sql = "UPDATE buses SET busname = :busname, `from` = :from, `to` = :to, timings = :timings, date = :date, price = :price, Total_seats = :Total_seats, available_seats = :available_seats WHERE id = :id";
               $stmt = $pdo->prepare($sql);
               $stmt->execute([
                   ':busname' => $busname,
                   ":from" => $newFrom,
                   ":to" => $newTo,
                   ":timings" => $newTimings,
                   ":date" => $newDate,
                   ":price" => $newPrice,
                   ":Total_seats" => $Total_seats,
                   ":available_seats" => $newAvailableSeats,
                   ":id" => $bus_id,
               ]);
   
              $_SESSION['result'] = "Bus name:".$busname . " details updated";
               header("Location: view_buses.php");
               return;
           } catch (PDOException $e) {
               echo "Error: " . $e->getMessage();
           }
       } else {
        
           echo "Please fill in all the fields.";
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
    <title>Updation</title>
    <link rel="stylesheet" href="style.css" />
</head>

<body class="admin">
    <h2>Update Bus Record</h2>
    <div class="card">
        <?php
        require_once("../model/pdo.php");
        if (isset($_POST["id"])) {
            $id = $_POST["id"];
            $sql = "SELECT * FROM buses WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([":id" => $id]);
            $busData = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        ?>
        <form method="post" action="update.php">
            <input type="hidden" name="id" id="id" value="<?= $busData["id"] ?>" required> <br>
            Bus Name:<br>
            <label for="busname"></label>
            <input type="text" name="busname" id="busname" value="<?= $busData["busname"] ?>" required> <br>
            <u>BUS ROUTE:</u><br>
            FROM:
            <input type="text" name="from" id="from" value="<?= $busData["from"] ?>" required> <br>
            To:
            <input type="text" name="to" id="to" value="<?= $busData["to"] ?>" required> <br>
            Timings:
            <input type="time" name="timings" id="timings" value="<?= $busData["timings"] ?>" required> <br>
            Date:
            <input type="date" name="date" id="date" value="<?= $busData["date"] ?>" required min="<?= date('Y-m-d') ?>"> <br>
            Price:
            <input type="text" name="price" id="price" value="<?= $busData["price"] ?>" required> <br>
            Number of seats:
            <input type="text" name="Total_seats" id="Total_seats" value="<?= $busData["Total_seats"] ?>" required> <br>
            <input type="hidden" name="available_seats" id="available_seats" value="<?= $busData["available_seats"] ?>" required> <br>

            <input type="submit" name="update" value="Update">
        </form>
    </div>
    <a href="view_buses.php">Back to View Buses</a>
