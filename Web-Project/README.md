# Iasi Joaca - Documentatie

## Local Greetings - Aplicatie web pentru organizarea si participarea la evenimente sportive

---

## 1. Introducere

### 1.1 Scopul documentului

Acest document detaliaza cerintele si specificatiile tehnice pentru aplicatia web "Iasi Joaca", o platforma dedicata organizarii si participarii la evenimente sportive in zona Iasi. Documentul serveste ca referinta principala pentru dezvoltatori, stakeholderi si utilizatori finali, oferind o descriere clara a functionalitatilor sistemului, a interactiunilor cu utilizatorul si a arhitecturii tehnice.

### 1.2 Domeniul de aplicare

Aplicatia "Iasi Joaca" are ca scop facilitarea organizarii de evenimente sportive si conectarea persoanelor interesate de sport din zona Iasi. Sistemul permite:

- Vizualizarea terenurilor de sport disponibile pe o harta interactiva
- Crearea si gestionarea evenimentelor sportive
- Inscrierea la evenimente si interactiunea intre participanti
- Gestionarea unui profil de utilizator
- Primirea de notificari despre evenimente prin RSS

## 2. Descriere generala

### 2.1 Perspectiva produsului

"Iasi Joaca" este o aplicatie web independenta, dezvoltata utilizand arhitectura MVC (Model-View-Controller), cu o baza de date MySQL pentru stocarea informatiilor. Aplicatia interactioneaza cu servicii externe precum OpenStreetMap pentru afisarea si gestionarea hartii interactive.

Componentele principale ale sistemului includ:

- Interfata web pentru utilizatori (frontend)
- Serverul de aplicatii PHP (backend)
- Baza de date relationala MySQL
- Integrarea cu API-uri externe (OpenStreetMap)

### 2.2 Functionalitatile produsului

Principalele functionalitati ale aplicatiei sunt:

#### Autentificare si gestionarea conturilor
- Inregistrare utilizatori noi
- Autentificare in sistem
- Gestionarea informatiilor de profil

#### Vizualizarea si interactiunea cu harta
- Afisarea terenurilor de sport disponibile pe harta
- Marcarea diferita a terenurilor libere si ocupate
- Interactiunea cu terenurile pentru crearea de evenimente

#### Gestionarea evenimentelor
- Crearea de evenimente noi
- Setarea parametrilor evenimentului (data, durata, numar maxim de participanti, etc.)
- Afisarea evenimentelor viitoare si trecute
- Vizualizarea detaliilor unui eveniment

#### Participarea la evenimente
- Inscrierea la evenimente
- Retragerea de la evenimente
- Vizualizarea listei de participanti

#### Comunicare intre participanti
- Sistem de chat pentru fiecare eveniment
- Notificari despre evenimente prin RSS

### 2.3 Clasele si caracteristicile utilizatorilor

Aplicatia este destinata urmatoarelor categorii de utilizatori:

#### Utilizatori neautentificati
Persoane care nu au cont sau nu sunt autentificate in sistem.
- Pot vizualiza pagina de login/inregistrare
- Nu au acces la alte functionalitati ale aplicatiei

#### Utilizatori autentificati
Persoane care au cont si sunt autentificate in sistem.
- Pot vizualiza harta terenurilor
- Pot crea evenimente
- Pot participa la evenimente
- Pot interactiona prin sistemul de chat
- Pot gestiona profilul personal

#### Organizatori de evenimente
Utilizatori autentificati care au creat unul sau mai multe evenimente.
- Au toate drepturile utilizatorilor autentificati
- Pot modifica detaliile evenimentelor create
- Pot interactiona cu participantii la evenimentele lor

### 2.4 Mediul de operare

Aplicatia "Iasi Joaca" opereaza in urmatorul mediu:

- **Server:** Apache
- **Limbaj de programare:** PHP
- **Baza de date:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Dispozitive suportate:** Desktop, Tableta, Mobil (design responsiv)

## 3. Cerinte specifice

### 3.1 Interfete externe

#### 3.1.1 Interfete utilizator

Aplicatia contine urmatoarele interfete utilizator principale:

- **Pagina de Login/Inregistrare:** Formulare pentru autentificarea si inregistrarea utilizatorilor
- **Dashboard:** Pagina principala dupa autentificare, prezentand optiuni si functionalitati
- **Harta Terenurilor:** Harta interactiva cu terenurile de sport disponibile
- **Pagina de Evenimente:** Listarea evenimentelor viitoare si trecute
- **Creare Eveniment:** Formular pentru crearea unui eveniment nou
- **Vizualizare Eveniment:** Detalii despre un eveniment specific, inclusiv chat si participanti
- **Profil Utilizator:** Vizualizarea si editarea informatiilor de profil

#### 3.1.2 Interfete hardware

Aplicatia nu necesita interfete hardware specifice, functionand pe orice dispozitiv cu browser web si conexiune la internet.

#### 3.1.3 Interfete software

Aplicatia interactioneaza cu urmatoarele interfete software externe:

