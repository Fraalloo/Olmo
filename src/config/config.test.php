<?php
   $host     = "localhost";
   $user     = "root";
   $password = "";
   $dbname   = "Olmo";
   $conn     = mysqli_connect($host, $user, $password, $dbname);

   if(!$conn){
      die("Errore connessione database.");
   }
?>