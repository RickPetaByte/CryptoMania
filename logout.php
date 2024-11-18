<?php 

session_start();
session_unset(); // Verwijdert alle sessievariabelen
session_destroy(); // Vernietigt de sessie

header("Location: index.php"); // Stuur gebruiker terug naar de homepage
exit;

