<?php
    function require_login(){
        if(!isset($_SESSION["user_id"])){
            header("Location: ../../../index.php");
            exit;
        }
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