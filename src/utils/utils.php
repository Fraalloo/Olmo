<?php
    function esc($value){
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    function base_path_from_current(){
        $scriptPath = $_SERVER["SCRIPT_FILENAME"] ?? __FILE__;
        $currentDir = realpath(dirname($scriptPath)) ?: dirname($scriptPath);
        $root = realpath(PROJECT_ROOT) ?: PROJECT_ROOT;

        $currentDir = str_replace("\\", "/", $currentDir);
        $root = rtrim(str_replace("\\", "/", $root), "/");

        if(strpos($currentDir, $root) !== 0){
            return ".";
        }

        $relative = trim(str_replace($root, "", $currentDir), "/");

        if($relative === ""){
            return ".";
        }

        $depth = substr_count($relative, "/") + 1;

        return rtrim(str_repeat("../", $depth), "/");
    }

    function other_page_url($page){
        return base_path_from_current()."/src/pages/others/".$page.".php";
    }
?>