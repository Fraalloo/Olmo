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
            data_registrazione,
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
    $success = isset($_GET["success"]);
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Profilo</title>

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
                <h1>Profilo</h1>

                <?php if($forcePassword): ?>
                    <div class="warning-box">
                        <h2>Cambio password richiesto</h2>
                        <p>
                            Stai usando una password temporanea. Per continuare devi modificarla.
                        </p>
                    </div>
                <?php endif; ?>

                <?php if($success): ?>
                    <p class="success-message">Profilo aggiornato correttamente.</p>
                <?php endif; ?>

                <div class="profile-view">
                    <img 
                        class="profile-avatar" 
                        src="<?= esc(TO_ASSETS . $pfp) ?>" 
                        alt="Foto profilo"
                    >

                    <div class="profile-details">
                        <p>
                            <strong>Nome utente:</strong>
                            <?= esc($utente["nome_utente"]) ?>
                        </p>

                        <p>
                            <strong>Ruolo:</strong>
                            <?= !empty($utente["is_admin"]) ? "Admin" : "Utente" ?>
                        </p>

                        <p>
                            <strong>Registrazione:</strong>
                            <?= esc(date("d/m/Y", strtotime($utente["data_registrazione"]))) ?>
                        </p>
                    </div>
                </div>

                <div class="profile-actions">
                    <a class="btn-profile-edit" href="modifica_profile.php">
                        <?= $forcePassword ? "Cambia password" : "Modifica profilo" ?>
                    </a>
                </div>
            </section>
        </main>

        <?php render_footer("home-button", "../home/home.php", "Home"); ?>
    </body>
</html>