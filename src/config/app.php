<?php
    const APP_NAME = "Sotto l'Olmo";
    const CURR_VERS = "v0.1.1";

    const PROJECT_ROOT = __DIR__."/../../";

    const DEFAULT_PFP = "src/assets/pfp.png";
    const DEFAULT_PFP_PATH = PROJECT_ROOT.DEFAULT_PFP;

    const UPLOAD_PFP = "uploads/pfp/";  
    const UPLOAD_PFP_PATH = PROJECT_ROOT.UPLOAD_PFP;

    const MAX_PFP_SIZE = 2 * 1024 * 1024; // 2 MB
    const ALLOWED_PFP_MIME = ["image/jpeg", "image/png", "image/webp"];

    header("Location: ../../index.php");
?>