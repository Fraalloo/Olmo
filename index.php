<?php
    require_once "src/config/app.php";
    require_once "src/utils/utils.php";

    session_start();

    if(isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"])){
        header("Location: ./src/pages/home/home.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Sotto l'Olmo</title>
        <meta name="robots" content="index, follow"/>
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="index.css">
        <link rel="icon" type="image/png" href="./src/assets/favicon.png"/>

        <meta property="description" content="Le storie di un territorio vivono solo se qualcuno le racconta."/>
        <meta property="og:type" content="website"/>
        <meta property="og:locale" content="it_IT"/>
        <meta property="og:site_name" content="Sotto l'Olmo"/>
        <meta property="og:title" content="Sotto l'Olmo | Le storie di un territorio"/>
        <meta property="og:description" content="Le storie di un territorio vivono solo se qualcuno le racconta."/>
        <meta property="og:url" content="https://www.olmo.it/"/>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Vibur&display=swap" rel="stylesheet">
    </head>
    <body>
        <div class="page">
            <header class="topbar">
                <a href="./index.php" class="brand-small">Sotto l'Olmo</a>

                <div class="nav">
                    <nav class="nav-links">
                        <a href="<?= htmlspecialchars(other_page_url("come_funziona")) ?>">Come funziona</a>
                        <a href="<?= htmlspecialchars(other_page_url("chi_siamo")) ?>">Chi siamo</a>
                        <a href="<?= htmlspecialchars(other_page_url("contattaci")) ?>">Contattaci</a>
                    </nav>

                    <div class="auth">
                        <a href="./src/auth/access.php?mode=login">Accedi</a>
                        <a href="./src/auth/access.php?mode=signup" class="register">Registrati</a>
                    </div>
                </div>
            </header>

            <main class="hero">
                <div class="hero-inner">
                    <div class="tree-card" aria-hidden="true">
                        <img class="hero-tree-image" src="./src/assets/logo.jpeg" alt="Logo">
                    </div>

                    <h1 class="title">Sotto l'Olmo</h1>
                    <p class="subtitle">Le storie di un territorio vivono solo se qualcuno le racconta.</p>
                    <a href="./src/auth/access.php" class="cta">Inizia ora</a>
                </div>
            </main>

            <footer class="footer">
                <div class="footer-inner-index">
                    <p>© 2026 Sotto l'Olmo. All rights reserved.</p>
                    <div class="footer-links">
                        <a href="<?= htmlspecialchars(other_page_url("privacy_policy")) ?>">Privacy Policy</a>
                        <a href="<?= htmlspecialchars(other_page_url("terms_of_service")) ?>">Terms of Service</a>
                        <a href="<?= htmlspecialchars(other_page_url("cookie_settings")) ?>">Cookies Settings</a>
                        <a href="https://github.com/Fraalloo/Olmo">GitHub Repository</a>
                        <p>version <?= CURR_VERS ?></p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>