<!DOCTYPE html>
<html>
    <head>
        <title>Inizializzazione Web App - completata</title>
    </head>
    <body>
        <div align="center">
            <?php
                if(file_exists("../src/config/config.prod.php"))
                    require_once "../src/config/config.prod.php";
                else
                    require_once "../src/config/config.test.php";

                const DBSCHEMA = "../db/db_schema.sql";
                const TEST_DB_DIR = "../test/db";

                $includeTestRecords = isset($_POST["include_test_records"]) && $_POST["include_test_records"] === "1";

                function run_sql_file($conn, $filePath){
                    if(!file_exists($filePath)){
                        return "File SQL non trovato: " . $filePath;
                    }

                    $sql = file_get_contents($filePath);

                    if($sql === false){
                        return "Impossibile leggere il file SQL: " . $filePath;
                    }

                    if(trim($sql) === ""){
                        return null;
                    }

                    if(!mysqli_multi_query($conn, $sql)){
                        return "Errore durante l'importazione di " .
                            basename($filePath) . ": " . mysqli_error($conn);
                    }

                    do{
                        if($res = mysqli_store_result($conn)){
                            mysqli_free_result($res);
                        }

                        if(mysqli_more_results($conn) && !mysqli_next_result($conn)){
                            return "Errore durante l'importazione di " .
                                basename($filePath) . ": " . mysqli_error($conn);
                        }
                    }while(mysqli_more_results($conn));

                    return null;
                }

                function get_test_sql_files(){
                    $orderedFiles = [
                        "utenti.sql",
                        "gruppi_articoli.sql",
                        "articoli.sql",
                        "file_articoli.sql",
                        "link_articoli.sql"
                    ];

                    $files = [];

                    foreach($orderedFiles as $fileName){
                        $filePath = TEST_DB_DIR . "/" . $fileName;

                        if(file_exists($filePath)){
                            $files[] = $filePath;
                        }
                    }

                    $allFiles = glob(TEST_DB_DIR . "/*.sql") ?: [];

                    foreach($allFiles as $filePath){
                        if(!in_array($filePath, $files, true)){
                            $files[] = $filePath;
                        }
                    }

                    return $files;
                }

                $conn_i = mysqli_connect($host, $user, $password);

                if(!$conn_i){
                    die("Errore connessione database.");
                }

                $importedFiles = [];
                $error = run_sql_file($conn_i, DBSCHEMA);

                if($error === null){
                    $importedFiles[] = DBSCHEMA;

                    if($includeTestRecords){
                        if(!is_dir(TEST_DB_DIR)){
                            $error = "Directory SQL di test non trovata: " . TEST_DB_DIR;
                        }else{
                            foreach(get_test_sql_files() as $filePath){
                                $error = run_sql_file($conn_i, $filePath);

                                if($error !== null){
                                    break;
                                }

                                $importedFiles[] = $filePath;
                            }
                        }
                    }
                }

                if($error === null){
                    echo "Importazione completata con successo.";

                    if($includeTestRecords){
                        echo "<br>Record di test importati correttamente.";
                    }

                    echo "<br><br>File importati:";
                    echo "<ul style=\"display:inline-block;text-align:left;\">";

                    foreach($importedFiles as $filePath){
                        echo "<li>" . htmlspecialchars($filePath) . "</li>";
                    }

                    echo "</ul>";
                    echo "<br><a href=\"../index.php\" class=\"cta\">Torna alla home</a>";
                }else{
                    echo htmlspecialchars($error);
                    echo "<br><a href=\"./index.html\">Torna allo script</a>";
                }

                mysqli_close($conn_i);
            ?>
        </div>
    </body>
</html>