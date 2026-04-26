<?php
    session_start();

    require_once "../config/app.php";
    require_once "../config/config.php";

    $_SESSION["access_mode"] = "signup";

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if($username === "" || $password === "" || $confirmPassword === ""){
        $_SESSION["access_error"] = "Compila tutti i campi obbligatori.";
        header("Location: access.php");
        exit;
    }

    if(strlen($username) <= 3) {
        $_SESSION["access_error"] = "Il nome utente deve avere più di 3 caratteri.";
        header("Location: access.php");
        exit;
    }

    if($password !== $confirmPassword) {
        $_SESSION["access_error"] = "Le password non coincidono.";
        header("Location: access.php");
        exit;
    }

    // Controllo se nome utente già esistente
    $query = "
        SELECT id_utente
        FROM utenti
        WHERE nome_utente = ?
    ";

    $stmt = mysqli_prepare($conn, $query);

    if(!$stmt){
        $_SESSION["access_error"] = "Errore nella preparazione della query.";
        header("Location: access.php");
        exit;
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if(mysqli_stmt_num_rows($stmt) > 0){
        mysqli_stmt_close($stmt);

        $_SESSION["access_error"] = "Nome utente già esistente.";
        header("Location: access.php");
        exit;
    }

    mysqli_stmt_close($stmt);

    // Upload foto profilo opzionale
    $pfpPath = null;
    if(
        isset($_FILES["profile_photo"]) &&
        $_FILES["profile_photo"]["error"] !== UPLOAD_ERR_NO_FILE
    ){
        if($_FILES["profile_photo"]["error"] !== UPLOAD_ERR_OK){
            $_SESSION["access_error"] = "Errore durante il caricamento della foto profilo.";
            header("Location: access.php");
            exit;
        }

        if($_FILES["profile_photo"]["size"] > MAX_PFP_SIZE){
            $_SESSION["access_error"] = "La foto profilo supera la dimensione massima (2 MB).";
            header("Location: access.php");
            exit;
        }

        $tmpName = $_FILES["profile_photo"]["tmp_name"];
        $mimeType = mime_content_type($tmpName);

        if(!in_array($mimeType, ALLOWED_PFP_MIME, true)){
            $_SESSION["access_error"] = "Formato immagine non valido. Usa JPG, PNG o WEBP.";
            header("Location: access.php");
            exit;
        }

        $extensions = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp"
        ];

        $uploadDir = UPLOAD_PFP_PATH;

        if(!is_dir($uploadDir)){
            if(!mkdir($uploadDir, 0777, true)){
                $_SESSION["access_error"] = "Impossibile inizializzare la directory.";
                header("Location: access.php");
                exit;
            }
        }

        $fileName = uniqid("pfp_", true).".".$extensions[$mimeType];
        $destination = $uploadDir.$fileName;

        if(!move_uploaded_file($tmpName, $destination)){
            $_SESSION["access_error"] = "Impossibile salvare la foto profilo.";
            echo $destination;
            header("Location: access.php");
            exit;
        }

        // Percorso da salvare nel DB
        $pfpPath = UPLOAD_PFP . $fileName;
    }

    // Inserimento nel DB
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    $query = "
        INSERT INTO utenti(nome_utente, password_hash, pfp)
        VALUES (?, ?, ?)
    ";

    $stmt = mysqli_prepare($conn, $query);

    if(!$stmt){
        $_SESSION["access_error"] = "Errore nella preparazione dell'inserimento.";
        header("Location: access.php");
        exit;
    }

    mysqli_stmt_bind_param($stmt, "sss", $username, $passwordHash, $pfpPath);

    if(!mysqli_stmt_execute($stmt)){
        mysqli_stmt_close($stmt);

        $_SESSION["access_error"] = "Errore durante la registrazione.";
        header("Location: access.php");
        exit;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    $_SESSION["access_mode"] = "login";
    $_SESSION["access_success"] = "Registrazione completata. Ora puoi accedere.";

    header("Location: access.php?mode=login");
    exit;
?>