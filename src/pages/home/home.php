<?php
    require_once "../../config/config.php";
    require_once "../../config/app.php";
    require_once "../../utils/extract_articles.php";
    require_once "../../utils/utils.php";
    
    session_start();

    const TO_ASSETS = "../../../";

    if(!isset($_SESSION["user_id"])){
        header("Location: ../../../index.php");
        exit;
    }

    $showOnlyMine = isset($_GET["mine"]) && $_GET["mine"] == "1";
    $mineQuery = $showOnlyMine ? "&mine=1" : "";
    $userId = (int)$_SESSION["user_id"];
    $isAdmin = $_SESSION["is_admin"];
    $pfp = $_SESSION["pfp"];
    $username = $_SESSION["username"];

    $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
    if($page < 1){
        $page = 1;
    }
    $limit = 10;

    $articoli = extract_articles($conn, $limit, $page, $showOnlyMine, $userId);
    $mapArticles = extract_map_articles($conn, $showOnlyMine, $userId);

    $totalArticles = count_active_articles($conn, $showOnlyMine, $userId);
    $totalPages = ceil($totalArticles / $limit);
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Homepage</title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="home.css">

        <!-- Importazione di LeafletJS: CSS -->
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        />
    </head>
    <body>
        <!-- Importazione di LeafletJS: JS -->
        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""
        ></script>

        <header class="topbar">
            <div class="topbar_left">
                <img class="topbar_pfp" src="<?= esc(TO_ASSETS . $pfp) ?>" alt="Foto profilo">
                <span class="topbar_username"><?= esc($username) ?></span>
            </div>

            <nav class="topbar_nav">
                <a href="#">Come funziona</a>
                <a href="#">Chi siamo</a>
                <a href="#">Contattaci</a>

                <form class="search-form" id="nominatimForm">
                    <input
                        type="text"
                        id="nominatimSearch"
                        name="nominatimSearch"
                        placeholder="Nominatim Search"
                        aria-label="Cerca luogo"
                    >
                    <button type="submit">Search</button>
                </form>

                <a class="btn-logout" href="../../auth/logout.php">Logout</a>
            </nav>
        </header>

        <main class="home-page">
            <section class="hero-section">
                <div class="map-card">
                    <div id="map"></div>
                </div>

                <div class="hero-actions">
                    <button class="btn-map" id="zoomToMarkersBtn">Centra tutti i luoghi</button>

                    <?php if($showOnlyMine): ?>
                        <a class="btn-toggle" href="home.php">Mostra tutti gli articoli</a>
                    <?php else: ?>
                        <a class="btn-toggle" href="home.php?mine=1">Mostra solo i miei articoli</a>
                    <?php endif; ?>

                    <?php if($isAdmin): ?>
                        <a class="btn-validate" href="#">Convalida contenuti</a>
                    <?php endif; ?>
                </div>
            </section>

            <section class="content-list-section">
                <h1>Luoghi, documenti e testimonianze:</h1>
                <p class="section-subtitle">Elenco completo degli articoli pubblicati</p>

                <?php if(empty($articoli)): ?>
                    <div class="empty-state">
                        <p>Non ci sono ancora contenuti pubblicati.</p>
                    </div>
                <?php else: ?>
                    <div class="articles-list">
                        <?php foreach($articoli as $articolo): ?>
                            <?php
                                $hasCoords = $articolo["latitudine"] !== null && $articolo["longitudine"] !== null;
                                $banner = !empty($articolo["banner"]) ? TO_ASSETS . $articolo["banner"] : "";
                            ?>
                            <article
                                class="article-card <?= $hasCoords ? 'has-coords' : 'no-coords' ?>"
                                data-article-id="<?= (int)$articolo["id_articolo"] ?>"
                                data-title="<?= esc($articolo["titolo"]) ?>"
                                data-lat="<?= $hasCoords ? esc($articolo["latitudine"]) : '' ?>"
                                data-lng="<?= $hasCoords ? esc($articolo["longitudine"]) : '' ?>"
                            >
                                <div class="article-card-header">
                                    <div>
                                        <span class="type-badge type-<?= esc($articolo["tipo_articolo"]) ?>">
                                            <?= esc(ucfirst($articolo["tipo_articolo"])) ?>
                                        </span>

                                        <?php if(!$hasCoords): ?>
                                            <span class="coords-badge coords-badge--missing">Senza coordinate</span>
                                        <?php else: ?>
                                            <span class="coords-badge coords-badge--ok">In mappa</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="article-card-body">
                                    <?php if(!empty($banner)): ?>
                                        <img class="article-banner" src="<?= esc($banner) ?>" alt="Banner <?= esc($articolo["titolo"]) ?>">
                                    <?php endif; ?>

                                    <div class="article-content">
                                        <h3><?= esc($articolo["titolo"]) ?></h3>

                                        <p class="meta">
                                            Pubblicato da <strong><?= esc($articolo["autore"]) ?></strong>
                                            il <?= esc(date("d/m/Y", strtotime($articolo["data_pubblicazione"]))) ?>
                                        </p>

                                        <p class="description">
                                            <?= substr(nl2br(esc($articolo["descrizione"])), 0, 500) ?>
                                            <?php if(strlen($articolo["descrizione"]) > 500): ?>
                                                <span class="description-cont">(continua...)</span>
                                            <?php endif; ?>
                                        </p>

                                        <?php if($hasCoords): ?>
                                            <p class="coords">
                                                Coordinate: <?= esc($articolo["latitudine"]) ?>, <?= esc($articolo["longitudine"]) ?>
                                            </p>
                                        <?php endif; ?>

                                        <div class="article-actions">
                                            <?php if($hasCoords): ?>
                                                <button
                                                    class="locate-on-map"
                                                    data-lat="<?= esc($articolo['latitudine']) ?>"
                                                    data-lng="<?= esc($articolo['longitudine']) ?>"
                                                >
                                                    Mostra sulla mappa
                                                </button>
                                            <?php endif; ?>

                                            <a class="btn-primary" href="#">
                                                Apri
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <div class="pagination">
                <?php if($page > 1):?>
                    <a class="page-arrow" href="?page=<?= $page - 1 ?><?= $mineQuery ?>">←</a>
                <?php endif; ?>

                <span>Pagina <?= $page ?> di <?= $totalPages ?></span>

                <?php if($page < $totalPages): ?>
                    <a class="page-arrow" href="?page=<?= $page + 1 ?><?= $mineQuery ?>">→</a>
                <?php endif; ?>
            </div>
        </main>

        <footer class="footer">
            <div class="footer-inner">
                <p>© 2026 Sotto l'Olmo. All rights reserved.</p>

                <a href="#" class="ins-button">Inserisci</a>

                <div class="footer-links">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">Cookies Settings</a>
                        <p>version <?= CURR_VERS ?></p>
                </div>
            </div>
        </footer>

        <!-- Dati da PHP e JS e importazione logica della mappa -->
        <script>
            window.mapArticles = <?= json_encode($mapArticles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        </script>
        <script type="module" src="home.js"></script>
    </body>
</html>