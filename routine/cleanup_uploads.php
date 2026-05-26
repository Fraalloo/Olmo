<?php
    if(PHP_SAPI !== "cli"){
        http_response_code(403);
        exit("Routine eseguibile solo da CLI.");
    }

    function print_usage(){
        echo "Uso:\n";
        echo "  php routine/cleanup_uploads.php [--dry|--delete]\n\n";
        echo "Opzioni:\n";
        echo "  --dry     Mostra i file orfani senza eliminarli. Modalita predefinita.\n";
        echo "  --delete  Elimina i file orfani trovati.\n";
        echo "  --help    Mostra questo messaggio.\n";
    }

    $args = array_slice($argv, 1);
    $mode = "dry";
    $modeSet = false;

    foreach($args as $arg){
        if($arg === "--help"){
            print_usage();
            exit;
        }

        if($arg === "--dry"){
            if($modeSet && $mode !== "dry"){
                print_usage();
                exit(1);
            }

            $mode = "dry";
            $modeSet = true;
        }elseif($arg === "--delete"){
            if($modeSet && $mode !== "delete"){
                print_usage();
                exit(1);
            }

            $mode = "delete";
            $modeSet = true;
        }else{
            print_usage();
            exit(1);
        }
    }

    require_once __DIR__."/cleanup_uploads_funcs.php";

    // Non si importa direttamente config.php perché da CLI può dare problemi
    $appConfig = __DIR__."/../src/config/app.php";
    $dbConfig = file_exists(__DIR__."/../src/config/config.prod.php")
        ? __DIR__."/../src/config/config.prod.php"
        : __DIR__."/../src/config/config.test.php";

    try{
        if(!is_file($appConfig)){
            throw new Exception("File non trovato: ".$appConfig);
        }

        if(!is_file($dbConfig)){
            throw new Exception("File non trovato: ".$dbConfig);
        }

        require_once $appConfig;
        require_once $dbConfig;

        mysqli_report(MYSQLI_REPORT_OFF);
        $conn = @mysqli_connect($host, $user, $password, $dbname);

        if(!$conn && $host === "localhost"){
            $conn = @mysqli_connect("127.0.0.1", $user, $password, $dbname);
        }

        if(!$conn){
            throw new Exception(mysqli_connect_error() ?: "Errore connessione database.");
        }

        $report = find_orphan_uploads($conn);

        if($mode === "delete"){
            $result = delete_orphan_uploads($report["orphans"]);

            echo "DELETE - eliminazione file orfani completata.\n\n";
            print_cleanup_report($report, $result["deleted"], $result["failed"]);

            if(!empty($result["failed"])){
                exit(1);
            }

            exit;
        }

        echo "DRY RUN - nessun file verra eliminato.\n\n";
        print_cleanup_report($report);
    }catch(Exception $e){
        fwrite(STDERR, "Errore: ".$e->getMessage()."\n");
        exit(1);
    }
?>