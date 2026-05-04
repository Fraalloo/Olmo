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
    }

        function addUserFilters(&$query, &$types, &$params, $filters){
        if(!empty($filters["search"])){
            $query .= " AND nome_utente LIKE ?";
            $types .= "s";
            $params[] = "%" . $filters["search"] . "%";
        }

        if($filters["role"] !== ""){
            if($filters["role"] === "admin"){
                $query .= " AND is_admin = 1";
            } elseif($filters["role"] === "user"){
                $query .= " AND is_admin = 0";
            }
        }

        if($filters["security"] !== ""){
            if($filters["security"] === "must_change"){
                $query .= " AND must_change_password = 1";
            } elseif($filters["security"] === "ok"){
                $query .= " AND must_change_password = 0";
            }
        }

        if(!empty($filters["date_from"])){
            $query .= " AND data_registrazione >= ?";
            $types .= "s";
            $params[] = $filters["date_from"];
        }

        if(!empty($filters["date_to"])){
            $query .= " AND data_registrazione <= ?";
            $types .= "s";
            $params[] = $filters["date_to"];
        }
    }
?>