# RETTIFICA DOCUMENTAZIONE v0.1.3

Data 04/05/2026

## RETTIFICHE GENERALI

È stato aggiunto un documento (admin.md), per la tracciabilità delle autorizzazioni dell’admin.

È stato aggiunto un documento (profilo.md), per la tracciabilità della gestione del profilo.

Tutta la documentazione Word (.docx) è stata convertita in file Markdown (.md).

## RETTIFICHE DB

Nel DB nella tabella utenti è stato aggiunto il campo must_change_password, per gestire il primo accesso di un admin, con un relativo CHECK per assicurarsi che sia un valore booleano.

Nel DB nella tabella articoli è stato aggiunto il campo is_hidden, per gestire la visibilità di una versione, con un relativo CHECK per assicurarsi che sia un valore booleano e un indice per velocizzarne le query.

Nel DB nell’inserimento di base dell’admin è stato aggiunto il campo id_utente con il valore 1.

Nel DB è stata rimossa la tabella link, incorporata definitivamente nella tabella link_articoli.
