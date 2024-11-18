<?php

    session_start();

    function checkLogin() {
        $isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

        echo "<script>var isLoggedIn = " . json_encode($isLoggedIn) . ";</script>";
    }

    function db_connect() {

        $servername = "localhost";
        $username = "root";
        $password = "";
        $db_name = "CryptoMania";

        //Create connection
        $conn = new mysqli($servername, $username, $password, $db_name);

        //Check connection
        if($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn;

        echo "Connected successfully";

    }

    function home() {
        ?>
            <div class="container">
                <div class="col-12 pt-5">
                    <div class="row">
                        <div class="">
                            <h1 class="text-center text-white">The Quickest and Most<a class="text-warning text-decoration-none fw-bold"> Secure </a>
                            <h1 class="text-center text-white">Way to Buy Crypto Coins</h1>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="text-center mt-3 mb-5">
                            <span class="text-white"> <!--- text not really dark more lighter ---->
                                Expierence lightning-fast transactions with our intuitive, user-friendly interface, 
                                designed to ensure seamless and efficient crypto trading for all users
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }

    function navbar() {
        ?>
            <nav class="navbar navbar_color">
                <div class="container-fluid">
                    <a class="navbar-brand text-white" href="index.php">CryptoMania</a>
                    <a class="navbar-brand text-white" href="exchanges.php">Exchanges</a>
                    <a class="navbar-brand text-white" href="cryptonews.php">Crypto News</a>
                    <?php
                        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                            echo '<a class="navbar-brand text-white" href="cryptowallet.php">Crypto Wallet</a>';
                        }
                    ?>
                    <?php
                        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                            // Als de gebruiker is ingelogd, toon 'Uitloggen'
                            echo '<a class="navbar-brand text-white" href="logout.php">Uitloggen</a>';
                        }

                        else {
                            // Als de gebruiker niet is ingelogd, toon 'Login'

                            echo '
                                <div class="d-flex">
                                
                                    <a class="navbar-brand text-white me-3" href="login.php">Login</a>
                                    <a class="navbar-brand text-white m-0" href="register.php">Register</a>
                                    
                                </div>
                            ';
                        }
                    ?>

                    <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button> -->
                    <!-- <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <div class="navbar-nav text-white">
                            <a class="nav-link active text-white" href="#">Home</a>
                            <a class="nav-link active text-white" href="#">Crypto wallet</a>
                        </div>
                    </div> -->
                </div>
            </nav>
        <?php
    }
    
    function table() {
        ?>
            <!--- table with crypto coins --->
            <div class="container table-responsive" style="max-height: 450px; overflow-y: scroll;">
                <table class="table table-striped table-bordered" id="characters-table">
                    <thead class="sticky-top bg-dark">
                        <tr>
                            <th class="table-dark">Short</th>
                            <th class="table-dark">Coin</th>
                            <th class="table-dark">Price in USD</th>
                            <th class="table-dark">Price in EUR</th>
                            <th class="table-dark">Market Cap</th>
                            <th class="table-dark">%24hr</th>
                            <th class="table-dark">Info</th>
                            <?php
                                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                                    echo '<th class="table-dark">Cryptofolio</th>';
                                }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dynamische rijen komen hier -->
                    </tbody>
                </table>
            </div> 
        <?php
    }

    function chartModal() {
        ?>
            <div class="modal fade" id="chartModal" tabindex="-1" aria-labelledby="chartModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="chartModalLabel">Coin Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <span id="symbol"></span><h1 id="currentName"></h1>  <!-- symbol nog toevoegen -->
                            <h1>Current price: <span id="currentPrice"></span></h1>
                            <h2>Market cap: <span id="marketcap"> </span></h2>
                            <canvas id="myChart"></canvas> <!-- Canvas for the chart -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div> 
        <?php
    }

    function cryptoWalletTable() {

        $username = $_SESSION['username'];

        $conn = db_connect();
    
        $sql = $conn->prepare("SELECT id, dates, coinname, price, amount FROM `cryptomania` WHERE users = ?");
        $sql->bind_param("s", $username); //
        $sql->execute();
    
        $result = $sql->get_result();
    
        if($result->num_rows > 0) {

            echo 
            "
                <div class='container mt-3 mb-3'>
                    <h1 class='text-white text-center'>Crypto Wallet</h1>
                </div>
                <div id='' class='container'>
                    <div>
                        <h1 id='coinmessage' class='text-success'></h1>
                    </div>
                </div>
                <div id='' class='container'>
                    <div>
                        <h1 id='deletemessage' class='text-danger'></h1>
                    </div>
                </div>
            ";

            // Maak de tabel slechts één keer aan
            echo "
            <div id'' class='container'><span id='allMessage' class='text-success'></span></div>
            <div class='container table-responsive' style='max-height: 400px; overflow-y: auto;'>
                <table id='walletTable' class='table table-striped table-bordered'>
                    <thead class='sticky-top bg-dark'>
                        <tr>
                            <th class='table-dark'>Id</th>
                            <th class='table-dark'>Bought on</th>
                            <th class='table-dark'>Name</th>
                            <th class='table-dark'>Price in USD</th>
                            <th class='table-dark'>Amount</th>
                            <th class='table-dark'>Total</th>
                            <th class='table-dark'>Save</th>
                            <th class='table-dark'>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
            ";
    
            // Voeg elke rij toe aan de bestaande tabel
            while($row = $result->fetch_assoc()) {
                if(!empty($row['coinname']) && !empty($row['price'])) {
                    echo "
                        <tr>
                            <td>" . $row["id"]. "</td>
                            <td>" . $row["dates"]. "</td>
                            <td>" . $row["coinname"]. "</td>
                            <td>" . $row["price"]. "</td>
                            <td><input class='walletamount' value=" . $row['amount'] ." type='number' min='0'></td>
                            <td></td>
                            <td><button onclick='walletSave(event)' class='btn btn-warning walletSave'>Save</button></td>
                            <td><button onclick='walletDelete(event)' id='walletDelete' class='btn btn-danger'>Delete</button></td>
                        </tr>
                    ";
                }
            }
    
            // Sluit de tabel af
            echo "
                    </tbody>
                </table>
            </div>
            ";
    
        } 
        else {
            echo
            "
                <div class='d-flex align-items-center justify-content-center h-75'> 
                    <div class='p-4 w-auto text-white shadow rounded-2 login_backcolor d-flex justify-content-center flex-direction-column'>
                        <div class=''>
                            <h1 class='d-flex justify-content-center text-white'>No wallet found</h1>
                            <h1>Click<a style='text-decoration: none; color: white;' href='index.php'> here </a>to buy crypto</h1>
                        </div>
                    </div>
                </div>
            ";
        }
    
        $conn->close();
    }

    function exchanges() {
        ?>
            <div class="container-fluid ">
                <!--- divs with exchanges --->
                <div id="exchange" class="text-white">

                </div>
            </div> 
        <?php
    }

    function cryptoNews() {
        ?>
            <div class="container">
                <!-- divs with cryptonews -->
                <div id="cryptonews" class="text-white">

                </div>
            </div> 
        <?php
    }

    function coinModal() {
        ?>
            <div class="modal fade" id="coinModal" tabindex="-1" aria-labelledby="coinModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="coinModalLabel"><span name="coinname" id="coinname"></span></h5> <!-- wordt naam van de coin -->
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="/savecoindetails.php">
                                <span name="currentprice">Current price: <span name="coinprice" id="coinprice"></span> </span> <!-- prijs van de coin -->
                                <br>
                                <span id="amount" name="amount">Amount:<input name="amount" id="number" value="1" min="0" type="number"></span> <!-- hoeveel coins je wilt kopen -->
                                <br>
                                <button id="addbutton" name="addbutton" type="button" class="btn btn-primary">Add</button>
                                <span class="text-success" id="coinmessage"></span>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div> 
        <?php
    }

    function scripts() {
        ?>
            <!-- jQuery -->
		    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

            <!-- Bootstrap -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

            <!-- Chart JS -->
            <script src=" https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js "></script>

            <script src="js/main.js"></script>
        <?php
    }

    function footer() {
        ?>
            <div class="container-fluid p-0 position-absolute bottom-0">
                <footer class="d-flex flex-wrap justify-content-between align-items-center mt-4 py-3 footer_color">
                    <div class="col-12 text-center">
                        <span class="text-white">Copyright &copy; 2024 CryptoMania</span>
                    </div>
                </footer> 
            </div>
            
        <?php
    }

    function customFooter() {
        ?>
            <div class="container-fluid p-0">
                <footer class="d-flex flex-wrap justify-content-between align-items-center mt-4 py-3 footer_color">
                    <div class="col-12 text-center">
                        <span class="text-white">Copyright &copy; 2024 CryptoMania</span>
                    </div>
                </footer> 
            </div>
            
        <?php
    }
?>