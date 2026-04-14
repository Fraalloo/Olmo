<?php
    session_start();

    const TO_ASSETS = "../../";
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <link rel="stylesheet" href="home.css">
        <link rel="stylesheet" href="../../style.css">
        <title>Homepage</title>
    </head>
    <body>
        <main class="cntr">
            <h1>HOMEPAGE</h1>
            <br>
            <?php
                if($_SESSION["is_admin"]){
                    echo "<h2>Amministratore</h2>";
                }else{
                    echo "<h2>Utente normale</h2>";
                }
            ?>
            <img class="pfp-image" src="<?= TO_ASSETS.$_SESSION["pfp"] ?>" alt="Foto profilo">
            <br>
            <a href="../auth/logout.php">logout</a>
        </main>
    </body>
</html>