<?php
    require_once "../../config/config.php";
    require_once "../../config/app.php";
    require_once "../../utils/utils.php";
    require_once "../../utils/extract_articles.php";
    require_once "../../utils/auth_guard.php";

    require_once "../../components/article_card/article_card.php";
    require_once "../../components/footer/footer.php";

    session_start();

    const TO_ASSETS = "../../../";

    require_login();
    require_password_change_if_needed("../profile/profile.php");
    require_admin();

    $pfp = $_SESSION["pfp"] ?? "";
    $username = $_SESSION["username"] ?? "";

    $page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
    if($page < 1){
        $page = 1;
    }
    $limit = 10;

    $articoli = extract_pending_articles($conn, $limit, $page);
    $totalArticles = count_pending_articles($conn);
    $totalPages = max(1, (int)ceil($totalArticles / $limit));
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Convalida</title>

        <link rel="stylesheet" href="../../../style.css">
        <link rel="stylesheet" href="../home/home.css">
        <link rel="stylesheet" href="../../components/article_card/article_card.css">
        <link rel="stylesheet" href="../../components/footer/footer.css">
        <link rel="stylesheet" href="convalida.css">
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

        <main class="validate-page">
            <h1>Convalida</h1>

            <?php if(empty($articoli)): ?>
                <p class="empty-state">Non ci sono articoli in attesa di approvazione.</p>
            <?php else: ?>
                <section class="articles-list validate-list">
                    <?php foreach($articoli as $articolo): ?>
                        <?php render_article_card($articolo, [
                            "to_assets" => TO_ASSETS,
                            "open_url" => "../article/article.php",
                            "show_map_button" => false,
                            "show_validation_actions" => true
                        ]); ?>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

            <?php if($totalPages > 1): ?>
                <div class="pagination">
                    <?php if($page > 1): ?>
                        <a class="page-arrow" href="?page=<?= $page - 1 ?>">←</a>
                    <?php endif; ?>

                    <span>Pagina <?= $page ?> di <?= $totalPages ?></span>

                    <?php if($page < $totalPages): ?>
                        <a class="page-arrow" href="?page=<?= $page + 1 ?>">→</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>

        <?php render_footer("home-button", "../home/home.php", "Home"); ?>

        <?php render_article_validation_modals(); ?>
        <script src="../../components/article_card/article_card.js"></script>
    </body>
</html>