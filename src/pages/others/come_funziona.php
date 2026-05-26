<?php
    require_once "../../config/app.php";
    require_once "../../components/footer/footer.php";
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Come funziona | <?= APP_NAME ?></title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">
        <link rel="stylesheet" href="others.css">
    </head>
    <body>
        <main class="other-page">
            <h1>Come funziona</h1>

            <p>
                Sotto l'Olmo consente agli utenti registrati di proporre luoghi,
                documenti e testimonianze legati alla memoria del territorio di San Giovanni Rotondo.
            </p>

            <h2>Proposte</h2>
            <p>
                Ogni articolo inserito o modificato viene inviato alla convalida
                degli amministratori.
            </p>

            <h2>Consultazione</h2>
            <p>
                Gli articoli approvati possono essere esplorati dalla Home, dalla
                lista dei contenuti e dalla mappa.
            </p>
        </main>

        <?php render_footer("home-button", "../../../index.php", "Home"); ?>
    </body>
</html>