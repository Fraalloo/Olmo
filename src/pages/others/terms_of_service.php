<?php
    require_once "../../config/app.php";
    require_once "../../components/footer/footer.php";
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Terms of Service | <?= APP_NAME ?></title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">
        <link rel="stylesheet" href="others.css">
    </head>
    <body>
        <main class="other-page">
            <h1>Terms of Service</h1>

            <p>
                L'utilizzo di Sotto l'Olmo richiede un comportamento rispettoso
                e contenuti coerenti con le finalità culturali del progetto.
            </p>

            <h2>Contenuti</h2>
            <p>
                Gli utenti possono proporre articoli, file e link. Le proposte vengono
                pubblicate solo dopo convalida da parte degli amministratori.
            </p>

            <h2>Responsabilità</h2>
            <p>
                Ogni utente è responsabile dei contenuti che propone e delle fonti
                che allega.
            </p>
        </main>

        <?php render_footer("home-button", "../../../index.php", "Home"); ?>
    </body>
</html>