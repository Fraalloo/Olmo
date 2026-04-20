<?php
    if(file_exists(__DIR__ . "/config.prod.php"))
        require_once __DIR__ . "/config.prod.php";
    else
        require_once __DIR__ . "/config.test.php";

    $conn = mysqli_connect($host, $user, $password, $dbname);

    if(!$conn){
        die("Errore connessione database.");
    }
?>