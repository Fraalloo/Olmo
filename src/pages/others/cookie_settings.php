<?php
    require_once "../../config/app.php";
    require_once "../../components/footer/footer.php";
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cookie Settings | <?= APP_NAME ?></title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">
        <link rel="stylesheet" href="others.css">
    </head>
    <body>
        <main class="other-page">
            <h1>Cookie Settings</h1>

            <p>
                Il progetto usa cookie tecnici necessari alla gestione della sessione
                utente e dell'accesso alle pagine protette.
            </p>

            <h2>Cookie tecnici</h2>
            <p>
                I cookie di sessione permettono di mantenere attivo il login durante
                la navigazione.
            </p>

            <h2>Cookie opzionali</h2>
            <p>
                Al momento non sono previsti cookie di profilazione o marketing.
            </p>
        </main>

        <?php render_footer("home-button", "../../../index.php", "Home"); ?>
    </body>
</html>