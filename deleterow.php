<?php
    //include connect.php
    include_once("./inc/functions.php");
    
    $conn = db_connect();

    //print_r($_POST);

    if(isset($_POST['walletId'])) {

        $walletId = $_POST["walletId"];

        $deleteRow = "DELETE FROM `cryptomania` WHERE id = '$walletId'";

        if ($stmt = mysqli_prepare($conn, $deleteRow)) {

            if(mysqli_stmt_execute($stmt)) {
                echo "Row deleted";
                
            }
            else {
                echo "Oops, cannot delete row:" . $deleteRow . "<br />" . mysqli_error($conn);
            }
    
            mysqli_stmt_close($stmt);
        }
        else {
            echo "Error preparing query: " . mysqli_error($conn);
        }
    
        mysqli_close($conn);
    }
    else {
        echo "No ID found";
    }
?>