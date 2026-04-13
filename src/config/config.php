<?php
    if(file_exists(__DIR__ . "/config.prod.php"))
        require_once __DIR__ . "/config.prod.php";
    else
        require_once __DIR__ . "/config.test.php";
?>