- **OpenStreetMap API:** Pentru afisarea si manipularea hartii interactive
- **Overpass API:** Pentru obtinerea informatiilor despre terenurile de sport
- **RSS Feed:** Pentru distribuirea informatiilor despre evenimente

#### 3.1.4 Interfete de comunicare

Comunicarea intre componentele sistemului se realizeaza prin:

- HTTP/HTTPS pentru comunicarea client-server
- Apeluri AJAX pentru actualizari asincrone ale interfetei
- API-uri RESTful pentru schimbul de date intre frontend si backend

### 3.2 Cerinte functionale

#### 3.2.1 Autentificare si inregistrare

**Descriere:** Sistemul permite utilizatorilor sa isi creeze conturi si sa se autentifice.

**Cerinte:**
- Inregistrarea cu username, email, nume, prenume, data nasterii, gen si parola
- Validarea datelor de inregistrare (email valid, parola sigura)
- Autentificarea cu username si parola
- Protejarea rutelor care necesita autentificare
- Deconectarea din sistem

#### 3.2.2 Gestionarea profilului

**Descriere:** Utilizatorii pot vizualiza si edita informatiile personale.

**Cerinte:**
- Vizualizarea datelor personale
- Editarea informatiilor (nume, prenume, email)
- Vizualizarea istoricului de participari la evenimente

#### 3.2.3 Vizualizarea terenurilor pe harta

**Descriere:** Sistemul afiseaza terenurile de sport disponibile pe o harta interactiva.

**Cerinte:**
- Incarcarea si afisarea hartii din OpenStreetMap
- Marcarea terenurilor de sport cu icon-uri diferite (libere/ocupate)
- Limitarea zonei de navigare la regiunea Iasi
- Afisarea informatiilor despre teren la click pe marker
- Optiuni de creare eveniment sau vizualizare evenimente existente la teren

#### 3.2.4 Crearea evenimentelor

**Descriere:** Utilizatorii pot crea evenimente sportive la terenurile disponibile.

**Cerinte:**
- Formular pentru specificarea detaliilor evenimentului (nume, descriere, data, durata, etc.)
- Validarea datelor introduse
- Verificarea disponibilitatii terenului la data si ora selectate
- Stabilirea numarului maxim de participanti
- Setarea cerintelor de participare (numar minim de participari anterioare)

#### 3.2.5 Gestionarea evenimentelor

**Descriere:** Sistemul gestioneaza listarea si statusul evenimentelor.

**Cerinte:**
- Afisarea evenimentelor viitoare si trecute
- Filtrarea evenimentelor dupa locatie
- Actualizarea automata a statusului evenimentelor (pending, running, expired)
- Afisarea detaliilor despre un eveniment (data, ora, locatie, participanti)

#### 3.2.6 Participarea la evenimente

**Descriere:** Utilizatorii se pot inscrie si retrage de la evenimente.

**Cerinte:**
- Inscrierea la un eveniment cu verificarea conditiilor (numar maxim de participanti, cerinte de participare)
- Retragerea de la un eveniment
- Vizualizarea listei de participanti
- Restrictii pentru inscrierea la evenimente care se suprapun

#### 3.2.7 Comunicare intre participanti

**Descriere:** Sistemul faciliteaza comunicarea intre participantii la evenimente.

**Cerinte:**
- Chat dedicat pentru fiecare eveniment
- Afisarea mesajelor cu numele utilizatorului si ora trimiterii
- Marcare speciala pentru organizator in chat
- Restrictionarea accesului la chat doar pentru participantii inscrisi si organizator

#### 3.2.8 Notificari prin RSS

**Descriere:** Sistemul ofera un feed RSS pentru notificari despre evenimente.

**Cerinte:**
- Generarea unui feed RSS cu evenimentele viitoare
- Includerea informatiilor relevante in feed (nume, data, locatie, organizator)
- Actualizarea automata a feed-ului

### 3.3 Cerinte de securitate

- Protectia impotriva SQL Injection prin utilizarea prepared statements
- Validarea tuturor datelor de intrare de la utilizatori
- Stocarea parolelor criptate folosind algoritmi de hashing siguri (password_hash)
- Protectia sesiunilor utilizatorilor
- Accesul restrictionat la functionalitati bazat pe autentificare
- Protectia impotriva atacurilor XSS (htmlspecialchars)

### 3.4 Cerinte de baza de date

Baza de date contine urmatoarele tabele principale:

#### users
Stocheaza informatiile despre utilizatori:
- user_id (cheie primara)
- username (unic)
- password (hashed)
- email (unic)
- firstname, lastname
- events_participated
- date (timestamp)
- role (user/admin)
- birth_date, gender

#### events
Stocheaza informatiile despre evenimente:
- event_id (cheie primara)
- event_name
- location
- description
- event_date
- location_lat, location_lon
- max_participants
- created_by (cheie straina la users)
- status (pending/running/expired)
- created_at
- min_events_participated
- duration

#### event_participants
Stocheaza relatiile dintre utilizatori si evenimentele la care participa:
- event_id (cheie primara partiala, cheie straina la events)
- user_id (cheie primara partiala, cheie straina la users)
- join_date
- status
- registration_date

