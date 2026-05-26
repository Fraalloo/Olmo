<?php
    require_once "../../config/app.php";
    require_once "../../components/footer/footer.php";
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chi siamo | <?= APP_NAME ?></title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">
        <link rel="stylesheet" href="others.css">
    </head>
    <body>
        <main class="other-page">
            <h1>Chi siamo</h1>

            <p>
                
                Sotto l'Olmo nasce come progetto web per raccogliere e valorizzare
                storie, documenti e luoghi significativi.
                Questo progetto è stato realizzato da dei ragazzi della 5^A informatica 2025/2026.
            </p>

            <h2>Obiettivo</h2>
            <p>
                L'obiettivo è offrire uno spazio ordinato dove la memoria locale
                possa essere proposta, verificata e consultata.
            </p>

            <h2>Comunità</h2>
            <p>
                Il progetto si basa sul contributo degli utenti e sulla revisione
                degli amministratori.
            </p>
        </main>

        <?php render_footer("home-button", "../../../index.php", "Home"); ?>
    </body>
</html>