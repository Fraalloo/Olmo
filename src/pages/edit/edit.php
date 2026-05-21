<?php
    session_start();

    require_once "../../config/config.php";
    require_once "../../config/app.php";
    require_once "../../utils/auth_guard.php";

    require_login();
    require_password_change_if_needed("../profile/profile.php");

    const AFTER_EDIT = "../home/home.php";

    $createdFiles = [];

    function redirect_edit_error($articleId, $message){
        $_SESSION["edit_error"] = $message;
        header("Location: edit_page.php?id=" . (int)$articleId);
        exit;
    }

    function clean_created_files($files){
        foreach($files as $file){
            if(is_file($file)){
                unlink($file);
            }
        }
    }

    function ensure_upload_dir($dir){
        if(is_dir($dir)){
            return is_writable($dir);
        }

        return mkdir($dir, 0755, true) && is_writable($dir);
    }

    function get_file_extension($mimeType, $originalName){
        $extensions = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp",
            "application/pdf" => "pdf",
            "text/plain" => "txt"
        ];

        if(isset($extensions[$mimeType])){
            return $extensions[$mimeType];
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $extension = preg_replace("/[^a-z0-9]/", "", $extension);

        return $extension !== "" ? $extension : null;
    }

    function save_uploaded_file(
        $file,
        $prefix,
        $uploadPath,
        $dbPath,
        $maxSize,
        $allowedMime,
        &$createdFiles
    ){
        if(!isset($file["error"]) || $file["error"] === UPLOAD_ERR_NO_FILE){
            return null;
        }

        if($file["error"] !== UPLOAD_ERR_OK){
            throw new Exception("Errore durante il caricamento del file.");
        }

        if($file["size"] > $maxSize){
            throw new Exception("Uno dei file supera la dimensione massima consentita.");
        }

        $tmpName = $file["tmp_name"];
        $originalName = $file["name"] ?? "file";

        if(!is_uploaded_file($tmpName)){
            throw new Exception("File caricato non valido.");
        }

        $mimeType = mime_content_type($tmpName);

        if($mimeType === false){
            throw new Exception("Impossibile verificare il formato del file.");
        }

        if(!in_array($mimeType, $allowedMime, true)){
            throw new Exception("Formato file non consentito.");
        }

        if(!ensure_upload_dir($uploadPath)){
            throw new Exception("La directory di upload non è scrivibile dal server.");
        }

        $extension = get_file_extension($mimeType, $originalName);

        if(!$extension){
            throw new Exception("Estensione del file non valida.");
        }

        $fileName = uniqid($prefix . "_", true) . "." . $extension;
        $destination = $uploadPath . $fileName;

        if(!move_uploaded_file($tmpName, $destination)){
            throw new Exception("Impossibile salvare il file caricato.");
        }

        $createdFiles[] = $destination;

        return [
            "original_name" => $originalName,
            "mime_type" => $mimeType,
            "db_path" => $dbPath . $fileName
        ];
    }

    function normalize_uploaded_files($files){
        $normalized = [];

        if(!isset($files["name"]) || !is_array($files["name"])){
            return $normalized;
        }

        foreach($files["name"] as $index => $name){
            $normalized[] = [
                "name" => $files["name"][$index] ?? "",
                "type" => $files["type"][$index] ?? "",
                "tmp_name" => $files["tmp_name"][$index] ?? "",
                "error" => $files["error"][$index] ?? UPLOAD_ERR_NO_FILE,
                "size" => $files["size"][$index] ?? 0
            ];
        }

        return $normalized;
    }

    if($_SERVER["REQUEST_METHOD"] !== "POST"){
        header("Location: ../home/home.php");
        exit;
    }

    $sourceArticleId = (int)($_POST["id_articolo"] ?? 0);

    if($sourceArticleId <= 0){
        header("Location: ../home/home.php");
        exit;
    }

    $idPubblicatore = $_SESSION["user_id"] ?? null;

    if(!$idPubblicatore){
        redirect_edit_error($sourceArticleId, "Sessione non valida. Effettua nuovamente il login.");
    }

    $titolo = trim($_POST["titolo"] ?? "");
    $descrizione = trim($_POST["descrizione"] ?? "");
    $idTipoArticolo = (int)($_POST["id_tipo_articolo"] ?? 0);
    $latitudineInput = trim($_POST["latitudine"] ?? "");
    $longitudineInput = trim($_POST["longitudine"] ?? "");

    if($titolo === "" || $descrizione === "" || $idTipoArticolo <= 0){
        redirect_edit_error($sourceArticleId, "Compila tutti i campi obbligatori.");
    }

    if(strlen($titolo) > 100){
        redirect_edit_error($sourceArticleId, "Il titolo non può superare i 100 caratteri.");
    }

    $latitudine = null;
    $longitudine = null;

    if($latitudineInput !== "" || $longitudineInput !== ""){
        if($latitudineInput === "" || $longitudineInput === ""){
            redirect_edit_error($sourceArticleId, "Inserisci sia latitudine sia longitudine.");
        }

        if(!is_numeric($latitudineInput) || !is_numeric($longitudineInput)){
            redirect_edit_error($sourceArticleId, "Le coordinate devono essere valori numerici.");
        }

        $latitudine = (float)$latitudineInput;
        $longitudine = (float)$longitudineInput;

        if($latitudine < -90 || $latitudine > 90){
            redirect_edit_error($sourceArticleId, "La latitudine deve essere compresa tra -90 e 90.");
        }

        if($longitudine < -180 || $longitudine > 180){
            redirect_edit_error($sourceArticleId, "La longitudine deve essere compresa tra -180 e 180.");
        }
    }

    $latitudineDb = $latitudine !== null
        ? number_format($latitudine, 6, ".", "")
        : null;
    $longitudineDb = $longitudine !== null
        ? number_format($longitudine, 6, ".", "")
        : null;

    $links = array_merge($_POST["existing_links"] ?? [], $_POST["links"] ?? []);
    $links = array_map("trim", $links);
    $links = array_filter($links, fn($link) => $link !== "");
    $links = array_values(array_unique($links));

    foreach($links as $link){
        if(strlen($link) > 255){
            redirect_edit_error($sourceArticleId, "Uno dei link supera i 255 caratteri.");
        }

        if(!filter_var($link, FILTER_VALIDATE_URL)){
            redirect_edit_error($sourceArticleId, "Uno dei link inseriti non è valido.");
        }
    }

    $existingFileIds = $_POST["existing_files"] ?? [];
    $existingFileIds = array_values(array_unique(array_map("intval", $existingFileIds)));
    $existingFileIds = array_filter($existingFileIds, fn($fileId) => $fileId > 0);

    if(!mysqli_begin_transaction($conn)){
        redirect_edit_error($sourceArticleId, "Impossibile inizializzare la transazione.");
    }

    try{
        $query = "
            SELECT
                id_articolo,
                id_gruppo_articolo,
                banner
            FROM articoli
            WHERE
                id_articolo = ? AND
                id_admin IS NOT NULL AND
                is_active = 1 AND
                is_hidden = 0
        ";
        $stmt = mysqli_prepare($conn, $query);

        if(!$stmt){
            throw new Exception("Errore nella preparazione della query sull'articolo.");
        }

        mysqli_stmt_bind_param($stmt, "i", $sourceArticleId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $sourceArticle = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if(!$sourceArticle){
            throw new Exception("Articolo non modificabile.");
        }

        $query = "
            SELECT id_tipo_articolo
            FROM tipi_articoli
            WHERE id_tipo_articolo = ?
        ";
        $stmt = mysqli_prepare($conn, $query);

        if(!$stmt){
            throw new Exception("Errore nella preparazione della query sul tipo articolo.");
        }

        mysqli_stmt_bind_param($stmt, "i", $idTipoArticolo);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if(mysqli_stmt_num_rows($stmt) === 0){
            mysqli_stmt_close($stmt);
            throw new Exception("Tipo articolo non valido.");
        }

        mysqli_stmt_close($stmt);

        $bannerPath = null;

        if(isset($_FILES["banner"]) && $_FILES["banner"]["error"] !== UPLOAD_ERR_NO_FILE){
            $banner = save_uploaded_file(
                $_FILES["banner"],
                "banner",
                UPLOAD_BANNER_PATH,
                UPLOAD_BANNER,
                MAX_PFP_SIZE,
                ALLOWED_PFP_MIME,
                $createdFiles
            );

            $bannerPath = $banner["db_path"];
        } elseif(isset($_POST["keep_banner"]) && $_POST["keep_banner"] === "1"){
            $bannerPath = $sourceArticle["banner"];
        }

        $idGruppoArticolo = (int)$sourceArticle["id_gruppo_articolo"];

        $query = "
            SELECT COALESCE(MAX(versione), 0) + 1 AS next_version
            FROM articoli
            WHERE id_gruppo_articolo = ?
        ";
        $stmt = mysqli_prepare($conn, $query);

        if(!$stmt){
            throw new Exception("Errore nella preparazione della query sulla versione.");
        }

        mysqli_stmt_bind_param($stmt, "i", $idGruppoArticolo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $versionRow = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $nextVersion = (int)$versionRow["next_version"];

        $query = "
            INSERT INTO articoli(
                id_gruppo_articolo,
                id_tipo_articolo,
                id_pubblicatore,
                banner,
                titolo,
                descrizione,
                latitudine,
                longitudine,
                versione
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = mysqli_prepare($conn, $query);

        if(!$stmt){
            throw new Exception("Errore nella preparazione dell'inserimento della modifica.");
        }

        mysqli_stmt_bind_param(
            $stmt,
            "iiisssssi",
            $idGruppoArticolo,
            $idTipoArticolo,
            $idPubblicatore,
            $bannerPath,
            $titolo,
            $descrizione,
            $latitudineDb,
            $longitudineDb,
            $nextVersion
        );

        if(!mysqli_stmt_execute($stmt)){
            mysqli_stmt_close($stmt);
            throw new Exception("Errore durante il salvataggio della modifica.");
        }

        mysqli_stmt_close($stmt);

        $newArticleId = mysqli_insert_id($conn);

        if(!empty($existingFileIds)){
            $placeholders = implode(",", array_fill(0, count($existingFileIds), "?"));
            $types = "i" . str_repeat("i", count($existingFileIds));
            $params = array_merge([$sourceArticleId], $existingFileIds);

            $query = "
                SELECT nome_originale, file_path, mime_type
                FROM file_articoli
                WHERE id_articolo = ? AND id_file IN ($placeholders)
            ";
            $stmt = mysqli_prepare($conn, $query);

            if(!$stmt){
                throw new Exception("Errore nella preparazione della query sui file esistenti.");
            }

            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while($file = mysqli_fetch_assoc($result)){
                $queryInsert = "
                    INSERT INTO file_articoli(
                        id_articolo,
                        nome_originale,
                        file_path,
                        mime_type
                    )
                    VALUES (?, ?, ?, ?)
                ";
                $insertStmt = mysqli_prepare($conn, $queryInsert);

                if(!$insertStmt){
                    mysqli_stmt_close($stmt);
                    throw new Exception("Errore nella preparazione della copia dei file.");
                }

                mysqli_stmt_bind_param(
                    $insertStmt,
                    "isss",
                    $newArticleId,
                    $file["nome_originale"],
                    $file["file_path"],
                    $file["mime_type"]
                );

                if(!mysqli_stmt_execute($insertStmt)){
                    mysqli_stmt_close($insertStmt);
                    mysqli_stmt_close($stmt);
                    throw new Exception("Errore durante la copia dei file esistenti.");
                }

                mysqli_stmt_close($insertStmt);
            }

            mysqli_stmt_close($stmt);
        }

        if(isset($_FILES["allegati"])){
            $attachments = normalize_uploaded_files($_FILES["allegati"]);

            foreach($attachments as $attachment){
                if($attachment["error"] === UPLOAD_ERR_NO_FILE){
                    continue;
                }

                $savedFile = save_uploaded_file(
                    $attachment,
                    "file",
                    UPLOAD_FILE_PATH,
                    UPLOAD_FILE,
                    MAX_ARTICLE_FILE_SIZE,
                    ALLOWED_ARTICLE_FILE_MIME,
                    $createdFiles
                );

                if(!$savedFile){
                    continue;
                }

                $query = "
                    INSERT INTO file_articoli(
                        id_articolo,
                        nome_originale,
                        file_path,
                        mime_type
                    )
                    VALUES (?, ?, ?, ?)
                ";
                $stmt = mysqli_prepare($conn, $query);

                if(!$stmt){
                    throw new Exception("Errore nella preparazione dell'inserimento dei nuovi allegati.");
                }

                mysqli_stmt_bind_param(
                    $stmt,
                    "isss",
                    $newArticleId,
                    $savedFile["original_name"],
                    $savedFile["db_path"],
                    $savedFile["mime_type"]
                );

                if(!mysqli_stmt_execute($stmt)){
                    mysqli_stmt_close($stmt);
                    throw new Exception("Errore durante il salvataggio dei nuovi allegati.");
                }

                mysqli_stmt_close($stmt);
            }
        }

        foreach($links as $link){
            $query = "
                INSERT INTO link_articoli(
                    id_articolo,
                    url_link
                )
                VALUES (?, ?)
            ";
            $stmt = mysqli_prepare($conn, $query);

            if(!$stmt){
                throw new Exception("Errore nella preparazione dell'inserimento dei link.");
            }

            mysqli_stmt_bind_param($stmt, "is", $newArticleId, $link);

            if(!mysqli_stmt_execute($stmt)){
                mysqli_stmt_close($stmt);
                throw new Exception("Errore durante il salvataggio dei link.");
            }

            mysqli_stmt_close($stmt);
        }

        if(!mysqli_commit($conn)){
            throw new Exception("Errore durante il completamento della modifica.");
        }

        header("Location: " . AFTER_EDIT);
        exit;
    }catch(Exception $e){
        mysqli_rollback($conn);
        clean_created_files($createdFiles);

        $_SESSION["edit_error"] = $e->getMessage();

        header("Location: edit_page.php?id=" . $sourceArticleId);
        exit;
    }
?>