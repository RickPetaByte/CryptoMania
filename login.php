<?php 

    //session_start();

    include_once("./inc/functions.php");

    $_SESSION['loggedin'] = false;

    if(isset($_POST['name']) && isset($_POST['password'])) {

        $name = $_POST["name"];
        $password = $_POST["password"];

        $conn = db_connect();

        $sql = $conn->prepare("SELECT id, users, passwords FROM `cryptomania` WHERE users = ? AND passwords = ? LIMIT 1");
        $sql->bind_param("ss", $name, $password);
        $sql->execute();
        $sql->bind_result($id, $name, $password);
        $sql->store_result();

        if($sql->num_rows == 1) {

            if($sql->fetch()) //fetching the contents of the row
            {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $name;
                $_SESSION['password'] = $password;
                $_SESSION['id'] = $id;
                
                $loginMessage = "<span class='text-success'>Login successful</span>";
    
                $sql_email = $conn->prepare("SELECT email FROM `cryptomania` WHERE id = ?");
                $sql_email->bind_param("i", $id);
    
                if ($sql_email->execute()) {
                    $sql_email->bind_result($email);
    
                    if ($sql_email->fetch()) {
                        // Sla het emailadres op in de sessie
                        $_SESSION['email'] = $email;
                    } else {
                        echo "Geen email gevonden voor deze gebruiker.";
                    }
                }
    
                $sql_email->close();
           }
        }
        else {
            echo "Wrong credentials";
        }

        $sql->close();
        $conn->close();
       
    }

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
        <title>Login</title>
    </head>
    <body>
        <?php navbar(); ?>

        <div class="d-flex align-items-center justify-content-center h-75"> <!-- height: 815px; -->
            <div class="p-4 w-auto text-white shadow rounded-2 d-flex justify-content-center flex-direction-column rounded border border-black login_backcolor ">
                <div class="">
                    <h1 class="d-flex justify-content-center">Login</h1>

                    <form method="POST" action="login.php">
                        <div class="container">
                            <div class="mb-3">
                                <label class="mb-2" for="name">Username</label>
                                <input class="form-control" type="text" name="name" placeholder="Username" required>
                            </div>
                            <div class="mb-3">
                                <label class="mb-2" for="password">Password</label>
                                <input class="form-control" type="password" name="password" placeholder="Password" required>
                            </div>
                            <div>
                                <?php
                                    if(isset($loginMessage)) {
                                        echo $loginMessage; 
                                    }
                                ?>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="mt-3">Don't have an account?<br><a class="text-decoration-none" href="register.php">Register</a></span>
                                <div class="d-flex justify-content-center">
                                    <button class="mt-3 btn btn-primary" name="submit" type="submit">Login</button>
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