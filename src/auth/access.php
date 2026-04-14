<?php
    require_once "../config/app.php";

    session_start();

    if(!isset($_SESSION["access_mode"])){
        $_SESSION["access_mode"] = "login";
    }

    if(isset($_GET["mode"])){
        $_SESSION["access_mode"] = ($_GET["mode"] === "signup") ? "signup" : "login";
    }

    $mode = $_SESSION["access_mode"];
    $isLogin = $mode === "login";

    $error = $_SESSION["access_error"] ?? "";
    unset($_SESSION["access_error"]);
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $isLogin ? "Login" : "Registrazione" ?> | Sotto l'Olmo</title>

        <link rel="stylesheet" href="../../style.css">
        <link rel="stylesheet" href="access.css">
        <link rel="icon" type="image/png" href="./src/assets/favicon.png">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Vibur&display=swap" rel="stylesheet">
    </head>
    <body>
        <div class="page">
            <header class="topbar">
                <a href="../../index.php" class="brand-small">Sotto l'Olmo</a>

                <nav class="nav-links">
                    <a href="#">Come funziona</a>
                    <a href="#">Chi siamo</a>
                    <a href="#">Contattaci</a>
                </nav>
            </header>

            <main class="access-hero">
                <section class="access-card">
                    <h1 class="access-title"><?= $isLogin ? "Login" : "Registrati" ?></h1>

                    <?php if($error): ?>
                        <p class="access-error"><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>

                    <form
                        action="<?= $isLogin ? "login.php" : "signup.php" ?>"
                        method="POST"
                        class="access-form"
                        <?= $isLogin ? "" : "enctype='multipart/form-data'" ?>
                    >
                        <label for="username">Nome utente</label>
                        <input
                            type="text" id="username" name="username" required
                            <?= $isLogin ? "" : "autocomplete='off'" ?>
                        >

                        <label for="password">Password</label>
                        <input
                            type="password" id="password" name="password" required
                            <?= $isLogin ? "" : "autocomplete='new-password'" ?>
                        >

                        <?php if(!$isLogin): ?>
                            <label for="confirm_password">Conferma password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>

                            <label for="profile_photo">Foto profilo (opzionale)</label>
                            <input type="file" id="profile_photo" name="profile_photo" accept=".jpg,.jpeg,.png,.webp">
                        <?php endif; ?>

                        <button type="submit" class="access-submit">
                            <?= $isLogin ? "Accedi" : "Registrati" ?>
                        </button>
                    </form>

                    <p class="access-switch">
                        <?php if($isLogin): ?>
                            Non hai un account? <a href="access.php?mode=signup">Registrati</a>
                        <?php else: ?>
                            Hai già un account? <a href="access.php?mode=login">Accedi</a>
                        <?php endif; ?>
                    </p>
                </section>
            </main>

            <footer class="footer">
                <div class="footer-inner">
                    <p>© 2026 Sotto l'Olmo. All rights reserved.</p>

                    <a href="../../index.php" class="home-button">Home</a>

                    <div class="footer-links">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">Cookies Settings</a>
                        <p>version <?= CURR_VERS ?></p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>