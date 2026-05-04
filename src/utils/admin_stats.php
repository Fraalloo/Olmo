<?php
    require_once "filters.php";

    function count_users($conn){
        $result = mysqli_query($conn, "SELECT COUNT(*) AS totale FROM utenti");
        $row = mysqli_fetch_assoc($result);
        return (int)$row["totale"];
    }

    function count_admins($conn){
        $result = mysqli_query($conn, "SELECT COUNT(*) AS totale FROM utenti WHERE is_admin = 1");
        $row = mysqli_fetch_assoc($result);
        return (int)$row["totale"];
    }

    function count_hidden_articles($conn){
        $result = mysqli_query($conn, "SELECT COUNT(*) AS totale FROM articoli WHERE is_hidden = 1");
        $row = mysqli_fetch_assoc($result);
        return (int)$row["totale"];
    }

    function count_total_versions($conn){
        $result = mysqli_query($conn, "SELECT COUNT(*) AS totale FROM articoli");
        $row = mysqli_fetch_assoc($result);
        return (int)$row["totale"];
    }

    function extract_users_for_admin($conn, $limit = 10, $page = 1, $filters = []){
        $limit = (int)$limit;
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $limit;

        $query = "
            SELECT
                id_utente,
                nome_utente,
                pfp,
                data_registrazione,
                is_admin,
                must_change_password
            FROM utenti
            WHERE 1 = 1
        ";

        $params = [];
        $types = "";

        addUserFilters($query, $types, $params, $filters);

        $query .= "
            ORDER BY is_admin DESC, nome_utente ASC
            LIMIT ? OFFSET ?
        ";

        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = mysqli_prepare($conn, $query);

        if($types !== ""){
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $utenti = [];

        if($result){
            while($row = mysqli_fetch_assoc($result)){
                $utenti[] = $row;
            }
        }

        mysqli_stmt_close($stmt);

        return $utenti;
    }

    function count_filtered_users($conn, $filters = []){
        $query = "
            SELECT COUNT(*) AS totale
            FROM utenti
            WHERE 1 = 1
        ";

        $params = [];
        $types = "";

        addUserFilters($query, $types, $params, $filters);

        $stmt = mysqli_prepare($conn, $query);

        if($types !== ""){
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return (int)$row["totale"];
    }
?>