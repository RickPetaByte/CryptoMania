<?php
    //include connect.php
    include_once("./inc/functions.php");
    
    $conn = db_connect();

    $name = $_POST["name"];
    $coinprice = $_POST["price"];
    $amount = $_POST["amount"];
    $date = date("Y-m-d H:i:s");

    $email = $_SESSION["email"]; //don't get email when i log in, register is no problem
    $user = $_SESSION["username"];
    $password = $_SESSION["password"];

    //print_r($_SESSION);

    // var_dump($_POST);

    $savedCoinDetails = "INSERT INTO `cryptomania` (users, email, passwords, price, coinname, amount, dates)
                            VALUES ('$user', '$email', '$password', '$coinprice', '$name', '$amount', '$date')";

    /*
        dit werkt: UPDATE `cryptomania` SET `price`='10000',`coinname`='Bitcoin',`amount`='2',`dates`='2020-4-6' WHERE 1; 
    */

    $stmt = $conn->prepare($savedCoinDetails);
    
    if ($stmt = mysqli_prepare($conn, $savedCoinDetails)) {

        if(mysqli_stmt_execute($stmt)) {
            echo "Coin details toegevoegd";
        }
        else {
            echo "Oops, kan coin details niet toevoegen:" . $savedCoinDetails . "<br />" . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
    else {
        echo "Fout bij het voorbereiden van de query: " . mysqli_error($conn);
    }

    mysqli_close($conn);
?>