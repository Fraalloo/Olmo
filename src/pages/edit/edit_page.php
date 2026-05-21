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
    $articleId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

    if($articleId <= 0){
        header("Location: ../home/home.php");
        exit;
    }

    $articolo = extract_article_by_id($conn, $articleId, false);

    if(!$articolo || (int)$articolo["is_active"] !== 1 || !empty($articolo["is_hidden"]) || empty($articolo["id_admin"])){
        http_response_code(404);
        die("Articolo non modificabile.");
    }

    $tipi_articolo = extract_article_types($conn);
    $links = extract_article_links($conn, $articleId);
    $files = extract_article_files($conn, $articleId);

    $accept_banner_mime = implode(",", ALLOWED_PFP_MIME);
    $accept_article_file_mime = implode(",", ALLOWED_ARTICLE_FILE_MIME);
    $edit_error = $_SESSION["edit_error"] ?? "";
    unset($_SESSION["edit_error"]);
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title><?= esc(APP_NAME) ?> - Modifica Articolo</title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="../home/home.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        >
        <link rel="stylesheet" href="edit_page.css">

        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""
            defer
        ></script>
        <script src="edit_page.js" defer></script>
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
                    <h1>Modifica articolo</h1>
                    <p class="version-tag">
                        Verrà creata una nuova versione in attesa di convalida.
                    </p>
                </div>

                <?php if($edit_error !== ""): ?>
                    <p class="insert-alert insert-alert-error">
                        <?= esc($edit_error) ?>
                    </p>
                <?php endif; ?>

                <form
                    action="edit.php"
                    method="POST"
                    enctype="multipart/form-data"
                    class="standard-form"
                    id="article-form"
                >
                    <input type="hidden" name="id_articolo" value="<?= (int)$articolo["id_articolo"] ?>">

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
                                maxlength="100"
                                required
                                value="<?= esc($articolo["titolo"]) ?>"
                            >
                        </div>

                        <div class="form-group">
                            <label for="id_tipo_articolo">Tipo di Contenuto *</label>
                            <select
                                id="id_tipo_articolo"
                                name="id_tipo_articolo"
                                required
                            >
                                <?php foreach($tipi_articolo as $tipo): ?>
                                    <option
                                        value="<?= (int)$tipo["id_tipo_articolo"] ?>"
                                        <?= ((int)$tipo["id_tipo_articolo"] === (int)$articolo["id_tipo_articolo"]) ? "selected" : "" ?>
                                    >
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
                            ><?= esc($articolo["descrizione"]) ?></textarea>
                        </div>
                    </section>

                    <section class="form-section">
                        <h3>
                            <i class="icon"></i>
                            Coordinate del Luogo
                        </h3>

                        <div class="coords-grid">
                            <div class="form-group">
                                <label for="latitudine">Latitudine</label>
                                <input
                                    type="number"
                                    id="latitudine"
                                    name="latitudine"
                                    min="-90"
                                    max="90"
                                    step="0.000001"
                                    value="<?= esc($articolo["latitudine"] ?? "") ?>"
                                >
                            </div>

                            <div class="form-group">
                                <label for="longitudine">Longitudine</label>
                                <input
                                    type="number"
                                    id="longitudine"
                                    name="longitudine"
                                    min="-180"
                                    max="180"
                                    step="0.000001"
                                    value="<?= esc($articolo["longitudine"] ?? "") ?>"
                                >
                            </div>
                        </div>

                        <div class="coords-actions">
                            <button
                                type="button"
                                class="btn-secondary btn-toggle-map"
                                id="toggle-map"
                                aria-expanded="false"
                                aria-controls="map-picker"
                            >
                                Mostra mappa
                            </button>

                            <button
                                type="button"
                                class="btn-secondary btn-clear-coords"
                                id="clear-coords"
                            >
                                Rimuovi coordinate
                            </button>
                        </div>

                        <div class="map-picker" id="map-picker" hidden>
                            <div id="insert-map"></div>
                        </div>
                    </section>

                    <section class="form-section">
                        <h3>
                            <i class="icon"></i>
                            Media e Allegati
                        </h3>

                        <div class="form-group">
                            <label for="banner">Immagine Banner - Copertina</label>

                            <?php if(!empty($articolo["banner"])): ?>
                                <div class="current-resource-box">
                                    <h4>Banner attuale</h4>
                                    <img
                                        class="current-banner-preview"
                                        src="<?= esc(TO_ASSETS . $articolo["banner"]) ?>"
                                        alt="Banner attuale"
                                    >
                                    <label class="current-banner-choice">
                                        <input type="checkbox" name="keep_banner" value="1" checked>
                                        Mantieni il banner attuale se non ne carichi uno nuovo
                                    </label>
                                </div>
                            <?php endif; ?>

                            <input
                                type="file"
                                id="banner"
                                name="banner"
                                accept="<?= esc($accept_banner_mime) ?>"
                            >

                            <small>
                                Max dimensione: 2MB. Formati: JPG, PNG, WEBP.
                            </small>
                        </div>

                        <?php if(!empty($files)): ?>
                            <div class="form-group">
                                <label>File già collegati</label>

                                <div class="current-resource-box">
                                    <h4>Seleziona i file da mantenere nella nuova versione</h4>
                                    <ul class="current-resource-list">
                                        <?php foreach($files as $file): ?>
                                            <li>
                                                <label>
                                                    <input
                                                        type="checkbox"
                                                        name="existing_files[]"
                                                        value="<?= (int)$file["id_file"] ?>"
                                                        checked
                                                    >
                                                    <?= esc($file["nome_originale"]) ?>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="allegato_picker">Nuovi documenti correlati</label>

                            <div class="resource-row">
                                <input
                                    type="file"
                                    id="allegato_picker"
                                    accept="<?= esc($accept_article_file_mime) ?>"
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
                                accept="<?= esc($accept_article_file_mime) ?>"
                                multiple
                                style="display: none;"
                            >

                            <small>
                                I file mantenuti saranno associati alla nuova versione; quelli nuovi verranno caricati.
                            </small>

                            <div class="temporary-box" id="file-preview-box" hidden>
                                <h4>Nuovi file inseriti momentaneamente</h4>
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

                            <?php if(!empty($links)): ?>
                                <div class="current-resource-box">
                                    <h4>Seleziona i link da mantenere nella nuova versione</h4>
                                    <ul class="current-resource-list">
                                        <?php foreach($links as $link): ?>
                                            <li>
                                                <label>
                                                    <input
                                                        type="checkbox"
                                                        name="existing_links[]"
                                                        value="<?= esc($link["url_link"]) ?>"
                                                        checked
                                                    >
                                                    <?= esc($link["url_link"]) ?>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

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
                                I link selezionati saranno associati alla nuova versione; quelli nuovi verranno aggiunti.
                            </small>

                            <div id="links-hidden-fields"></div>

                            <div class="temporary-box" id="link-preview-box" hidden>
                                <h4>Nuovi link inseriti momentaneamente</h4>
                                <ul id="link-list" class="temporary-list"></ul>
                            </div>
                        </div>
                    </section>

                    <div class="form-footer-actions">
                        <button type="submit" class="btn-primary">
                            Invia modifica per la Revisione
                        </button>

                        <a href="../article/article.php?id=<?= (int)$articolo["id_articolo"] ?>" class="btn-secondary">
                            Torna all'articolo
                        </a>
                    </div>
                </form>
            </div>
        </main>

        <?php render_footer("home-button", "../home/home.php", "Home"); ?>
    </body>
</html>