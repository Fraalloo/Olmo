<?php
    require_once "../../config/config.php";

    session_start();

    if(!isset($_SESSION["user_id"]) || empty($_SESSION["is_admin"])){
        http_response_code(403);
        exit("Accesso negato.");
    }

    $adminId = (int)$_SESSION["user_id"];
    $articleId = isset($_POST["id"]) ? (int)$_POST["id"] : 0;

    if($articleId <= 0){
        header("Location: convalida.php");
        exit;
    }

    $stmt = mysqli_prepare($conn, "
        SELECT id_gruppo_articolo
        FROM articoli
        WHERE
            id_articolo = ? AND
            id_admin IS NULL AND
            is_active = 0
    ");

    mysqli_stmt_bind_param($stmt, "i", $articleId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $articolo = mysqli_fetch_assoc($result);

    if(!$articolo){
        header("Location: convalida.php");
        exit;
    }

    $groupId = (int)$articolo["id_gruppo_articolo"];

    mysqli_begin_transaction($conn);

    try {
        $stmt = mysqli_prepare($conn, "
            UPDATE articoli
            SET is_active = 0
            WHERE id_gruppo_articolo = ?
        ");
        mysqli_stmt_bind_param($stmt, "i", $groupId);
        mysqli_stmt_execute($stmt);

        $stmt = mysqli_prepare($conn, "
            UPDATE articoli
            SET id_admin = ?, is_active = 1
            WHERE id_articolo = ?
        ");
        mysqli_stmt_bind_param($stmt, "ii", $adminId, $articleId);
        mysqli_stmt_execute($stmt);

        mysqli_commit($conn);
    }catch(Exception $e){
        mysqli_rollback($conn);
    }

    header("Location: convalida.php");
    exit;
?>