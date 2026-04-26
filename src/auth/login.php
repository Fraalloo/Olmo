<?php
    require_once "../config/config.php";
    require_once "../config/app.php";

    session_start();

    $_SESSION["access_mode"] = "login";

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if($username === "" || $password === ""){
        $_SESSION["access_error"] = "Compila tutti i campi.";
        header("Location: access.php");
        exit;
    }

    $query = "
        SELECT id_utente, nome_utente, password_hash, pfp, is_admin
        FROM utenti
        WHERE nome_utente = ?
    ";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if(mysqli_stmt_num_rows($stmt) !== 1) {
        $_SESSION["access_error"] = "Credenziali non valide.";
        header("Location: access.php");
        exit;
    }

    mysqli_stmt_bind_result($stmt, $idUtente, $nomeUtente, $passwordHash, $pfp, $isAdmin);
    mysqli_stmt_fetch($stmt);

    if(!is_string($passwordHash) || $passwordHash === ''){
        $_SESSION["access_error"] = "Credenziali non valide.";
        header("Location: access.php");
        exit;
    }

    if(!password_verify($password, $passwordHash)){
        $_SESSION["access_error"] = "Credenziali non valide.";
        header("Location: access.php");
        exit;
    }

    $_SESSION["user_id"] = $idUtente;
    $_SESSION["username"] = $nomeUtente;
    $_SESSION["is_admin"] = (bool)$isAdmin;
    $_SESSION["pfp"] = !empty($pfp) ? $pfp : DEFAULT_PFP;

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header("Location: ../pages/home/home.php");
    exit;
?>