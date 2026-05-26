<?php
    require_once "../../config/config.php";
    require_once "../../config/app.php";
    require_once "../../../routine/cleanup_uploads_funcs.php";

    session_start();

    if(!isset($_SESSION["user_id"]) || empty($_SESSION["is_admin"])){
        http_response_code(403);
        exit("Accesso negato.");
    }

    $articleId = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
    $groupId = null;
    $bannerPath = null;
    $filePaths = [];

    if($articleId > 0){
        mysqli_begin_transaction($conn);

        try{
            $stmt = mysqli_prepare($conn, "
                SELECT id_gruppo_articolo, banner
                FROM articoli
                WHERE
                    id_articolo = ? AND
                    id_admin IS NULL AND
                    is_active = 0
            ");
            mysqli_stmt_bind_param($stmt, "i", $articleId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $article = mysqli_fetch_array($result);
            mysqli_stmt_close($stmt);

            if($article){
                $groupId = (int)$article["id_gruppo_articolo"];
                $bannerPath = $article["banner"];

                $stmt = mysqli_prepare($conn, "
                    SELECT file_path
                    FROM file_articoli
                    WHERE id_articolo = ?
                ");
                mysqli_stmt_bind_param($stmt, "i", $articleId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                while($file = mysqli_fetch_array($result)){
                    $filePaths[] = $file["file_path"];
                }

                mysqli_stmt_close($stmt);

                $stmt = mysqli_prepare($conn, "
                    DELETE FROM articoli
                    WHERE
                        id_articolo = ? AND
                        id_admin IS NULL AND
                        is_active = 0
                ");
                mysqli_stmt_bind_param($stmt, "i", $articleId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                $stmt = mysqli_prepare($conn, "
                    SELECT COUNT(*) AS totale
                    FROM articoli
                    WHERE id_gruppo_articolo = ?
                ");
                mysqli_stmt_bind_param($stmt, "i", $groupId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $groupArticles = mysqli_fetch_array($result);
                mysqli_stmt_close($stmt);

                if((int)$groupArticles["totale"] === 0){
                    $stmt = mysqli_prepare($conn, "
                        DELETE FROM gruppi_articoli
                        WHERE id_gruppo_articolo = ?
                    ");
                    mysqli_stmt_bind_param($stmt, "i", $groupId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }

            mysqli_commit($conn);
        }catch(Exception $e){
            mysqli_rollback($conn);
            header("Location: convalida.php");
            exit;
        }

        delete_upload_if_unreferenced($conn, $bannerPath, UPLOAD_BANNER, "articoli", "banner");

        foreach($filePaths as $filePath){
            delete_upload_if_unreferenced($conn, $filePath, UPLOAD_FILE, "file_articoli", "file_path");
        }
    }

    header("Location: convalida.php");
    exit;
?>