# 🌳 Sotto l'Olmo

Benvenuto nel progetto **Sotto l'Olmo**.

---

## 👥 Chi siamo

WORK IN PROGRESS

---

## ⚙️ Installazione

### 1. Requisiti

Per installare il progetto sono necessari:

- PHP 7.4 o superiore
- MySQL / MariaDB
- Server web (o XAMPP)

---

### 2. Inizializzazione

Per inizializzare il progetto (database e dati):

1. Accedere alla directory "scripts/" dal browser
2. Aprire il file "index.php", se non aperto automaticamente
   (es. http://localhost/scripts/index.php)
3. Avviare la procedura di inizializzazione (pulsante "Inizializzazione")

### 3. Directory uploads

Se il server web non consente la creazione automatica di directory,
creare manualmente nella root del progetto:

- uploads/pfp
- uploads/banner

Impostare i permessi a 700.

---

## ⚙️ Utilizzo

Accedendo alla root del progetto viene caricata la landing page (index.php).

Da qui è possibile:
- effettuare il login
- registrarsi
- accedere alle funzionalità principali

## ⚙️ Testing

Per testare rapidamente il sistema è disponibile la directory "test/".

Database:
- test/db/ contiene file SQL da importare manualmente nel DBMS
  per creare utenti e articoli di esempio
- I dati riguardanti gli utenti (es. password) sono tutti commentati nei file

File uploads:
- test/uploads/ contiene immagini di esempio
- Copiare la cartella nella root del progetto
- Se necessario, sostituire la directory "uploads/" esistente