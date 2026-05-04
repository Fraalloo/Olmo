**DOCUMENTAZIONE DEI FILE DI AUTENTICAZIONE**

Il sistema di accessi del progetto Sotto l’Olmo gestisce l’ingresso degli utenti nel sito, la registrazione di nuovi account, il mantenimento della sessione e l’uscita dal sistema. Questo modulo costituisce il punto di ingresso all’area riservata e si integra con la struttura del database e con il flusso generale previsto dal prototipo.

Il sistema di accessi è strettamente collegato alla distinzione tra utenti normali e amministratori prevista dal progetto.

L’utente normale può proporre nuovi contenuti e modifiche, ma le sue azioni devono essere convalidate da un admin. L’amministratore, invece, dispone di privilegi aggiuntivi, tra cui creazione, modifica, eliminazione e convalida dei contenuti. Questa differenza di ruolo influisce direttamente sulla visibilità di alcuni comandi nell’interfaccia e sul comportamento delle pagine successive all’accesso, come home.php, luogo.php e convalida.php.

**index.php**

Dal punto di vista funzionale, la landing page (index.php) rappresenta la prima schermata visibile all’utente e consente di scegliere se effettuare il login, registrarsi oppure accedere direttamente alla Home nel caso in cui la sessione sia già attiva.

**access.php**

La pagina centrale del sistema di accessi è access.php, che contiene la parte grafica comune sia al login sia alla registrazione.

La modalità visualizzata viene stabilita tramite una variabile di sessione, che di default assume il valore login. In questo modo si evita di duplicare la struttura HTML e CSS delle due schermate, mantenendo un’unica pagina capace di alternare i due form in base al contesto. I file login.php e signup.php contengono invece solo la logica server-side e le query necessarie per interagire con il database.

**login.php**

Il login consente a un utente già registrato di autenticarsi mediante nome utente e password.

Il sistema cerca nel database il record corrispondente nella tabella utenti, recupera l’hash della password e verifica la corrispondenza con il valore inserito nel form.

Se i dati sono corretti, vengono salvate in sessione le informazioni principali dell’utente, come identificativo, nome utente, eventuale ruolo amministrativo e immagine profilo.

Se invece le credenziali non risultano valide, il sistema restituisce un messaggio di errore e riporta l’utente alla pagina di accesso.

La presenza nel database del campo password_hash conferma che le password non vengono memorizzate in chiaro, ma in forma hashata.

**signup.php**

La registrazione permette di creare un nuovo account come utente normale. In coerenza con la documentazione del database, il campo is_admin ha valore predefinito pari a 0, quindi l’assegnazione dei privilegi amministrativi non avviene tramite form pubblico ma resta a carico del DBA o di amministratori già esistenti. Inoltre, il nome utente deve rispettare il vincolo di lunghezza maggiore di tre caratteri, mentre la foto profilo è facoltativa, perché il campo pfp può essere nullo e, in assenza di immagine caricata, il sistema utilizza un’icona predefinita. Queste scelte riflettono le rettifiche introdotte nella documentazione e nella struttura della tabella utenti.

Durante la registrazione, l’utente inserisce nome utente, password, conferma password ed eventualmente una foto profilo. Il sistema verifica che i campi obbligatori siano stati compilati, controlla che le due password coincidano, verifica che il nome utente non sia già presente nel database e, se viene caricata un’immagine, controlla dimensione massima e tipo MIME consentito. In caso di esito positivo, la password viene trasformata in hash e il nuovo utente viene salvato nel database. Se non viene caricata alcuna immagine, viene assegnato il percorso della foto profilo di default.

**logout.php**

Il logout viene gestito da un file dedicato, logout.php, che ha il compito di svuotare la sessione, distruggerla e reindirizzare l’utente verso la landing page. In questo modo si conclude la sessione autenticata e il sistema torna allo stato iniziale.

**Gestione admin**

Gli admin sono gestiti da altri admin o dal DBA.

Di base, ogni utente creato non sarà admin (is_admin = 0), ma un altro admin potrà renderlo tale.

Il primo admin, DBAdmin, avrà come password 123!OlmoOlmo!321, il cui hash è:

\$2y\$12\$MFMnmfE16pJ8b5w30SLBoepi3T4BRhTjhvK.gTEyutNqKr9C/XuVS

Sarà subito richiesto il cambio password al primo accesso di DBAdmin.
