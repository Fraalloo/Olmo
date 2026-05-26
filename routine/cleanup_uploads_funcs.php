<?php
    function normalize_upload_path($path){
        $path = trim((string)$path);
        $path = str_replace("\\", "/", $path);
        $path = ltrim($path, "/");

        if($path === "" || strpos($path, "../") !== false){
            return null;
        }

        return $path;
    }

    function count_upload_references($conn, $table, $column, $path){
        $allowedTargets = [
            "articoli.banner" => true,
            "file_articoli.file_path" => true
        ];
        $target = $table.".".$column;

        if(!isset($allowedTargets[$target])){
            throw new Exception("Riferimento upload non consentito.");
        }

        $stmt = mysqli_prepare($conn, "
            SELECT COUNT(*) AS totale
            FROM $table
            WHERE $column = ?
        ");
        mysqli_stmt_bind_param($stmt, "s", $path);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return (int)$row["totale"];
    }

    function delete_upload_if_unreferenced($conn, $path, $allowedPrefix, $table, $column){
        $path = normalize_upload_path($path);

        if($path === null || strpos($path, $allowedPrefix) !== 0){
            return false;
        }

        if(count_upload_references($conn, $table, $column, $path) > 0){
            return false;
        }

        $absolutePath = PROJECT_ROOT.$path;

        return is_file($absolutePath) && unlink($absolutePath);
    }

    function collect_referenced_uploads($conn){
        $referenced = [];
        $queries = [
            "SELECT pfp AS upload_path FROM utenti WHERE pfp IS NOT NULL AND pfp <> ''",
            "SELECT banner AS upload_path FROM articoli WHERE banner IS NOT NULL AND banner <> ''",
            "SELECT file_path AS upload_path FROM file_articoli WHERE file_path IS NOT NULL AND file_path <> ''"
        ];

        foreach($queries as $query){
            $result = mysqli_query($conn, $query);

            if(!$result){
                throw new Exception("Errore durante la lettura dei riferimenti dal database.");
            }

            while($row = mysqli_fetch_assoc($result)){
                $path = normalize_upload_path($row["upload_path"] ?? "");

                if($path !== null){
                    $referenced[$path] = true;
                }
            }

            mysqli_free_result($result);
        }

        return $referenced;
    }

    function collect_physical_uploads(){
        $uploadDirs = [
            UPLOAD_PFP,
            UPLOAD_BANNER,
            UPLOAD_FILE
        ];
        $files = [];

        foreach($uploadDirs as $relativeDir){
            $absoluteDir = PROJECT_ROOT . $relativeDir;

            if(!is_dir($absoluteDir)){
                continue;
            }

            $dirFiles = scandir($absoluteDir);

            if($dirFiles === false){
                continue;
            }

            foreach($dirFiles as $fileName){
                if($fileName === "." || $fileName === ".." || strpos($fileName, ".") === 0){
                    continue;
                }

                $absolutePath = $absoluteDir.$fileName;

                if(!is_file($absolutePath)){
                    continue;
                }

                $relativePath = normalize_upload_path($relativeDir.$fileName);

                if($relativePath !== null){
                    $files[$relativePath] = $absolutePath;
                }
            }
        }

        return $files;
    }

    function find_orphan_uploads($conn){
        $referenced = collect_referenced_uploads($conn);
        $physical = collect_physical_uploads();
        $orphans = [];

        foreach($physical as $relativePath => $absolutePath){
            if(!isset($referenced[$relativePath])){
                $orphans[$relativePath] = $absolutePath;
            }
        }

        return [
            "orphans" => $orphans,
            "total_physical" => count($physical),
            "total_referenced" => count($referenced)
        ];
    }

    function print_cleanup_report($report, $deleted = [], $failed = []){
        echo "Upload fisici trovati: ".$report["total_physical"]."\n";
        echo "Path referenziati dal DB: ".$report["total_referenced"]."\n";
        echo "File orfani trovati: ".count($report["orphans"])."\n";

        if(!empty($report["orphans"])){
            echo "\nFile orfani:\n";

            foreach(array_keys($report["orphans"]) as $relativePath){
                echo "- ".$relativePath."\n";
            }
        }

        if(!empty($deleted)){
            echo "\n"."File eliminati:"."\n";

            foreach($deleted as $relativePath){
                echo "- ".$relativePath."\n";
            }
        }

        if(!empty($failed)){
            echo "\n"."File non eliminati:"."\n";

            foreach($failed as $relativePath){
                echo "- ".$relativePath."\n";
            }
        }
    }

    function delete_orphan_uploads($orphans){
        $deleted = [];
        $failed = [];

        foreach($orphans as $relativePath => $absolutePath){
            if(is_file($absolutePath) && unlink($absolutePath)){
                $deleted[] = $relativePath;
            }else{
                $failed[] = $relativePath;
            }
        }

        return [
            "deleted" => $deleted,
            "failed" => $failed
        ];
    }
?>