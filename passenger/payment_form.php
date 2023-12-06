<?php

require_once("../model/pdo.php");
session_start();


if (isset($_POST["payment"]) || (isset($_POST["pay"]) && isset($_POST["pay_amount"]))) {
    $passenger_id = $_SESSION['user_id'];
    $bus_id = filter_input(INPUT_POST, 'bus_id', FILTER_SANITIZE_STRING);
    // var_dump($bus_id);
    $busname = filter_input(INPUT_POST, 'busname', FILTER_SANITIZE_STRING);
    $from = filter_input(INPUT_POST, 'from', FILTER_SANITIZE_STRING);
    $to = filter_input(INPUT_POST, 'to', FILTER_SANITIZE_STRING);
    $timings = filter_input(INPUT_POST, 'timings', FILTER_SANITIZE_STRING);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $amountToBePaid = $_POST['TotalPrice'];
    // var_dump($amountToBePaid);

    if (isset($_POST["pay"]) && isset($_POST["pay_amount"])) {
        $inputAmount = $_POST['pay_amount'];
        // var_dump($inputAmount);

        if (floatval($inputAmount) == floatval($amountToBePaid)) {
            $sqlUpdate = "UPDATE passenger_bus_selections SET payment_status = 'Paid', payment_amount=:payment_amount WHERE bus_id = :bus_id AND passenger_id = :passenger_id";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute(array(':bus_id' => $bus_id, ':payment_amount' => $inputAmount, ':passenger_id' => $passenger_id));

            $_SESSION['success'] = "Payment successful! Payment status updated for: $busname.";
            header("Location: user_payment.php");
            return;
            
        } else {
            $_SESSION['error'] = "Amount not matching to the total amount for: $busname Try again";
                    header("Location: user_payment.php");
                    return;
           
        }
    }
}

?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="admin">
    <div>
        <h2>Payment Section</h2>
        <div>
            <a class="nav-link" style="color: red" href="index.php" target="_self">Logout</a>
        </div>
        <div class="card">
            <form method="post" action="payment_form.php" onsubmit="return validateCreditCard()">
                <input type="hidden" name="bus_id" value="<?= $bus_id ?>">
                
                <label for="busname">Bus Name:</label>
                <input type="text" name="busname" id="busname" value="<?= $busname ?>" readonly> <br>
                <u>BUS ROUTE:</u><br>
                FROM:
                <input type="text" name="from" id="from" value="<?= $from ?>" readonly> <br>
                To:
                <input type="text" name="to" value="<?= $to ?>" readonly> <br>
                Timings:
                <input type="time" name="timings" id="timings" value="<?= $timings ?>" readonly> <br>
                Date:
                <input type="date" name="date" id="date" value="<?= $date ?>" readonly> <br>
                Total Price:
                <input type="text" name="TotalPrice" id="TotalPrice" value="<?= $amountToBePaid ?>" readonly> <br>
                <hr>

                Enter Card Number (16 digits):
               <input type="tel" id="creditCardNumber" name="creditCardNumber" autocomplete="cc-number" placeholder="xxxx xxxx xxxx xxxx"   required><br><br>
                PAY AMOUNT: <br>
                <input type="number" name="pay_amount"> <br>

              

                <button type="submit" name="pay" id="pay">Pay</button>
            </form>
        </div>
    </div>
    <a class='nav-link' style='color: red' href='user_payment.php' target='_self'>Back</a> <br>


    <script src="script.js"> </script>

</body>

</html>