<?php
    require_once "../../config/config.php";
    require_once "../../config/app.php";
    require_once "../../utils/utils.php";
    require_once "../../utils/extract_articles.php";
    require_once "../../utils/auth_guard.php";

    require_once "../../components/footer/footer.php";
    require_once "../../components/article_admin_actions/article_admin_actions.php";

    session_start();

    const TO_ASSETS = "../../../";

    require_login();
    require_password_change_if_needed("../profile/profile.php");

    $userId = (int)$_SESSION["user_id"];
    $isAdmin = !empty($_SESSION["is_admin"]);
    $pfp = $_SESSION["pfp"] ?? "";
    $username = $_SESSION["username"] ?? "";
    $articleId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

    if($articleId <= 0){
        header("Location: ../home/home.php");
        exit;
    }

    $articolo = extract_article_by_id($conn, $articleId, $isAdmin);

    if(!$articolo){
        http_response_code(404);
        die("Articolo non trovato.");
    }

    $versioni = extract_versions($conn, (int)$articolo["id_gruppo_articolo"], $isAdmin);
    $links = extract_article_links($conn, $articleId);
    $files = extract_article_files($conn, $articleId);

    $hasCoords = $articolo["latitudine"] !== null && $articolo["longitudine"] !== null;
    $banner = !empty($articolo["banner"]) ? TO_ASSETS . $articolo["banner"] : null;
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= esc($articolo["titolo"]) ?></title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="article.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">
        <link rel="stylesheet" href="../../components/article_admin_actions/article_admin_actions.css">
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
                <a href="#">Come funziona</a>
                <a href="#">Chi siamo</a>
                <a href="#">Contattaci</a>

                <a class="btn-home" href="../home/home.php">Home</a>
                <a class="btn-logout" href="../../auth/logout.php">Logout</a>
            </nav>
        </header>

        <main class="article-page">
            <?php if($banner): ?>
                <section class="article-hero">
                    <img src="<?= esc($banner) ?>" alt="Immagine articolo <?= esc($articolo["titolo"]) ?>">
                </section>
            <?php endif; ?>

            <section class="article-layout">
                <article class="article-main">
                    <div class="article-title-row">
                        <div>
                            <span class="type-badge">
                                <?= esc(ucfirst($articolo["tipo_articolo"])) ?>
                            </span>

                            <?php if($isAdmin && empty($articolo["id_admin"])): ?>
                                <span class="article-status-badge article-status-pending">
                                    In attesa
                                </span>
                            <?php endif; ?>

                            <?php if($isAdmin && !empty($articolo["is_hidden"])): ?>
                                <span class="article-status-badge article-status-hidden">
                                    Hidden
                                </span>
                            <?php endif; ?>

                            <h1><?= esc($articolo["titolo"]) ?></h1>

                            <p class="article-meta">
                                Pubblicato da <strong><?= esc($articolo["autore"]) ?></strong>
                                il <?= esc(date("d/m/Y", strtotime($articolo["data_pubblicazione"]))) ?>
                            </p>
                        </div>

                        <div class="article-actions">
                            <?php if((int)$articolo["is_active"] === 1 && empty($articolo["is_hidden"]) && !empty($articolo["id_admin"])): ?>
                                <a class="btn-edit" href="#">
                                    Modifica
                                </a>
                            <?php endif; ?>

                            <?php if($isAdmin): ?>
                                <?php render_article_admin_actions($articolo); ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="article-description">
                        <?= nl2br(esc($articolo["descrizione"])) ?>
                    </div>
                </article>

                <aside class="article-sidebar">
                    <h2>Approfondimenti</h2>

                    <div class="info-box">
                        <h3>Dettagli</h3>

                        <p>
                            <strong>Tipo:</strong>
                            <?= esc($articolo["tipo_articolo"]) ?>
                        </p>

                        <p>
                            <strong>Versione corrente:</strong>
                            <?= (int)$articolo["versione"] ?>
                        </p>

                        <?php if($isAdmin): ?>
                            <p>
                                <strong>Stato:</strong>

                                <?php if(empty($articolo["id_admin"])): ?>
                                    In attesa di approvazione
                                <?php elseif(!empty($articolo["is_hidden"])): ?>
                                    Hidden
                                <?php elseif((int)$articolo["is_active"] === 1): ?>
                                    Attiva
                                <?php else: ?>
                                    Storica
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <?php if(!empty($versioni)): ?>
                            <form class="version-form" method="GET" action="article.php">
                                <label for="version-select">
                                    <strong><?= $isAdmin ? "Tutte le versioni:" : "Versioni approvate:" ?></strong>
                                </label>

                                <select
                                    id="version-select"
                                    name="id"
                                    class="version-select"
                                    onchange="this.form.submit()"
                                >
                                    <?php foreach($versioni as $versione): ?>
                                        <option
                                            value="<?= (int)$versione["id_articolo"] ?>"
                                            <?= ((int)$versione["id_articolo"] === (int)$articolo["id_articolo"]) ? "selected" : "" ?>
                                        >
                                            Versione <?= (int)$versione["versione"] ?>
                                            <?= ((int)$versione["is_active"] === 1) ? " - attiva" : "" ?>
                                            <?= empty($versione["id_admin"]) ? " - in attesa" : "" ?>
                                            <?= !empty($versione["is_hidden"]) ? " - hidden" : "" ?>
                                            - <?= esc(date("d/m/Y", strtotime($versione["data_pubblicazione"]))) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        <?php endif; ?>

                        <?php if(!empty($articolo["admin_nome"])): ?>
                            <p>
                                <strong>Convalidato da:</strong>
                                <?= esc($articolo["admin_nome"]) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if($hasCoords): ?>
                        <div class="info-box">
                            <h3>Coordinate</h3>

                            <p>
                                <?= esc($articolo["latitudine"]) ?>,
                                <?= esc($articolo["longitudine"]) ?>
                            </p>

                            <a
                                class="side-link"
                                href="https://www.openstreetmap.org/?mlat=<?= esc($articolo["latitudine"]) ?>&mlon=<?= esc($articolo["longitudine"]) ?>#map=16/<?= esc($articolo["latitudine"]) ?>/<?= esc($articolo["longitudine"]) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                Apri su OpenStreetMap
                            </a>
                        </div>

                        <div class="info-box">
                            <h3>Meteo attuale</h3>

                            <p
                                id="weatherBox"
                                data-lat="<?= esc($articolo["latitudine"]) ?>"
                                data-lng="<?= esc($articolo["longitudine"]) ?>"
                            >
                                Caricamento meteo...
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="info-box">
                            <h3>Coordinate</h3>
                            <p>Coordinate non disponibili.</p>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($links)): ?>
                        <div class="info-box">
                            <h3>Link utili</h3>

                            <?php foreach($links as $link): ?>
                                <a
                                    class="side-link"
                                    href="<?= esc($link["url_link"]) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <?= esc($link["url_link"]) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($files)): ?>
                        <div class="info-box">
                            <h3>File allegati</h3>

                            <?php foreach($files as $file): ?>
                                <a
                                    class="side-link"
                                    href="<?= esc(TO_ASSETS . $file["file_path"]) ?>"
                                    download="<?= esc($file["nome_originale"]) ?>"
                                >
                                    <?= esc($file["nome_originale"]) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </aside>
            </section>
        </main>

        <?php render_footer("home-button", "../home/home.php", "Home"); ?>

        <?php if($isAdmin): ?>
            <?php render_article_admin_modals(); ?>
            <script src="../../components/article_admin_actions/article_admin_actions.js"></script>
        <?php endif; ?>

        <script type="module" src="article.js"></script>
    </body>
</html>