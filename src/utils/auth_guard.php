<?php
    function check_session_timeout($timeout_seconds = 1800){
        $lastActivity = $_SESSION["last_activity"] ?? null;

        if($lastActivity !== null && time() - (int)$lastActivity > $timeout_seconds){
            session_unset();
            session_destroy();

            header("Location: ../../auth/access.php?mode=login&timeout=1");
            exit;
        }

        $_SESSION["last_activity"] = time();
    }

    function require_login(){
        if(!isset($_SESSION["user_id"])){
            header("Location: ../../../index.php");
            exit;
        }

        check_session_timeout();
    }

    function require_password_change_if_needed($profilePath){
        $currentPage = basename($_SERVER["PHP_SELF"]);

        $allowedPages = [
            "profile.php",
            "update_profile.php",
            "logout.php"
        ];

        if(!empty($_SESSION["must_change_password"]) && !in_array($currentPage, $allowedPages)){
            header("Location: " . $profilePath . "?force_password=1");
            exit;
        }
    }

    function require_admin(){
        if(empty($_SESSION["is_admin"])){
            http_response_code(403);
            die("Accesso negato.");
        }
    }
?>