<?php
    function where_for_id($onlyMine = false, $userId = null){
        $whereMine = "";

        if($onlyMine && $userId !== null){
            $userId = (int)$userId;
            $whereMine = " AND a.id_pubblicatore = $userId ";
        }
        
        return $whereMine;
    }

    function extract_articles($conn, $limit = 20, $page = 1, $onlyMine = false, $userId = null){
        $limit = (int)$limit;
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $limit;
        $whereMine = where_for_id($onlyMine, $userId);

        $result = mysqli_query($conn, "
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
                $whereMine
            ORDER BY
                a.data_pubblicazione DESC,
                a.titolo ASC
            LIMIT $limit OFFSET $offset
        ");
        $articoli = [];

        if($result){
            while ($row = mysqli_fetch_array($result)) {
                $articoli[] = $row;
            }
        }

        return $articoli;
    }

    function extract_map_articles($conn, $onlyMine = false, $userId = null){
        $whereMine = where_for_id($onlyMine, $userId);

        $result = mysqli_query($conn, "
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
                $whereMine
            ORDER BY a.data_pubblicazione DESC
        ");
        $articoli = [];

        if($result){
            while($row = mysqli_fetch_array($result)){
                $articoli[] = $row;
            }
        }

        return $articoli;
    }

    function count_active_articles($conn, $onlyMine = false, $userId = null){
        $whereMine = where_for_id($onlyMine, $userId);

        $result = mysqli_query($conn, "
            SELECT COUNT(*) AS totale
            FROM articoli a
            WHERE is_active = 1
            $whereMine
        ");
        $tot = mysqli_fetch_array($result);

        return (int)$tot["totale"];
    }
?>