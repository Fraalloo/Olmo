<?php
    require_once "../../config/config.php";
    require_once "../../config/app.php";
    require_once "../../utils/utils.php";
    require_once "../../utils/auth_guard.php";
    require_once "../../components/footer/footer.php";

    session_start();

    const TO_ASSETS = "../../../";

    require_login();

    $userId = (int)$_SESSION["user_id"];
    $forcePassword = !empty($_SESSION["must_change_password"]);

    $query = "
        SELECT
            id_utente,
            nome_utente,
            pfp,
            is_admin,
            must_change_password
        FROM utenti
        WHERE id_utente = ?
    ";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $utente = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if(!$utente){
        header("Location: ../../auth/logout.php");
        exit;
    }

    $pfp = !empty($utente["pfp"]) ? $utente["pfp"] : DEFAULT_PFP;
    $error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Modifica profilo</title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">
        <link rel="stylesheet" href="profile.css">
    </head>

    <body>
        <header class="topbar">
            <p>Profilo</p>

            <nav class="topbar_nav">
                <a href="<?= htmlspecialchars(other_page_url("come_funziona")) ?>">Come funziona</a>
                <a href="<?= htmlspecialchars(other_page_url("chi_siamo")) ?>">Chi siamo</a>
                <a href="<?= htmlspecialchars(other_page_url("contattaci")) ?>">Contattaci</a>

                <?php if(!$forcePassword): ?>
                    <a class="btn-home" href="../home/home.php">Home</a>
                <?php endif; ?>

                <a class="btn-logout" href="../../auth/logout.php">Logout</a>
            </nav>
        </header>

        <main class="profile-page">
            <section class="profile-card">
                <h1>Modifica profilo</h1>

                <?php if($forcePassword): ?>
                    <div class="warning-box">
                        <h2>Cambio password obbligatorio</h2>
                        <p>Prima di continuare devi impostare una nuova password.</p>
                    </div>
                <?php endif; ?>

                <?php if($error !== ""): ?>
                    <p class="error-message">
                        <?php if($error === "password_required"): ?>
                            La nuova password è obbligatoria.
                        <?php elseif($error === "password_short"): ?>
                            La password deve contenere almeno 8 caratteri.
                        <?php elseif($error === "password_mismatch"): ?>
                            Le password non coincidono.
                        <?php elseif($error === "same_password"): ?>
                            La nuova password deve essere diversa da quella attuale.
                        <?php elseif($error === "wrong_current_password"): ?>
                            Password attuale errata.
                        <?php elseif($error === "username_short"): ?>
                            Il nome utente deve contenere più di 3 caratteri.
                        <?php elseif($error === "username_exists"): ?>
                            Nome utente già in uso.
                        <?php elseif($error === "pfp_invalid"): ?>
                            Il file selezionato non è valido.
                        <?php else: ?>
                            Si è verificato un errore.
                        <?php endif; ?>
                    </p>
                <?php endif; ?>

                <form 
                    method="POST" 
                    action="update_profile.php" 
                    enctype="multipart/form-data" 
                    class="profile-form"
                >
                    <label>
                        Nome utente
                        <input 
                            type="text" 
                            name="nome_utente" 
                            value="<?= esc($utente["nome_utente"]) ?>"
                            <?= $forcePassword ? "readonly" : "" ?>
                        >
                    </label>

                    <label>
                        Foto profilo
                        <input 
                            type="file" 
                            name="pfp" 
                            accept="image/png, image/jpeg, image/webp"
                            <?= $forcePassword ? "disabled" : "" ?>
                        >
                    </label>

                    <hr>

                    <?php if(!$forcePassword): ?>
                        <label>
                            Password attuale
                            <input type="password" name="current_password">
                        </label>
                    <?php endif; ?>

                    <label>
                        Nuova password
                        <input type="password" name="new_password">
                    </label>

                    <label>
                        Conferma nuova password
                        <input type="password" name="confirm_password">
                    </label>

                    <button type="submit">
                        <?= $forcePassword ? "Aggiorna password" : "Salva modifiche" ?>
                    </button>
                </form>
            </section>
        </main>

        <?php render_footer("home-button", "profile.php", "Profilo"); ?>
    </body>
</html>