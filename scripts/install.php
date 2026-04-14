<?php
    require_once "../src/config/config.php";

    const DBSCHEMA = "../db/db_schema.sql";

    if(!file_exists(DBSCHEMA)){
        die("File SQL non trovato: ".$DBSCHEMA);
    }

    $sql = file_get_contents(DBSCHEMA);
    if(!$sql){
        die("Impossibile leggere il file SQL.");
    }

    if(mysqli_multi_query($conn, $sql)){
        do{
            if($res = mysqli_store_result($conn)) mysqli_free_result($res);
        }while(mysqli_more_results($conn) && mysqli_next_result($conn));

        echo "Importazione completata con successo.";
    }else echo "Errore durante l'importazione: " . mysqli_error($conn);
?>