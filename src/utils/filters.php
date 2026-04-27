<?php
    function addOnlyMineFilter(&$query, &$types, &$params, $onlyMine, $userId){
        if($onlyMine && $userId !== null){
            $query .= " AND a.id_pubblicatore = ?";
            $types .= "i";
            $params[] = $userId;
        }
    }

    function addSearchFilter(&$query, &$types, &$params, $search){
        if($search !== ""){
            $query .= " AND (a.titolo LIKE ? OR a.descrizione LIKE ?)";
            $types .= "ss";
            $like = "%" . $search . "%";
            $params[] = $like;
            $params[] = $like;
        }
    }

    function addAdvancedFilters(&$query, &$types, &$params, $filters){
        $search = $filters["search"] ?? "";
        $type = $filters["type"] ?? "";
        $author = $filters["author"] ?? "";
        $coords = $filters["coords"] ?? "";
        $dateFrom = $filters["date_from"] ?? "";
        $dateTo = $filters["date_to"] ?? "";
        $hasBanner = $filters["has_banner"] ?? false;

        addSearchFilter($query, $types, $params, $search);

        if($type !== ""){
            $query .= " AND t.descrizione = ?";
            $types .= "s";
            $params[] = $type;
        }

        if($author !== ""){
            $query .= " AND u.nome_utente LIKE ?";
            $types .= "s";
            $params[] = "%" . $author . "%";
        }

        if($coords === "with"){
            $query .= " AND a.latitudine IS NOT NULL AND a.longitudine IS NOT NULL";
        }

        if($coords === "without"){
            $query .= " AND (a.latitudine IS NULL OR a.longitudine IS NULL)";
        }

        if($dateFrom !== ""){
            $query .= " AND a.data_pubblicazione >= ?";
            $types .= "s";
            $params[] = $dateFrom;
        }

        if($dateTo !== ""){
            $query .= " AND a.data_pubblicazione <= ?";
            $types .= "s";
            $params[] = $dateTo;
        }

        if($hasBanner){
            $query .= " AND a.banner IS NOT NULL AND a.banner <> ''";
        }
    }
?>