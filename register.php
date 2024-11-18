<?php
    include_once("./inc/functions.php");

    if(isset($_POST['email']) && isset($_POST['name']) && isset($_POST['password'])) {

        $email = $_POST["email"]; 
        $username = $_POST["name"];
        $password = $_POST["password"];

        //print_r($_POST);
    
        $conn = db_connect();
    
        $checkUserSQL = "SELECT COUNT(*) FROM cryptomania WHERE users = ?";
        $stmt = $conn->prepare($checkUserSQL);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            // echo "Deze gebruikersnaam is al in gebruik.";
            $registerMessageWrong = "<span class='text-danger'>Something went wrong</span>";

            //exit;
        } 
        else {
          
            $sql = $conn->prepare("INSERT INTO `CryptoMania`(`users`, `email`, `passwords`) VALUES (?, ?, ?)"); 
            $sql->bind_param("sss", $username, $email, $password);
            
            if ($sql->execute()) {
                // echo "Registratie succesvol!";

                $registerMessageSuccess = "<span class='text-success'>Register successfull</span>";

                $_SESSION['email'] = $email;
            } 
            else {
                echo "Er is een fout opgetreden: " . $conn->error;
            }
            
            $sql->close();
        }
    
        $conn->close();
    }

    //add hashed password
    //add message when user is registered and login successfully and unsuccessfully
    //fix css register and login
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="./css/style.css">
        <title>Register</title>
    </head>
    <body>
        <?php navbar(); ?>

        <div class="d-flex align-items-center justify-content-center h-75"> <!-- height: 815px; -->
            <div class="p-4 w-auto text-white shadow rounded-2 login_backcolor d-flex justify-content-center flex-direction-column">
                <div class="">
                    <h1 class="d-flex justify-content-center">Register</h1>

                    <form method="POST" action="register.php">
                        <div class="container">
                            <div class="mb-3">
                                <label class="" for="email">Email</label>
                                <input class="form-control" type="email" name="email" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <label class="" for="name">Username</label>
                                <input class="form-control" type="text" name="name" placeholder="Username" required>
                            </div>
                            <div class="mb-3">
                                <label class="" for="password">Password</label>
                                <input class="form-control" type="password" name="password" placeholder="Password" required>
                            </div>
                            <div class="">
                                <?php
                                    if(isset($registerMessageSuccess)) {

                                        echo $registerMessageSuccess;
                                    }
                                    if(isset($registerMessageWrong)) {

                                        echo $registerMessageWrong;
                                    }
                                ?>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="mt-1">Already have an account?<br><a class="text-decoration-none" href="login.php">Log in</a></span>
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-primary mt-3" name="submit" type="submit">Register</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php footer(); ?>
        <?php scripts(); ?>
    </body>
</html>