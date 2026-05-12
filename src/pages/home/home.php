<?php
    require_once "../../config/config.php";
    require_once "../../config/app.php";
    require_once "../../utils/extract_articles.php";
    require_once "../../utils/utils.php";
    require_once "../../utils/auth_guard.php";

    require_once "../../components/article_card/article_card.php";
    require_once "../../components/footer/footer.php";
    
    session_start();

    const TO_ASSETS = "../../../";

    require_login();
    require_password_change_if_needed("../profile/profile.php");

    // Valori dell'utente
    $userId = (int)$_SESSION["user_id"];
    $isAdmin = $_SESSION["is_admin"];
    $pfp = $_SESSION["pfp"];
    $username = $_SESSION["username"];

    // Paginazione
    $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
    if($page < 1){
        $page = 1;
    }
    $limit = 10;

    // Filtri
    $showOnlyMine = isset($_GET["mine"]) && $_GET["mine"] == "1";
    $search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
    $type = isset($_GET["type"]) ? trim($_GET["type"]) : "";
    $author = isset($_GET["author"]) ? trim($_GET["author"]) : "";
    $coords = isset($_GET["coords"]) ? trim($_GET["coords"]) : "";
    $dateFrom = isset($_GET["date_from"]) ? trim($_GET["date_from"]) : "";
    $dateTo = isset($_GET["date_to"]) ? trim($_GET["date_to"]) : "";
    $filtersOpen = isset($_GET["filters_open"]) && $_GET["filters_open"] === "1";
    $filters = [
        "search" => $search,
        "type" => $type,
        "author" => $author,
        "coords" => $coords,
        "date_from" => $dateFrom,
        "date_to" => $dateTo,
    ];
    $queryExtra = "";

    // Conservazione dei filtri nella paginazione
    if($showOnlyMine) $queryExtra .= "&mine=1";
    if($search !== "") $queryExtra .= "&search=" . urlencode($search);
    if($type !== "") $queryExtra .= "&type=" . urlencode($type);
    if($author !== "") $queryExtra .= "&author=" . urlencode($author);
    if($coords !== "") $queryExtra .= "&coords=" . urlencode($coords);
    if($dateFrom !== "") $queryExtra .= "&date_from=" . urlencode($dateFrom);
    if($dateTo !== "") $queryExtra .= "&date_to=" . urlencode($dateTo);
    if($filtersOpen) $queryExtra .= "&filters_open=1";

    $articoli = extract_articles($conn, $limit, $page, $showOnlyMine, $userId, $filters);
    $mapArticles = extract_map_articles($conn, $showOnlyMine, $userId, $search);

    $totalArticles = count_active_articles($conn, $showOnlyMine, $userId, $filters);
    $totalPages = max(1, (int)ceil($totalArticles / $limit));
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Homepage</title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="home.css">
        <link rel="stylesheet" href="../../components/article_card/article_card.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">

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
                        <a class="btn-validate" href="../convalida/convalida.php">Convalida contenuti</a>
                        <a class="btn-validate" href="../admin/dashboard.php">Dashboard admin</a>
                    <?php endif; ?>
                </div>
            </section>

            <form class="article-search-form" method="GET" action="home.php" id="articleFiltersForm">
                <?php if($showOnlyMine): ?>
                    <input type="hidden" name="mine" value="1">
                <?php endif; ?>
                <input
                    type="hidden"
                    name="filters_open"
                    id="filtersOpenInput"
                    value="<?= isset($_GET["filters_open"]) && $_GET["filters_open"] === "1" ? "1" : "0" ?>"
                >

                <div class="search-main-row">
                    <input
                        type="text"
                        name="search"
                        id="articleSearchInput"
                        placeholder="Cerca per titolo o testo..."
                        value="<?= esc($search) ?>"
                    >

                    <button type="button" id="toggleFiltersBtn" class="btn-filter-toggle">
                        Mostra Filtri
                    </button>

                    <button type="submit">Cerca</button>

                    <a href="home.php<?= $showOnlyMine ? '?mine=1' : '' ?>">Annulla</a>
                </div>

                <div
                    class="advanced-filters <?= isset($_GET["filters_open"]) && $_GET["filters_open"] === "1" ? "is-open" : "" ?>"
                    id="advancedFilters"
                >
                    <select name="type">
                        <option value="">Tutti i tipi</option>
                        <option value="luogo" <?= $type === "luogo" ? "selected" : "" ?>>Luoghi</option>
                        <option value="documento" <?= $type === "documento" ? "selected" : "" ?>>Documenti</option>
                        <option value="testimonianza" <?= $type === "testimonianza" ? "selected" : "" ?>>Testimonianze</option>
                    </select>

                    <input type="text" name="author" placeholder="Autore..." value="<?= esc($author) ?>">

                    <select name="coords">
                        <option value="">Coordinate: tutte</option>
                        <option value="with" <?= $coords === "with" ? "selected" : "" ?>>Solo con coordinate</option>
                        <option value="without" <?= $coords === "without" ? "selected" : "" ?>>Solo senza coordinate</option>
                    </select>

                    <label class="date-filter">
                        <span>Data di partenza</span>
                        <input type="date" name="date_from" value="<?= esc($dateFrom) ?>">
                    </label>

                    <label class="date-filter">
                        <span>Data di fine</span>
                        <input type="date" name="date_to" value="<?= esc($dateTo) ?>">
                    </label>
                </div>
            </form>

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
                            <?php render_article_card($articolo, [
                                "to_assets" => TO_ASSETS,
                                "open_url" => "../article/article.php",
                                "show_map_button" => true,
                                "show_validation_actions" => false
                            ]); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <div class="pagination">
                <?php if($page > 1):?>
                    <a class="page-arrow" href="?page=<?= $page - 1 ?><?= $queryExtra ?>">←</a>
                <?php endif; ?>

                <span>Pagina <?= $page ?> di <?= $totalPages ?></span>

                <?php if($page < $totalPages): ?>
                    <a class="page-arrow" href="?page=<?= $page + 1 ?><?= $queryExtra ?>">→</a>
                <?php endif; ?>
            </div>
        </main>

        <?php render_footer("ins-button", "../insert/insert_page.php", "Inserisci"); ?>

        <!-- Dati da PHP e JS e importazione logica della mappa -->
        <script>
            window.mapArticles = <?= json_encode($mapArticles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        </script>
        <script type="module" src="home.js"></script>
    </body>
</html>