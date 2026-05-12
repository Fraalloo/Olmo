<?php
    require_once "../../config/config.php";
    require_once "../../config/app.php"; 
    require_once "../../utils/utils.php";
    require_once "../../utils/auth_guard.php";
    require_once "../../utils/extract_articles.php"; 

    require_once "../../components/footer/footer.php";

    session_start();

    const TO_ASSETS = "../../../";

    require_login();
    require_password_change_if_needed("../profile/profile.php");

    $pfp = $_SESSION["pfp"] ?? "";
    $username = $_SESSION["username"] ?? "";
    
    $tipi_articolo = extract_article_types($conn);
    $accept_mime = implode(",", ALLOWED_PFP_MIME);
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title><?= APP_NAME ?> - Nuovo Articolo</title>
        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="../home/home.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">
        <link rel="stylesheet" href="insert_page.css">
    </head>
    <body>
        <header class="topbar">
            <a class="topbar_profile" href="../profile/profile.php">
                <?php if(!empty($pfp)): ?>
                    <img class="topbar_pfp" src="<?= esc(TO_ASSETS . $pfp) ?>" alt="Foto profilo">
                <?php endif; ?>
                <span class="topbar_username"><?= esc($username) ?></span>
            </a>
            <nav class="topbar_nav">
                <a class="btn-home" href="../home/home.php">Home</a>
                <a class="btn-logout" href="../../auth/logout.php">Logout</a>
            </nav>
        </header>

        <main class="insert-page">
            <div class="form-container">
                <div class="form-header">
                    <h1>Inserisci un nuovo articolo</h1>
                </div>

                <form action="insert.php" method="POST" enctype="multipart/form-data" class="standard-form">
                    <section class="form-section">
                        <h3><i class="icon"></i> Informazioni Base</h3>
                        <div class="form-group">
                            <label for="titolo">Titolo dell'articolo *</label>
                            <input type="text" id="titolo" name="titolo" required placeholder="Inserisci il titolo...">
                        </div>

                        <div class="form-group">
                            <label for="id_tipo_articolo">Tipo di Contenuto *</label>
                            <select id="id_tipo_articolo" name="id_tipo_articolo" required>
                                <option value="" disabled selected>Scegli una categoria...</option>
                                <?php foreach($tipi_articolo as $tipo): ?>
                                    <option value="<?= (int)$tipo['id_tipo_articolo'] ?>">
                                        <?= esc(ucfirst($tipo['descrizione'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="descrizione">Descrizione Completa *</label>
                            <textarea id="descrizione" name="descrizione" rows="10" required placeholder="Racconta la storia..."></textarea>
                        </div>
                    </section>

                    <section class="form-section">
                        <h3><i class="icon"></i> Media e Allegati</h3>
                        
                        <div class="form-group">
                            <label for="banner">Immagine Banner (Copertina)</label>
                            <input type="file" id="banner" name="banner" accept="<?= $accept_mime ?>">
                            <small>Max dimensione: 2MB. Formati: JPG, PNG, WEBP.</small>
                        </div>

                        <div class="form-group">
                            <label for="allegati">Documenti correlati (PDF, Immagini extra...)</label>
                            <input type="file" id="allegati" name="allegati[]" multiple>
                            <small>Puoi caricare più file contemporaneamente.</small>
                        </div>
                    </section>

                    <section class="form-section">
                        <h3><i class="icon"></i> Link e Riferimenti</h3>
                        <div id="links-wrapper">
                            <div class="form-group">
                                <label>URL Fonte o Approfondimento</label>
                                <input type="url" name="links[]" placeholder="https://www.esempio.it/articolo">
                            </div>
                        </div>
                    </section>

                    <div class="form-footer-actions">
                        <button type="submit" class="btn-primary">Invia per la Revisione</button>
                        <a href="../home/home.php" class="btn-secondary">Torna indietro</a>
                    </div>
                </form>
            </div>
        </main>

        <?php render_footer("home-button", "../home/home.php", "Home"); ?>
    </body>
</html>