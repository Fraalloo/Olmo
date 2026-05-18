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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title><?= esc(APP_NAME) ?> - Nuovo Articolo</title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="../home/home.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">
        <link rel="stylesheet" href="insert_page.css">

        <script src="insert_page.js" defer></script>
    </head>

    <body>
        <header class="topbar">
            <a class="topbar_profile" href="../profile/profile.php">
                <?php if(!empty($pfp)): ?>
                    <img 
                        class="topbar_pfp" 
                        src="<?= esc(TO_ASSETS . $pfp) ?>" 
                        alt="Foto profilo"
                    >
                <?php endif; ?>

                <span class="topbar_username">
                    <?= esc($username) ?>
                </span>
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

                <form 
                    action="insert.php" 
                    method="POST" 
                    enctype="multipart/form-data" 
                    class="standard-form" 
                    id="article-form"
                >
                    <section class="form-section">
                        <h3>
                            <i class="icon"></i>
                            Informazioni Base
                        </h3>

                        <div class="form-group">
                            <label for="titolo">Titolo dell'articolo *</label>
                            <input 
                                type="text" 
                                id="titolo" 
                                name="titolo" 
                                required 
                                placeholder="Inserisci il titolo..."
                            >
                        </div>

                        <div class="form-group">
                            <label for="id_tipo_articolo">Tipo di Contenuto *</label>
                            <select 
                                id="id_tipo_articolo" 
                                name="id_tipo_articolo" 
                                required
                            >
                                <option value="" disabled selected>
                                    Scegli una categoria...
                                </option>

                                <?php foreach($tipi_articolo as $tipo): ?>
                                    <option value="<?= (int) $tipo["id_tipo_articolo"] ?>">
                                        <?= esc(ucfirst($tipo["descrizione"])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="descrizione">Descrizione Completa *</label>
                            <textarea 
                                id="descrizione" 
                                name="descrizione" 
                                rows="10" 
                                required 
                                placeholder="Racconta la storia..."
                            ></textarea>
                        </div>
                    </section>

                    <section class="form-section">
                        <h3>
                            <i class="icon"></i>
                            Media e Allegati
                        </h3>

                        <div class="form-group">
                            <label for="banner">Immagine Banner - Copertina</label>

                            <input 
                                type="file" 
                                id="banner" 
                                name="banner" 
                                accept="<?= esc($accept_mime) ?>"
                            >

                            <small>
                                Max dimensione: 2MB. Formati: JPG, PNG, WEBP.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="allegato_picker">Documenti correlati</label>

                            <div class="resource-row">
                                <input 
                                    type="file" 
                                    id="allegato_picker"
                                    multiple
                                >

                                <button 
                                    type="button" 
                                    id="add-file" 
                                    class="btn-add-resource"
                                >
                                    Aggiungi file
                                </button>
                            </div>

                            <input 
                                type="file" 
                                id="allegati" 
                                name="allegati[]" 
                                multiple
                                style="display: none;"
                            >

                            <small>
                                Seleziona uno o più file, clicca “Aggiungi file” e poi puoi selezionarne altri.
                            </small>

                            <div class="temporary-box" id="file-preview-box" hidden>
                                <h4>File inseriti momentaneamente</h4>
                                <ul id="file-list" class="temporary-list"></ul>
                            </div>
                        </div>
                    </section>

                    <section class="form-section">
                        <h3>
                            <i class="icon"></i>
                            Link e Riferimenti
                        </h3>

                        <div class="form-group">
                            <label for="link_input">URL Fonte o Approfondimento</label>

                            <div class="resource-row">
                                <input 
                                    type="url" 
                                    id="link_input" 
                                    placeholder="https://www.esempio.it/articolo"
                                >

                                <button 
                                    type="button" 
                                    id="add-link" 
                                    class="btn-add-resource"
                                >
                                    Aggiungi link
                                </button>
                            </div>

                            <small>
                                Inserisci un link, clicca “Aggiungi link” e poi puoi inserirne un altro.
                            </small>

                            <div id="links-hidden-fields"></div>

                            <div class="temporary-box" id="link-preview-box" hidden>
                                <h4>Link inseriti momentaneamente</h4>
                                <ul id="link-list" class="temporary-list"></ul>
                            </div>
                        </div>
                    </section>

                    <div class="form-footer-actions">
                        <button type="submit" class="btn-primary">
                            Invia per la Revisione
                        </button>

                        <a href="../home/home.php" class="btn-secondary">
                            Torna indietro
                        </a>
                    </div>
                </form>
            </div>
        </main>

        <?php render_footer("home-button", "../home/home.php", "Home"); ?>
    </body>
</html>