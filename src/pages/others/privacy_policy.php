<?php
    require_once "../../config/app.php";
    require_once "../../components/footer/footer.php";
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Privacy Policy | <?= APP_NAME ?></title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">
        <link rel="stylesheet" href="others.css">
    </head>
    <body>
        <main class="other-page">
            <h1>Privacy Policy</h1>

            <p>
                Sotto l'Olmo raccoglie solo i dati necessari alla registrazione,
                all'accesso e alla pubblicazione dei contenuti.
            </p>

            <h2>Dati gestiti</h2>
            <p>
                Il sistema conserva nome utente, password cifrata, eventuale foto profilo
                e contenuti proposti dagli utenti.
            </p>

            <h2>Uso dei dati</h2>
            <p>
                I dati vengono usati per autenticare gli utenti, mostrare gli autori
                degli articoli e consentire la gestione dei contenuti.
            </p>
        </main>

        <?php render_footer("home-button", "../../../index.php", "Home"); ?>
    </body>
</html>