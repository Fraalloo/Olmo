<?php
    require_once "filters.php";

    function extract_articles($conn, $limit = 20, $page = 1, $onlyMine = false, $userId = null, $filters = []){
        $limit = (int)$limit;
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $limit;

        $query = "
            SELECT
                a.id_articolo,
                a.id_gruppo_articolo,
                a.titolo,
                a.descrizione,
                a.banner,
                a.latitudine,
                a.longitudine,
                a.data_pubblicazione,
                a.versione,
                t.descrizione AS tipo_articolo,
                u.nome_utente AS autore
            FROM articoli a, tipi_articoli t, utenti u
            WHERE
                a.id_tipo_articolo = t.id_tipo_articolo AND
                a.id_pubblicatore = u.id_utente AND
                a.is_active = 1 AND
                a.is_hidden = 0 AND
                a.id_admin IS NOT NULL
        ";

        $params = [];
        $types = "";

        addOnlyMineFilter($query, $types, $params, $onlyMine, $userId);
        addAdvancedFilters($query, $types, $params, $filters);

        $query .= " ORDER BY a.data_pubblicazione DESC LIMIT ? OFFSET ?";
        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = mysqli_prepare($conn, $query);
        if($types !== ""){
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $articoli = [];
        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $articoli[] = $row;
            }
        }

        mysqli_stmt_close($stmt);

        return $articoli;
    }

    function extract_map_articles($conn, $onlyMine = false, $userId = null, $search = ""){
        $query = "
            SELECT
                a.id_articolo,
                a.titolo,
                a.descrizione,
                a.latitudine,
                a.longitudine,
                t.descrizione AS tipo_articolo
            FROM articoli a, tipi_articoli t
            WHERE
                a.id_tipo_articolo = t.id_tipo_articolo AND
                a.is_active = 1 AND
                a.is_hidden = 0 AND
                a.id_admin IS NOT NULL AND
                a.latitudine IS NOT NULL AND
                a.longitudine IS NOT NULL AND
                t.descrizione = 'luogo'
        ";

        $params = [];
        $types = "";

        addOnlyMineFilter($query, $types, $params, $onlyMine, $userId);
        addSearchFilter($query, $types, $params, $search);

        $query .= " ORDER BY a.data_pubblicazione DESC";

        $stmt = mysqli_prepare($conn, $query);
        if($types !== ""){
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $articoli = [];
        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $articoli[] = $row;
            }
        }

        mysqli_stmt_close($stmt);

        return $articoli;
    }

    function count_active_articles($conn, $onlyMine = false, $userId = null, $filters = []){
        $query = "
            SELECT COUNT(*) AS totale
            FROM articoli a, tipi_articoli t, utenti u
            WHERE
                a.id_tipo_articolo = t.id_tipo_articolo AND
                a.id_pubblicatore = u.id_utente AND
                a.is_active = 1 AND
                a.is_hidden = 0 AND
                a.id_admin IS NOT NULL
        ";

        $params = [];
        $types = "";

        addOnlyMineFilter($query, $types, $params, $onlyMine, $userId);
        addAdvancedFilters($query, $types, $params, $filters);

        $stmt = mysqli_prepare($conn, $query);
        if($types !== ""){
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $tot = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return (int)$tot["totale"];
    }

    function extract_versions($conn, $groupId, $isAdmin = false){
        $query = "
            SELECT
                a.id_articolo,
                a.versione,
                a.data_pubblicazione,
                a.is_active,
                a.is_hidden,
                a.id_admin,
                u.nome_utente AS admin_nome
            FROM articoli a
            LEFT JOIN utenti u ON a.id_admin = u.id_utente
            WHERE a.id_gruppo_articolo = ?
        ";

        if(!$isAdmin){
            $query .= "
                AND a.id_admin IS NOT NULL
                AND a.is_hidden = 0
            ";
        }

        $query .= " ORDER BY a.versione DESC";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $groupId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $versioni = [];

        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $versioni[] = $row;
            }
        }

        mysqli_stmt_close($stmt);

        return $versioni;
    }

    function extract_pending_articles($conn, $limit = 10, $page = 1){
        $limit = (int)$limit;
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $limit;

        $query = "
            SELECT
                a.id_articolo,
                a.id_gruppo_articolo,
                a.titolo,
                a.descrizione,
                a.banner,
                a.latitudine,
                a.longitudine,
                a.data_pubblicazione,
                a.versione,
                t.descrizione AS tipo_articolo,
                u.nome_utente AS autore
            FROM articoli a
            JOIN tipi_articoli t ON a.id_tipo_articolo = t.id_tipo_articolo
            JOIN utenti u ON a.id_pubblicatore = u.id_utente
            WHERE
                a.id_admin IS NULL AND
                a.is_active = 0 AND
                a.is_hidden = 0
            ORDER BY a.data_pubblicazione DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $articoli = [];

        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $articoli[] = $row;
            }
        }

        mysqli_stmt_close($stmt);

        return $articoli;
    }

    function count_pending_articles($conn){
        $query = "
            SELECT COUNT(*) AS totale
            FROM articoli
            WHERE
                id_admin IS NULL AND
                is_active = 0 AND
                is_hidden = 0
        ";

        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);

        return (int)$row["totale"];
    }

    function extract_article_by_id($conn, $articleId, $isAdmin = false){
        $query = "
            SELECT
                a.id_articolo,
                a.id_gruppo_articolo,
                a.banner,
                a.titolo,
                a.descrizione,
                a.latitudine,
                a.longitudine,
                a.data_pubblicazione,
                a.versione,
                a.is_active,
                a.is_hidden,
                a.id_admin,
                t.descrizione AS tipo_articolo,
                u.nome_utente AS autore,
                ad.nome_utente AS admin_nome
            FROM articoli a
            JOIN tipi_articoli t ON a.id_tipo_articolo = t.id_tipo_articolo
            JOIN utenti u ON a.id_pubblicatore = u.id_utente
            LEFT JOIN utenti ad ON a.id_admin = ad.id_utente
            WHERE a.id_articolo = ?
        ";

        if(!$isAdmin){
            $query .= "
                AND a.id_admin IS NOT NULL
                AND a.is_hidden = 0
            ";
        }

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $articleId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $article = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return $article;
    }

    function extract_article_links($conn, $articleId){
        $query = "
            SELECT url_link
            FROM link_articoli
            WHERE id_articolo = ?
            ORDER BY url_link
        ";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $articleId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $links = [];

        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $links[] = $row;
            }
        }

        mysqli_stmt_close($stmt);

        return $links;
    }

    function extract_article_files($conn, $articleId){
        $query = "
            SELECT nome_originale, file_path, mime_type, data_upload
            FROM file_articoli
            WHERE id_articolo = ?
            ORDER BY data_upload DESC
        ";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $articleId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $files = [];

        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $files[] = $row;
            }
        }

        mysqli_stmt_close($stmt);

        return $files;
    }
?>