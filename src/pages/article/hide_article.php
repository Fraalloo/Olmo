<?php
    require_once "../../config/config.php";
    require_once "../../utils/article_versions.php";

    session_start();

    if(!isset($_SESSION["user_id"]) || empty($_SESSION["is_admin"])){
        http_response_code(403);
        exit("Accesso negato.");
    }

    $articleId = isset($_POST["id"]) ? (int)$_POST["id"] : 0;

    if($articleId <= 0){
        header("Location: ../home/home.php");
        exit;
    }

    $stmt = mysqli_prepare($conn, "
        SELECT id_gruppo_articolo
        FROM articoli
        WHERE id_articolo = ?
          AND id_admin IS NOT NULL
          AND is_hidden = 0
    ");

    mysqli_stmt_bind_param($stmt, "i", $articleId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $articolo = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if(!$articolo){
        header("Location: article.php?id=" . $articleId);
        exit;
    }

    $groupId = (int)$articolo["id_gruppo_articolo"];

    mysqli_begin_transaction($conn);

    try{
        $stmt = mysqli_prepare($conn, "
            UPDATE articoli
            SET is_hidden = 1,
                is_active = 0
            WHERE id_articolo = ?
              AND id_admin IS NOT NULL
        ");

        mysqli_stmt_bind_param($stmt, "i", $articleId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $newActiveId = recalculate_active_version($conn, $groupId);

        mysqli_commit($conn);

        if($newActiveId){
            header("Location: article.php?id=" . $newActiveId);
        } else {
            header("Location: ../home/home.php");
        }

        exit;
    }catch(Exception $e){
        mysqli_rollback($conn);
        http_response_code(500);
        exit("Errore durante l'eliminazione.");
    }
?>