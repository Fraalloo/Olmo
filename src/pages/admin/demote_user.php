<?php
    require_once "../../config/config.php";
    require_once "../../config/app.php";
    require_once "../../utils/auth_guard.php";

    session_start();

    require_login();
    require_admin();

    $currentUserId = (int)$_SESSION["user_id"];
    $userId = isset($_POST["id"]) ? (int)$_POST["id"] : 0;

    if($userId <= 0 || $userId === $currentUserId){
        header("Location: dashboard.php");
        exit;
    }

    $stmt = mysqli_prepare($conn, "
        SELECT nome_utente
        FROM utenti
        WHERE id_utente = ?
    ");

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $utente = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if(!$utente || $utente["id_utente"] == 1){
        header("Location: dashboard.php");
        exit;
    }

    $stmt = mysqli_prepare($conn, "
        SELECT COUNT(*) AS totale
        FROM utenti
        WHERE is_admin = 1
    ");

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if((int)$row["totale"] > 1){
        $stmt = mysqli_prepare($conn, "
            UPDATE utenti
            SET is_admin = 0
            WHERE id_utente = ?
              AND nome_utente <> 'DBAdmin'
        ");

        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: dashboard.php");
    exit;
?>