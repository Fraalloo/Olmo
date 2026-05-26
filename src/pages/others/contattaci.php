<?php
    require_once "../../config/app.php";
    require_once "../../components/footer/footer.php";
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contattaci | <?= APP_NAME ?></title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">
        <link rel="stylesheet" href="others.css">
    </head>
    <body>
        <main class="other-page">
            <h1>Contattaci</h1>

            <p>
                Per informazioni sul progetto, segnalazioni o richieste di supporto,
                è possibile usare il repository GitHub del progetto.
            </p>

            <h2>Repository</h2>
            <p>
                <a href="https://github.com/Fraalloo/Olmo" target="_blank" rel="noopener noreferrer">
                    GitHub Repository
                </a>
            </p>
        </main>

        <?php render_footer("home-button", "../../../index.php", "Home"); ?>
    </body>
</html>