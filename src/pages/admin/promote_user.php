<?php
    require_once "../../config/config.php";
    require_once "../../utils/auth_guard.php";

    session_start();

    require_login();
    require_admin();

    $userId = isset($_POST["id"]) ? (int)$_POST["id"] : 0;

    if($userId > 0){
        $stmt = mysqli_prepare($conn, "
            UPDATE utenti
            SET
                is_admin = 1,
                must_change_password = 1
            WHERE id_utente = ?
        ");

        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: dashboard.php");
    exit;
?>