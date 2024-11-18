<?php
    //include connect.php
    include_once("./inc/functions.php");
    
    $conn = db_connect();

    print_r($_POST);

    $amount = $_POST["amount"];
    $idAmount = $_POST["idAmount"];

    $updateAmount = "UPDATE `cryptomania` SET amount = '$amount' WHERE id = '$idAmount'";
    
    if ($stmt = mysqli_prepare($conn, $updateAmount)) {

        if(mysqli_stmt_execute($stmt)) {
            echo "Amount updated";
        }
        else {
            echo "Oops, cannot update amount:" . $updateAmount . "<br />" . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
    else {
        echo "Error preparing query: " . mysqli_error($conn);
    }

    mysqli_close($conn);
?>