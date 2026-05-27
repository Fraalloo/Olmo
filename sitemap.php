<?php
    require_once "src/config/config.php";

    function sitemap_base_url(){
        $isHttps = (
            (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ||
            (($_SERVER["SERVER_PORT"] ?? "") === "443")
        );
        $scheme = $isHttps ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"] ?? "localhost";
        $scriptDir = rtrim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? "")), "/");

        if($scriptDir === "." || $scriptDir === "/"){
            $scriptDir = "";
        }

        return $scheme."://".$host.$scriptDir;
    }

    function sitemap_xml($value){
        return htmlspecialchars((string)$value, ENT_XML1 | ENT_QUOTES, "UTF-8");
    }

    $baseUrl = sitemap_base_url();
    $staticPages = [
        "/index.php",
        "/src/pages/others/come_funziona.php",
        "/src/pages/others/chi_siamo.php",
        "/src/pages/others/contattaci.php",
        "/src/pages/others/privacy_policy.php",
        "/src/pages/others/terms_of_service.php",
        "/src/pages/others/cookie_settings.php",
    ];

    $articles = [];
    $query = "
        SELECT id_articolo, data_pubblicazione
        FROM articoli
        WHERE
            is_active = 1 AND
            is_hidden = 0 AND
            id_admin IS NOT NULL
        ORDER BY data_pubblicazione DESC, id_articolo DESC
    ";

    $result = mysqli_query($conn, $query);
    if($result){
        while($row = mysqli_fetch_assoc($result)){
            $articles[] = $row;
        }
    }

    header("Content-Type: application/xml; charset=UTF-8");

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach($staticPages as $page): ?>
        <url>
            <loc><?= sitemap_xml($baseUrl.$page) ?></loc>
        </url>
    <?php endforeach; ?>

    <?php foreach($articles as $article): ?>
        <url>
            <loc><?= sitemap_xml($baseUrl."/src/pages/article/article.php?id=".(int)$article["id_articolo"]) ?></loc>
            <lastmod><?= sitemap_xml($article["data_pubblicazione"]) ?></lastmod>
        </url>
    <?php endforeach; ?>
</urlset>