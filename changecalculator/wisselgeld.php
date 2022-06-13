<?php
session_start();
?>

<!doctype html>
<html lang="en">

<head>
    <title>Change calculator</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <?php

    $money = $_SESSION['money'] = $err = $result = "";
    # Defining money units
    define('MONEYUNITS', array(50, 20, 10, 5, 2, 1));

    # This function rounds an amount of cents to the nearest €0.05 value, eg. 8.37 > 8.35. 
    function roundToFive($centNumber)
    {
        $lastdigit = $centNumber % 10;
        if ($lastdigit != 5 && $lastdigit != 0) {
            if ($lastdigit < 3) {
                $centNumber = $centNumber + (0 - $lastdigit);
            } else if ($lastdigit > 2 && $lastdigit < 8) {
                $centNumber = $centNumber + (5 - $lastdigit);
            } else {
                $centNumber = $centNumber + (10 - $lastdigit);
            }
        }
        return $centNumber;
    }

    # This function loops through the coin / bills available and checks which ones should be handed out. 
    function checkMoneyList($unit, $unitName, $string)
    {
        foreach (MONEYUNITS as $coin) {
            if ($unit >= $coin) {
                $timesCoin = floor($unit / $coin);
                $unit = fmod($unit, ($coin * $timesCoin));
                for ($i = 0; $i < $timesCoin; $i++) {
                    $string .= "<img src='{$coin}{$unitName}.png'>";
                }
            }
        }
        return $string;
    }

    # This function combines the previous two functions, accepts a money amount and returns the calculated change. 
    function calcChange($amount)
    {
        $change = "";
        $cents = $amount - floor($amount);
        $euros = $amount - $cents;
        $change = checkMoneyList($euros, "euro", $change);
        $cents = round($cents * 100);
        $cents = roundToFive($cents);
        $change = checkMoneyList($cents, "cent", $change);
        return $change;
    }

    # This function checks the value the user puts in and catches the errors that can occur for wrong inputs
    function checkInput($userInput)
    {
        $caught = false;
        try {
            if (!is_numeric($userInput)) {
                throw new Exception();
            }
        } catch (Exception $e) {
            $caught = true;
            return "Input must be a valid number ";
        }

        try {
            if (floatval($userInput) == 0) {
                throw new Exception();
            }
        } catch (Exception $e) {
            $caught = true;
            return "Geen wisselgeld. No change.";
        }

        try {
            if (floatval($userInput) < 0) {
                throw new Exception();
            }
        } catch (Exception $e) {
            $caught = true;
            return "Input must be a positive number";
        }
        if (!$caught) {
            return false;
        }
    }
    # if the user input can be converterd into change, this function will calculate and give the result. It also catches any yet undetected errors.
    function convertChange($userMoney)
    {
        try {
            $moneyIn = floatval($userMoney);
            $moneyOut = calcChange($moneyIn, MONEYUNITS);
            return $moneyOut;
        } catch (Exception $x) {
            return "Caught error: unknown error";
        }
    }

    # Finally, first check the userInput, then convert it to change, using all the functions declared above. 
    if (isset($_POST['submit'])) {
        $money = $_POST['money'];
        if (checkInput($money)) {
            $err = checkInput($money);
        } else {
            $result = convertChange($money);
        }
    }
    ?>
    <h1>Change your money!</h1>

    <form method="POST">
        <img src='change.png' id='mainimg'>
        <div class='inputs'>
            <div>
                <label for="n1">Your money:</label>
                <input type="text" id="money" name="money" value="<?php echo $money ?>">
                <p class='error'><?php echo $err ?></p>
            </div>
            <div>
                <input type="submit" id="submit" value="Submit" name="submit">
            </div>
        </div>
        <div class="resulttext">
            <?php if ($result) {
                echo $result;
            } ?>
        </div>
    </form>
</body>

</html>