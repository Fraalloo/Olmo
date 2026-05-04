<?php
    require_once "../../config/config.php";
    require_once "../../config/app.php";
    require_once "../../utils/utils.php";
    require_once "../../utils/auth_guard.php";

    session_start();

    require_login();

    const TO_ASSETS = "../../../";

    $userId = (int)$_SESSION["user_id"];
    $mustChangePassword = !empty($_SESSION["must_change_password"]);

    $nomeUtente = trim($_POST["nome_utente"] ?? "");
    $currentPassword = $_POST["current_password"] ?? "";
    $newPassword = $_POST["new_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    $query = "
        SELECT
            nome_utente,
            password_hash,
            pfp,
            must_change_password
        FROM utenti
        WHERE id_utente = ?
    ";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $utente = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if(!$utente){
        header("Location: ../../auth/logout.php");
        exit;
    }

    $base = "Location: modifica_profile.php?";
    if($mustChangePassword && trim($newPassword) === ""){
        header($base . "error=password_required");
        exit;
    }

    $newHash = null;

    if(trim($newPassword) !== ""){
        if(strlen($newPassword) < 8){
            header($base . "error=password_short");
            exit;
        }

        if($newPassword !== $confirmPassword){
            header($base . "error=password_mismatch");
            exit;
        }

        if(password_verify($newPassword, $utente["password_hash"])){
            header($base . "error=same_password");
            exit;
        }

        if(!$mustChangePassword){
            if(trim($currentPassword) === "" || !password_verify($currentPassword, $utente["password_hash"])){
                header($base . "error=wrong_current_password");
                exit;
            }
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    if($mustChangePassword){
        $query = "
            UPDATE utenti
            SET password_hash = ?,
                must_change_password = 0
            WHERE id_utente = ?
        ";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $newHash, $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION["must_change_password"] = false;

        header($base . "success=1");
        exit;
    }

    if(strlen($nomeUtente) <= 3){
        header($base . "error=username_short");
        exit;
    }

    if($nomeUtente !== $utente["nome_utente"]){
        $query = "
            SELECT id_utente
            FROM utenti
            WHERE nome_utente = ?
              AND id_utente <> ?
        ";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $nomeUtente, $userId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $existingUser = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if($existingUser){
            header($base . "error=username_exists");
            exit;
        }
    }

    $pfpPath = $utente["pfp"];

    if(isset($_FILES["pfp"]) && $_FILES["pfp"]["error"] === UPLOAD_ERR_OK){
        $allowedTypes = [
            "image/png" => "png",
            "image/jpeg" => "jpg",
            "image/webp" => "webp"
        ];

        $mime = mime_content_type($_FILES["pfp"]["tmp_name"]);

        if(!isset($allowedTypes[$mime])){
            header($base . "error=pfp_invalid");
            exit;
        }

        $extension = $allowedTypes[$mime];
        $fileName = uniqid("pfp_", true) . "." . $extension;
        $relativePath = "uploads/pfp/" . $fileName;
        $absolutePath = TO_ASSETS . $relativePath;

        if(!move_uploaded_file($_FILES["pfp"]["tmp_name"], $absolutePath)){
            header($base . "error=pfp_invalid");
            exit;
        }

        $pfpPath = $relativePath;
    }

    if($newHash !== null){
        $query = "
            UPDATE utenti
            SET nome_utente = ?,
                pfp = ?,
                password_hash = ?
            WHERE id_utente = ?
        ";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $nomeUtente, $pfpPath, $newHash, $userId);
    } else {
        $query = "
            UPDATE utenti
            SET nome_utente = ?,
                pfp = ?
            WHERE id_utente = ?
        ";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $nomeUtente, $pfpPath, $userId);
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION["username"] = $nomeUtente;
    $_SESSION["pfp"] = !empty($pfpPath) ? $pfpPath : DEFAULT_PFP;

    header("Location: profile.php?success=1");
    exit;
?>