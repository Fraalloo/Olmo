<?php
    function recalculate_active_version($conn, $groupId){
        $groupId = (int)$groupId;

        $stmt = mysqli_prepare($conn, "
            SELECT id_articolo
            FROM articoli
            WHERE id_gruppo_articolo = ?
              AND id_admin IS NOT NULL
              AND is_hidden = 0
            ORDER BY versione DESC
            LIMIT 1
        ");

        mysqli_stmt_bind_param($stmt, "i", $groupId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $activeArticle = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($conn, "
            UPDATE articoli
            SET is_active = 0
            WHERE id_gruppo_articolo = ?
        ");

        mysqli_stmt_bind_param($stmt, "i", $groupId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if($activeArticle){
            $activeArticleId = (int)$activeArticle["id_articolo"];

            $stmt = mysqli_prepare($conn, "
                UPDATE articoli
                SET is_active = 1
                WHERE id_articolo = ?
            ");

            mysqli_stmt_bind_param($stmt, "i", $activeArticleId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            return $activeArticleId;
        }

        return null;
    }
?>