**RETTIFICA DOCUMENTAZIONE v0.1.1**

Data 14/04/2026

**RETTIFICHE GENERALI**

È stato aggiunto un documento (versioni.docx), per la tracciabilità delle versioni del software.

È stato aggiunto un documento (config.docx), per la documentazione dei file di configurazione del progetto.

È stato aggiunto un documento (auth.docx), per la documentazione specifica del sistema di autenticazione.

**RETTIFICHE DB**

Nel DB nella tabella utenti nel campo pfp (path foto profilo) è stato rimosso il vincolo NOT NULL, per dare la possibilità agli utenti di non inserire la foto profilo. Verrà inserita al suo posto un’icona di default.

Nel DB nella tabella utenti al campo is_admin è stato aggiunto il vincolo DEFAULT 0, insieme alla seguente spiegazione: “La registrazione permette di creare solo utenti normali. L’aggiunta di admin sarà a carico del DBA o altri admin tramite una pagina dedicata.”

Nel DB nella tabella utenti al campo nome_utente è stato aggiunto il vincolo CHECK(LENGTH(nome_utente) \> 3).
