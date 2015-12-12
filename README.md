openDataLecceBot è un sistema automatico di riuso dei dati aperti del Comune di Lecce.

Fonti:
Spese Correnti      -> Soldipubblici.gov.it Lic. CC-BY 3.0

Bollettini rischi   -> Protezione Civile di Lecce - dataset su dati.comune.lecce.it tramite il progetto InfoAlert365 (A cura: Gaetano Lipari)

Eventi culturali    -> Dataset su dati.comune.lecce.it fonte Lecce Events

Qualtà dell'Aria    -> Dataset su dati.comune.lecce.it (A cura: Luciano Mangia)

L'Acchiappialibro   -> Dataset su dati.comune.lecce.it (A cura: Nuccio Massimiliano)

Defibrillatori DAE  -> Dataset su dati.comune.lecce.it (A cura: Alessandro Tondi)

Aree sosta          -> Dataset su dati.comune.lecce.it (A cura: Alessandro Tondi)

Farmacie            -> Dataset su dati.comune.lecce.it (A cura: Lucio Stefanelli)

Monumenti           -> Dataset su dati.comune.lecce.it (A cura: Annarita Cairella)

Traffico            -> Dataset su dati.comune.lecce.it (Sarà a cura: Luisella Gallucci)

Mensa scolastica    -> Dataset su dati.comune.lecce.it (A cura: Nuccio Massimiliano)

Hot Spot            -> Dataset su dati.comune.lecce.it (A cura: Andrea Lezzi)

Bandi ed esiti gare -> Dataset su dati.comune.lecce.it (A cura: Andrea Lezzi)

News                -> Dataset su dati.comune.lecce.it (A cura: Andrea Lezzi)

orari Scuole        -> Dataset su dati.comune.lecce.it (A cura: Nuccio Massimiliano e Elisabetta Indennitate)

Benzinai            -> Dataset su openstreemap Lic. odBL

Musei               -> Dataset su openstreemap Lic. odBL

Meteo e temperatura -> Api pubbliche di www.wunderground.com


Uso:
- Cercare su Telegram l'utente "opendataleccebot" e fare Avvia
- Inviare una segnalazione cliccando "posizione" dal menù a forma di graffetta e dopo alcuni secondi verrà chiesto di inviare il contenuto della segnalazione piuttosto che digitale farmacia o musei o benzine
- Alternativamente si possono cliccare le etichette già predefinite (meteo, eventi, ect)


Installazione:
- Impostare in setting.php il numero di risultati della ricerca openstreetmap e il raggio d'azione
- Impostare e rinominare settings_template.php inserendo il token del bot assegnato e le API Google per lo shortner link. 
- Attivare o un crontab php start.php getupdates oppure attivare SSL , inserire in settings.php il link https al file start.php e lanciare da shell: php start.php sethook



Progetto ispirato e in parte derivato da @emergenzeprato, LIC MIT di Matteo Tempestini http://iltempe.github.io/Emergenzeprato
