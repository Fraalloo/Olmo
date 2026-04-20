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
                $conn_i = mysqli_connect($host, $user, $password);

                if(!file_exists(DBSCHEMA)){
                    die("File SQL non trovato: ".$DBSCHEMA);
                }

                $sql = file_get_contents(DBSCHEMA);
                if(!$sql){
                    die("Impossibile leggere il file SQL.");
                }

                if(mysqli_multi_query($conn_i, $sql)){
                    do{
                        if($res = mysqli_store_result($conn_i)) mysqli_free_result($res);
                    }while(mysqli_more_results($conn_i) && mysqli_next_result($conn_i));

                    echo "Importazione completata con successo.";
                    echo "<br><a href=\"../index.php\" class=\"cta\">Torna alla home</a>";

                }else echo "Errore durante l'importazione: " . mysqli_error($conn_i);
            ?>
        </div>
    </body>
</html>
