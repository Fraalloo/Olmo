<?php
    session_start();

    require_once "../config/app.php";
    require_once "../config/config.php";

    function redirect_access_error($message, $createdFile = null){
        if($createdFile !== null && is_file($createdFile)){
            unlink($createdFile);
        }

        $_SESSION["access_error"] = $message;
        header("Location: access.php");
        exit;
    }

    $_SESSION["access_mode"] = "signup";

    if($_SERVER["REQUEST_METHOD"] !== "POST"){
        header("Location: access.php?mode=signup");
        exit;
    }

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if($username === "" || $password === "" || $confirmPassword === ""){
        redirect_access_error("Compila tutti i campi obbligatori.");
    }

    if(strlen($username) <= 3) {
        redirect_access_error("Il nome utente deve avere più di 3 caratteri.");
    }

    if($password !== $confirmPassword) {
        redirect_access_error("Le password non coincidono.");
    }

    // Controllo se nome utente già esistente
    $query = "
        SELECT id_utente
        FROM utenti
        WHERE nome_utente = ?
    ";

    $stmt = mysqli_prepare($conn, $query);

    if(!$stmt){
        redirect_access_error("Errore nella preparazione della query.");
    }

    mysqli_stmt_bind_param($stmt, "s", $username);

    if(!mysqli_stmt_execute($stmt)){
        mysqli_stmt_close($stmt);
        redirect_access_error("Errore durante la verifica del nome utente.");
    }

    mysqli_stmt_store_result($stmt);

    if(mysqli_stmt_num_rows($stmt) > 0){
        mysqli_stmt_close($stmt);

        redirect_access_error("Nome utente già esistente.");
    }

    mysqli_stmt_close($stmt);

    // Upload foto profilo opzionale
    $pfpPath = null;
    $uploadedPfpFile = null;

    if(
        isset($_FILES["profile_photo"]) &&
        $_FILES["profile_photo"]["error"] !== UPLOAD_ERR_NO_FILE
    ){
        if($_FILES["profile_photo"]["error"] !== UPLOAD_ERR_OK){
            redirect_access_error("Errore durante il caricamento della foto profilo.");
        }

        if($_FILES["profile_photo"]["size"] > MAX_PFP_SIZE){
            redirect_access_error("La foto profilo supera la dimensione massima (2 MB).");
        }

        $tmpName = $_FILES["profile_photo"]["tmp_name"];

        if(!is_uploaded_file($tmpName)){
            redirect_access_error("File caricato non valido.");
        }

        $mimeType = mime_content_type($tmpName);

        if($mimeType === false){
            redirect_access_error("Impossibile verificare il formato della foto profilo.");
        }

        if(!in_array($mimeType, ALLOWED_PFP_MIME, true)){
            redirect_access_error("Formato immagine non valido. Usa JPG, PNG o WEBP.");
        }

        $extensions = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp"
        ];

        $uploadDir = UPLOAD_PFP_PATH;

        if(!is_dir($uploadDir)){
            if(!mkdir($uploadDir, 0755, true)){
                redirect_access_error("Impossibile inizializzare la directory.");
            }
        }

        $fileName = uniqid("pfp_", true).".".$extensions[$mimeType];
        $destination = $uploadDir.$fileName;

        if(!move_uploaded_file($tmpName, $destination)){
            redirect_access_error("Impossibile salvare la foto profilo.");
        }

        $uploadedPfpFile = $destination;

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
        redirect_access_error("Errore nella preparazione dell'inserimento.", $uploadedPfpFile);
    }

    mysqli_stmt_bind_param($stmt, "sss", $username, $passwordHash, $pfpPath);

    if(!mysqli_stmt_execute($stmt)){
        mysqli_stmt_close($stmt);

        redirect_access_error("Errore durante la registrazione.", $uploadedPfpFile);
    }

    mysqli_stmt_close($stmt);

    $_SESSION["access_mode"] = "login";
    $_SESSION["access_success"] = "Registrazione completata. Ora puoi accedere.";

    header("Location: access.php?mode=login");
    exit;
?>