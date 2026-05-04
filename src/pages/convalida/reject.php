<?php
    require_once "../../config/config.php";

    session_start();

    if(!isset($_SESSION["user_id"]) || empty($_SESSION["is_admin"])){
        http_response_code(403);
        exit("Accesso negato.");
    }

    $articleId = isset($_POST["id"]) ? (int)$_POST["id"] : 0;

    if($articleId > 0){
        $stmt = mysqli_prepare($conn, "
            DELETE FROM articoli
            WHERE id_articolo = ?
            AND id_admin IS NULL
            AND is_active = 0
        ");

        mysqli_stmt_bind_param($stmt, "i", $articleId);
        mysqli_stmt_execute($stmt);
    }

    header("Location: convalida.php");
    exit;
?>