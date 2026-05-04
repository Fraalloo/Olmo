**Prototipo – Sotto l’Olmo**

Gruppo:

Gallo, Scaramuzzi G, Turano e Gagliardi

Schema CRUD: draw.io

Design: Figma

Schema CRUD:

<img src="media/Prototipo - Sotto l&#39;Olmo/media/image1.png" style="width:6.13082in;height:6.18656in" />

<img src="media/Prototipo - Sotto l&#39;Olmo/media/image2.png" style="width:6.57102in;height:4.2441in" />

La prima pagina che vedrà l’utente sarà la landing page (index.php) da cui potrà accedere, registrarsi o andare direttamente alla Home se risulterà già registrato.

Per quanto riguarda gli utenti, si farà distinzione tra utenti normali e admin.

L’utente normale potrà creare e modificare articoli, ma spetterà ad un admin convalidare le sue azioni.

Un admin può: creare, modificare, eliminare e convalidare.

<img src="media/Prototipo - Sotto l&#39;Olmo/media/image3.png" style="width:6.69306in;height:4.32292in" />

Dalla Home page (home.php) si potrà vedere una mappa (Leaflet.js e OpenMap API) con cui trovare luoghi storici da scoprire.

Oltre alla mappa, ci sarà un elenco completo con i luoghi, oltre a documenti e testimonianze.

Tramite l’utilizzo di altre API esterne (es. OpenMeteo, wikiapi, etc.) si scopriranno informazioni aggiuntive sul luogo.

Oppure, con l’API Nominatim, tramite il nome del luogo (da mettere in un’apposita barra di ricerca, NominatimSearch) si troveranno in automatico le coordinate da mostrare sulla mappa.

Il layout presentato è quello dell’utente normale, manca il tasto per la convalidazione che vedranno solo gli admin.

<img src="media/Prototipo - Sotto l&#39;Olmo/media/image4.png" style="width:6.69306in;height:4.27778in" />

Dalla landing page (index.php) si puó cliccare Accedi per fare il login, che porterá alla pagina degli accessi (access.php). Se non ci si è registrati, cliccando Registrati si potrá passare alla registrazione.

Il layout della registrazione è analogo.

<img src="media/Prototipo - Sotto l&#39;Olmo/media/image5.png" style="width:6.69306in;height:4.32292in" />

Dalla pagina dell’articolo (article.php) si possono vedere tutte le informazioni postate riguardanti quell’articolo (testo, coordinate, meteo, etc.) Per gli utenti sarà possibile proporre modifiche e gli admin potranno eliminarlo.

Il layout presentato è quello dell’utente normale, manca il tasto per l’elimina che vedranno solo gli admin.

<img src="media/Prototipo - Sotto l&#39;Olmo/media/image6.png" style="width:6.69306in;height:4.32292in" />

Gli admin possono accedere alla pagina della convalidazione (convalida.php) per visualizzare tutti gli articoli postati dagli utenti in attesa di approvazione del contenuto. L’admin dall’elenco potrà: approfondire l’articolo (icona tre puntini), bocciarlo (icona x) o approvarlo (icona OK). Gli articoli in attesa non verranno mostrati in Home, mentre quelli bocciati verranno rimossi dal DB.
