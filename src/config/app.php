<?php
    const APP_NAME = "Sotto l'Olmo";
    const CURR_VERS = "v0.2.1";
    const DEBUG = true;

    const PROJECT_ROOT = __DIR__."/../../";

    const DEFAULT_PFP = "src/assets/pfp.png";
    const DEFAULT_PFP_PATH = PROJECT_ROOT.DEFAULT_PFP;

    const UPLOAD_PFP = "uploads/pfp/";  
    const UPLOAD_PFP_PATH = PROJECT_ROOT.UPLOAD_PFP;

    const UPLOAD_BANNER = "uploads/banner/";  
    const UPLOAD_BANNER_PATH = PROJECT_ROOT.UPLOAD_BANNER;

    const UPLOAD_FILE = "uploads/file/";
    const UPLOAD_FILE_PATH = PROJECT_ROOT.UPLOAD_FILE;

    const MAX_PFP_SIZE = 2 * 1024 * 1024; // 2 MB
    const MAX_ARTICLE_FILE_SIZE = 10 * 1024 * 1024; // 10 MB

    const ALLOWED_PFP_MIME = ["image/jpeg", "image/png", "image/webp"];
    const ALLOWED_ARTICLE_FILE_MIME = [
        "application/pdf",
        "text/plain",
        "image/jpeg",
        "image/png",
        "image/webp"
    ];

    if(DEBUG){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
?>