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
                a.is_active = 1
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
            while($row = mysqli_fetch_array($result)){
                $articoli[] = $row;
            }
        }

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
            while($row = mysqli_fetch_array($result)){
                $articoli[] = $row;
            }
        }

        return $articoli;
    }

    function count_active_articles($conn, $onlyMine = false, $userId = null, $filters = []){
        $query = "
            SELECT COUNT(*) AS totale
            FROM articoli a, tipi_articoli t, utenti u
            WHERE
                a.id_tipo_articolo = t.id_tipo_articolo
                AND a.id_pubblicatore = u.id_utente
                AND a.is_active = 1
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

        $tot = mysqli_fetch_array($result);
        return (int)$tot["totale"];
    }
?>