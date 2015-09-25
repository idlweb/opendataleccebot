openDataLecceBot è un sistema automatico di riuso dei dati aperti del Comune di Lecce.
In questa prima versione:
- Meteo
- Previsioni meteo
- Bollettini Protezione Civile Lecce tramite il servizio InfoAlert365 e presenti sul portale opendata di Lecce
- Elenchi culturali pianificati per Lecce Capitale Italiana della Cultura 2015 insieme a quelli dei cittadini inseriti tramite il portale www.lecce-events.it e presenti sul portale opendata di Lecce
- Temperatura dell'aria
- Qualità dell'aria delle centraline gestite del Comune, i cui dati vengono poi validati da Arpa Puglia, e sono  presenti sul portale opendata di Lecce
- Servizio demo e di test sulle congestioni del traffico 
- Elenco Farmacie presenti sul portale opendata di Lecce e riversate su openStreetMap dalla comunità (ringraziamo Federico Cortese per il continuo aggiornamento)

Dati non ufficiali ma presenti e in fase di ispezione.
- Elenco Musei
- Elenco distributori di carburante


Uso:
- Cercare su Telegram l'utente "opendataleccebot" e fare Avvia
- Inviare una segnalazione cliccando "posizione" dal menù a forma di graffetta e dopo alcuni secondi verrà chiesto di inviare il contenuto della segnalazione piuttosto che digitale farmacia o musei o benzine
- Alternativamente si possono cliccare le etichette già predefinite (meteo, eventi, ect)


Installazione:
- Impostare in setting.php il numero di risultati della ricerca openstreetmap e il raggio d'azione
- Impostare e rinominare settings_templete.php inserendo il token del bot assegnato. 
- Attivare o un crontab php start.php getupdates oppure attivare SSL , inserire in settings.php il link https al file start.php e lanciare php start.php sethook


Da implementare:
- Alerting broadcast (invio massivo di avvisi alla pubblicazione di bollettini della Protezione Civile)
- varies



Progetto derivato da @emergenzeprato, LIC MIT di Matteo Tempestini http://iltempe.github.io/Emergenzeprato
