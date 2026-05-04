**DOCUMENTAZIONE DEI FILE DI CONFIGURAZIONE**

Il sistema di configurazione del progetto Sotto l’Olmo ha lo scopo di centralizzare i parametri fondamentali dell’applicazione, così da evitare valori scritti direttamente nei file operativi, ridondanze e rendere più semplice la manutenzione del software.

**app.php**

Il file app.php contiene le costanti applicative condivise, cioè i valori che definiscono il comportamento generale del sistema.

Tra questi rientrano il nome dell’applicazione, il percorso dell’immagine profilo predefinita, la dimensione massima consentita per il caricamento della foto profilo e l’elenco dei formati MIME ammessi.

Questo file viene usato soprattutto dal sistema di accessi e dalla gestione delle immagini, in modo da garantire coerenza tra interfaccia, logica applicativa e controlli lato server.

**config.php**

Il file config.php ha il compito di caricare la configurazione adatta all’ambiente corrente. In particolare, il progetto prevede un meccanismo per importare config.prod.php se presente, altrimenti config.test.php.

In questo modo è possibile distinguere un ambiente di produzione da uno di test senza modificare il codice delle pagine principali.

Questa scelta rende il progetto più ordinato e permette di cambiare credenziali o impostazioni tecniche senza intervenire direttamente sulla logica del sito.

**config.\*.php**

Il file config.prod.php contiene i parametri da usare in ambiente reale, mentre config.test.php contiene quelli destinati allo sviluppo locale o alle prove.

In entrambi i casi, i file possono includere i dati di connessione al DBMS, come host, nome del database, utente e password, oltre ad altre impostazioni specifiche dell’installazione.

Il loro impiego separato consente di lavorare in sicurezza, evitando di mescolare configurazioni di sviluppo e configurazioni operative.