#### event_chat
Stocheaza mesajele din chat-ul evenimentelor:
- id (cheie primara)
- event_id (cheie straina la events)
- user_id (cheie straina la users)
- message
- sent_at

#### user_profile
Stocheaza informatii suplimentare despre profilurile utilizatorilor:
- id (cheie primara)
- user_id (cheie straina la users)
- phone
- bio
- created_at, updated_at

## 4. Arhitectura sistemului

### 4.1 Arhitectura MVC

Aplicatia "Iasi Joaca" este dezvoltata folosind arhitectura Model-View-Controller (MVC), care separa logica aplicatiei in trei componente interconectate:

#### Model
Modelele gestioneaza logica de business si interactiunea cu baza de date:
- **EventModel:** Gestioneaza evenimentele (creare, listare, inscriere)
- **UserModel:** Gestioneaza utilizatorii (autentificare, inregistrare, profil)
- **ProfileModel:** Gestioneaza profilurile utilizatorilor
- **RSSModel:** Genereaza feed-ul RSS

#### View
View-urile reprezinta interfata cu utilizatorul:
- **login.php, register.php:** Paginile de autentificare si inregistrare
- **dashboard.php:** Pagina principala
- **harta.php:** Pagina cu harta terenurilor
- **evenimente.php:** Pagina cu lista evenimentelor
- **event_create.php:** Pagina de creare eveniment
- **view_event.php:** Pagina de detalii eveniment
- **profile.php, edit_profile.php:** Paginile profilului
- **rss.php:** Feed-ul RSS

#### Controller
Controlerele proceseaza cererile utilizatorilor:
- **loginController.php, registerController.php:** Gestioneaza autentificarea si inregistrarea
- **createEventController.php:** Gestioneaza crearea evenimentelor
- **getEventsController.php:** Gestioneaza listarea evenimentelor
- **checkEventsController.php:** Verifica statusul evenimentelor
- **joinEventController.php, viewEventController.php:** Gestioneaza participarea la evenimente
- **profileController.php:** Gestioneaza profilul utilizatorilor
- **logoutController.php:** Gestioneaza deconectarea

### 4.2 Arhitectura Frontend

Frontend-ul aplicatiei este construit folosind:

- **HTML/CSS:** Pentru structura si stilul paginilor
- **JavaScript:** Pentru interactivitate si comunicare asincrona cu backend-ul
- **Leaflet.js:** Pentru implementarea hartii interactive
- **Fetch API:** Pentru comunicarea cu backend-ul prin API-uri RESTful

Principalele fisiere JavaScript:
- **map.js:** Gestioneaza harta si interactiunea cu terenurile
- **createEvent.js:** Gestioneaza crearea evenimentelor
- **getEvents.js:** Gestioneaza listarea evenimentelor
- **login.js, register.js:** Gestioneaza autentificarea si inregistrarea

Fisierele CSS organizate pe module:
- **styleStartPage.css:** Stiluri pentru pagina principala
- **loginStyle.css:** Stiluri pentru paginile de autentificare si inregistrare
- **evenimenteStyle.css:** Stiluri pentru pagina de evenimente
- **eventCreationStyle.css:** Stiluri pentru pagina de creare eveniment
- **viewEventStyle.css:** Stiluri pentru pagina de detalii eveniment
- **profileStyle.css:** Stiluri pentru paginile profilului

### 4.3 API Endpoints

Aplicatia ofera urmatoarele endpoint-uri API pentru comunicarea intre frontend si backend:

#### Autentificare si inregistrare:
- **POST /controllers/loginController.php:** Autentificare utilizator
- **POST /controllers/registerController.php:** Inregistrare utilizator nou
- **GET /controllers/logoutController.php:** Deconectare utilizator

#### Evenimente:
- **POST /controllers/getEventsController.php:** Obtinere evenimente
- **POST /controllers/createEventController.php:** Creare eveniment nou
- **GET /controllers/checkEventsController.php:** Verificare status evenimente
- **POST /controllers/joinEventController.php:** Inscriere la eveniment
- **POST /controllers/viewEventController.php:** Actiuni in pagina de eveniment (inscriere, retragere, mesaje)

#### Profil:
- **POST /controllers/profileController.php:** Actualizare profil utilizator

#### RSS:
- **GET /views/rss.php:** Feed RSS cu evenimente viitoare

## 6. Concluzie

Acest document a prezentat specificatiile si cerintele pentru aplicatia web "Iasi Joaca", o platforma dedicata organizarii si participarii la evenimente sportive in zona Iasi. Documentul detaliaza arhitectura sistemului, functionalitatile principale, cerintele de performanta si securitate, precum si detalii de implementare si testare.

---

## Tehnologii utilizate

- **Backend:** PHP, Apache, MySQL
- **Frontend:** HTML, CSS, JavaScript, Leaflet.js
- **Arhitectura:** MVC (Model-View-Controller)
- **API-uri externe:** OpenStreetMap, Overpass API
- **Notificari:** RSS Feed
