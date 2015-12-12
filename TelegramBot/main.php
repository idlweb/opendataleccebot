<?php
/**
 * Telegram Bot per Comune di Lecce by @piersoft
 * @author originale motore Telegram: Gabriele Grillo <gabry.grillo@alice.it> con riadattamento da parte di
 * Matteo Tempestini per @emergenza prato
  * designed starting from https://github.com/Eleirbag89/TelegramBotPHP

 */

//include(dirname(__FILE__).'/../settings.php');
include('settings_t.php');
include(dirname(dirname(__FILE__)).'/getting.php');
include("Telegram.php");
//include("broadcast.php");
include("QueryLocation.php");
$a="";
$b="";

class main{

const MAX_LENGTH = 4096;


 function start($telegram,$update)
	{

		date_default_timezone_set('Europe/Rome');
		$today = date("Y-m-d H:i:s");
  //  $api = new GoogleURL('AIzaSyBUMmMuuo4WkImc3IHrch3yMLHu5DeFtPA');

		// Instances the class
		$data=new getdata();
  //  $geturl=new getshorturl();
		$db = new PDO(DB_NAME);
    $log="";
		/* If you need to manually take some parameters
		*  $result = $telegram->getData();
		*  $text = $result["message"] ["text"];
		*  $chat_id = $result["message"] ["chat"]["id"];
		*/
    $latgl="";
    $longl="";
		$text = $update["message"] ["text"];
		$chat_id = $update["message"] ["chat"]["id"];
		$user_id=$update["message"]["from"]["id"];
		$location=$update["message"]["location"];
		$reply_to_msg=$update["message"]["reply_to_message"];

		$this->shell($latgl,$longl,$telegram, $db,$data,$text,$chat_id,$user_id,$location,$reply_to_msg);
$db = NULL;
	}

	//gestisce l'interfaccia utente
	 function shell($latgl,$longl,$telegram,$db,$data,$text,$chat_id,$user_id,$location,$reply_to_msg)
	{
		date_default_timezone_set('Europe/Rome');
		$today = date("Y-m-d H:i:s");

		if ($text == "/start") {
				$log=$today. ";new chat started;" .$chat_id. "\n";
			}
      elseif (strpos($text,'?') !== false) {
          $text=str_replace("?","",$text);
          $img = curl_file_create('soldipubblici.png','image/png');
          $contentp = array('chat_id' => $chat_id, 'photo' => $img);
          $telegram->sendPhoto($contentp);
          sleep(1);
          $reply ="Interrogazione del Database di Soldipubblici.gov.it attendere....";
          $reply .= $data->get_spesecorrenti($text);
          $chunks = str_split($reply, self::MAX_LENGTH);
          foreach($chunks as $chunk) {
              $content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
              $telegram->sendMessage($content);
          }
               $log=$today. ";spese correnti sent;" .$chat_id. "\n";
        }
        elseif (strpos($text,'-') !== false) {
            $text=str_replace("-","",$text);
            $img = curl_file_create('bancadelibro.png','image/png');
            $contentp = array('chat_id' => $chat_id, 'photo' => $img);
            $telegram->sendPhoto($contentp);
            sleep(1);
          //  $reply ="Interrogazione del Database di Soldipubblici.gov.it attendere....";
            $reply .= $data->get_libro($text);
            $chunks = str_split($reply, self::MAX_LENGTH);
            foreach($chunks as $chunk) {
                $content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
                $telegram->sendMessage($content);
            }
            if ($reply == NULL){
              $reply .= "\nNessun libro trovato";

              $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
              $telegram->sendMessage($content);
            }else{


            $reply = "Se il libro ricercato è in stato DISPONIBILE,\n";
            $reply .= "puoi contattarci tramite il modulo online:\nhttp://goo.gl/forms/RNZpAQplT2";
            $reply .= "\nRicordati di appuntarti il Numero ID !";

            $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
            $telegram->sendMessage($content);
          }
                 $log=$today. ";libro sent;" .$chat_id. "\n";
          }
          elseif ($text == "/News" || $text == "News") {
            $reply = "Sto interrogano la banca dati per le ultime news dal Comune di Lecce...\n";
            $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
            $telegram->sendMessage($content);
            $reply1 = $data->get_news();
            $chunks = str_split($reply1, self::MAX_LENGTH);
            foreach($chunks as $chunk) {
             // $forcehide=$telegram->buildForceReply(true);
                //chiedo cosa sta accadendo nel luogo
                $content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
                $telegram->sendMessage($content);

            }

              $log=$today. ";bandi e gare sent;" .$chat_id. "\n";
      		}
          elseif ($text == "/bandi e gare" || $text == "bandi e gare") {
            $reply = "Sto interrogano la banca dati per le ultime news su Bandi e Gare...\n";
            $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
            $telegram->sendMessage($content);
            $reply1 = $data->get_bandi();
            $chunks = str_split($reply1, self::MAX_LENGTH);
            foreach($chunks as $chunk) {
             // $forcehide=$telegram->buildForceReply(true);
                //chiedo cosa sta accadendo nel luogo
                $content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
                $telegram->sendMessage($content);

            }

              $log=$today. ";bandi e gare sent;" .$chat_id. "\n";
      		}
			//richiedi previsioni meteo di oggi
			elseif ($text == "/meteo oggi" || $text == "meteo oggi") {
        $reply = "Previsioni Meteo per oggi:\n" .$data->get_forecast("Lecceoggi");
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
        $log=$today. ";previsioni Lecce sent;" .$chat_id. "\n";
				}
			//richiede previsioni meteo di domani
			elseif ($text == "/previsioni" || $text == "previsioni") {

        $reply = "Previsioni Meteo :\n" .$data->get_forecast("Lecce");
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
        $log=$today. ";previsioni Lecce sent;" .$chat_id. "\n";
			}	//richiede rischi di oggi a Lecce
  			elseif ($text == "/bollettini rischi" || $text == "bollettini rischi") {
          $img = curl_file_create('infoalert.png','image/png');
          $contentp = array('chat_id' => $chat_id, 'photo' => $img);
          $telegram->sendPhoto($contentp);
          sleep(1);
          $avviso ="\nInterrogazione in corso al database opendata...";

          $content = array('chat_id' => $chat_id, 'text' => $avviso,'disable_web_page_preview'=>true);
          $telegram->sendMessage($content);


          $reply = "Allerta Meteo Protezione Civile Lecce:\n" .$data->get_allertameteo("Lecceoggi");
          $content = array('chat_id' => $chat_id, 'text' => $reply);
          $telegram->sendMessage($content);

  				$log=$today. ";rischi sent;" .$chat_id. "\n";

  			}//richiede defibrillatori di oggi a Lecce
        elseif ($text == "/hotspot" || $text == "Hot Spot") {


          $avviso ="\nInterrogazione in corso al database opendata...";

          $content = array('chat_id' => $chat_id, 'text' => $avviso,'disable_web_page_preview'=>true);
          $telegram->sendMessage($content);

        $reply = $data->get_hotspot("");

        $reply .="\nPer vedere tutti i luoghi dove è presente un Hot Spot clicca qui:\nhttp://u.osmfr.org/m/61642/";
        //$reply .="\nPer vedere, invece, i luoghi dove è presente un Hot Spot nel raggio di 500mt,\nallora invia la tua posizione cliccando la graffetta \xF0\x9F\x93\x8E e poi digita: hotspot";


        $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
        $telegram->sendMessage($content);

        $log=$today. ";hotspot sent;" .$chat_id. "\n";
        $img = curl_file_create('hotspot.png','image/png');
        $contentp = array('chat_id' => $chat_id, 'photo' => $img);
        $telegram->sendPhoto($contentp);

        }
        //richiede defibrillatori di oggi a Lecce
        elseif ($text == "/defibrillatori" || $text == "defibrillatori") {


          $avviso ="\nInterrogazione in corso al database opendata...";

          $content = array('chat_id' => $chat_id, 'text' => $avviso,'disable_web_page_preview'=>true);
          $telegram->sendMessage($content);

        $reply = $data->get_dae();

        $reply .="\nPer vedere tutti i luoghi dove è presente un defibrillatore (DAE) clicca qui:\nhttp://u.osmfr.org/m/54531/";

        $reply .="\nPer vedere invece i luoghi dove è presente un defribillatore (DAE) nel raggio di 500mt,\nallora invia la tua posizione cliccando la graffetta \xF0\x9F\x93\x8E e poi digita: DAE";

        $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
        $telegram->sendMessage($content);

        $log=$today. ";dae sent;" .$chat_id. "\n";
        $img = curl_file_create('dae.png','image/png');
        $contentp = array('chat_id' => $chat_id, 'photo' => $img);
        $telegram->sendPhoto($contentp);

        }
        elseif ($text == "/orari scuole" || $text == "orari scuole") {

  	 			$log=$today. ";temp requested;" .$chat_id. "\n";
  				$this->create_keyboard_temp_orari($telegram,$chat_id);
  				exit;
  			}
        elseif ($text == "/nido comun." || $text == "nido comun.") {
        $reply = $data->get_orariscuole("nido");

        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
        echo $reply;
          $log=$today. ";orari sent;" .$chat_id. "\n";

        }
        elseif ($text == "/infanzia comunale" || $text == "inf.comun.") {
        $reply = $data->get_orariscuole("infanziacomunale");

        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
         echo $reply;
          $log=$today. ";orari sent;" .$chat_id. "\n";

        }
        elseif ($text == "/infanzia statale" || $text == "inf.statale") {
        $reply = $data->get_orariscuole("infanziastatale");

        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
         echo $reply;
          $log=$today. ";orari sent;" .$chat_id. "\n";

        }
        elseif ($text == "/primaria" || $text == "primaria") {
        $reply = $data->get_orariscuole("primaria");
      //  echo $reply;
        $chunks = str_split($reply, self::MAX_LENGTH);
        foreach($chunks as $chunk) {
         // $forcehide=$telegram->buildForceReply(true);
            //chiedo cosa sta accadendo nel luogo
            $content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
            $telegram->sendMessage($content);

        }
    //    $content = array('chat_id' => $chat_id, 'text' => $reply);
    //    $telegram->sendMessage($content);
      //  $telegram->forwardMessage($content);
          $log=$today. ";orari sent;" .$chat_id. "\n";

        }
        elseif ($text == "/secondaria primogrado" || $text == "secondaria primogrado") {
        $reply = $data->get_orariscuole("secondaria_primogrado");

        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
        echo $reply;
          $log=$today. ";orari sent;" .$chat_id. "\n";

        }
        elseif ($text == "/primaria paritaria" || $text == "primaria paritaria") {
        $reply = $data->get_orariscuole("primariaparitaria");

        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
        echo $reply;
          $log=$today. ";orari sent;" .$chat_id. "\n";

        }
        elseif ($text == "/infanzia paritaria" || $text == "inf.paritaria") {
        $reply = $data->get_orariscuole("infanziaparitaria");

        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
        echo $reply;
          $log=$today. ";orari sent;" .$chat_id. "\n";

        }
        elseif ($text == "tariffasosta" && $location==null) {

          $reply ="Invia la tua posizione cliccando sulla graffetta \xF0\x9F\x93\x8E e poi digita: sosta";

          $content = array('chat_id' => $chat_id, 'text' => $reply);
          $telegram->sendMessage($content);

            $log=$today. ";tariffa_antegps sent;" .$chat_id. "\n";

  			}
			//richiede rischi di oggi a Lecce
			elseif ($text == "/aria" || $text == "qualità aria") {
        $avviso ="\nInterrogazione in corso al database opendata...";
        $content = array('chat_id' => $chat_id, 'text' => $avviso,'disable_web_page_preview'=>true);
        $telegram->sendMessage($content);


      $reply = $data->get_aria("lecce");
      $reply .="\nTabella valori di riferimento e info: http://goo.gl/H1nPxO";

      $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
      $telegram->sendMessage($content);
      $img = curl_file_create('qaria.png','image/png');
      $contentp = array('chat_id' => $chat_id, 'photo' => $img);
      $telegram->sendPhoto($contentp);

				$log=$today. ";aria sent;" .$chat_id. "\n";

			}elseif ($text == "/traffico" || $text == "traffico") {
      $reply = "Segnalazione Demo/Test non reale".$data->get_traffico("lecce");
      $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
      $telegram->sendMessage($content);
			$log=$today. ";traffico sent;" .$chat_id. "\n";
    }elseif ($text == "/monumenti" || $text == "monumenti") {
      $img = curl_file_create('wikiloves.png','image/png');
      $contentp = array('chat_id' => $chat_id, 'photo' => $img);
      $telegram->sendPhoto($contentp);
      sleep(1);
      $avviso ="\nInterrogazione in corso al database opendata...";

      $content = array('chat_id' => $chat_id, 'text' => $avviso,'disable_web_page_preview'=>true);
      $telegram->sendMessage($content);

        $reply = "Monumenti che posso essere fotografati e inseriti nel progetto Wikilovesmonuments\n".$data->get_monumenti("lecce");
        $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
        $telegram->sendMessage($content);
  				$log=$today. ";monumenti sent;" .$chat_id. "\n";

		}elseif ($text == "/mensa scuole" || $text == "mensa scuole") {
      $log=$today. ";mense requested;" .$chat_id. "\n";
      $this->create_keyboard_temp_mensa($telegram,$chat_id);
      exit;
    }elseif ($text == "/Infanzia_Aut_Inverno" || $text == "Infanzia_Aut_Inverno"){
      $avviso ="\nInterrogazione in corso al database opendata...";

      $content = array('chat_id' => $chat_id, 'text' => $avviso,'disable_web_page_preview'=>true);
      $telegram->sendMessage($content);

      $giorni = array("Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato");
      $mesi = array("Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre","Novembre", "Dicembre");

      // giorno della settimana in italiano
      $numero_giorno_settimana = date("w");
      $nome_giorno = $giorni[$numero_giorno_settimana];
      if ($nome_giorno =="Sabato" || $nome_giorno =="Domenica"){
        $content = array('chat_id' => $chat_id, 'text' => "Sabato e Domenica non si fornisce il servizio mensa",'disable_web_page_preview'=>true);
        $telegram->sendMessage($content);
      }
      function datediff($tipo, $partenza, $fine)
        {
            switch ($tipo)
            {
                case "A" : $tipo = 365;
                break;
                case "M" : $tipo = (365 / 12);
                break;
                case "S" : $tipo = (365 / 53);
                break;
                case "G" : $tipo = 1;
                break;
            }
            $arr_partenza = explode("/", $partenza);
            $partenza_gg = $arr_partenza[0];
            $partenza_mm = $arr_partenza[1];
            $partenza_aa = $arr_partenza[2];
            $arr_fine = explode("/", $fine);
            $fine_gg = $arr_fine[0];
            $fine_mm = $arr_fine[1];
            $fine_aa = $arr_fine[2];
            $date_diff = mktime(12, 0, 0, $fine_mm, $fine_gg, $fine_aa) - mktime(12, 0, 0, $partenza_mm, $partenza_gg, $partenza_aa);
            $date_diff  = floor(($date_diff / 60 / 60 / 24) / $tipo);
            return $date_diff;
        }
        $diff=1;
        $diff1=-datediff("S", date("d/m/Y"), "05/10/2015");
        if ($diff1 == 10 || $diff1 == 15 || $diff1 == 20|| $diff1 == 25|| $diff1 == 30|| $diff1 == 35|| $diff1 == 40) $diff1 = 5;

          if (($diff1-5)<5 && ($diff1-5)>0) {
          $diff1 =$diff1-5;
        }elseif (($diff1-5)>0){
              $diff1 =$diff1-10;
        }
        if (($diff1-5)<5 && ($diff1-5)>0) {
        $diff1 =$diff1-5;
      }elseif (($diff1-5)>0){
            $diff1 =$diff1-10;
      }
        if (($diff1-5)<5 && ($diff1-5)>0) {
        $diff1 =$diff1-5;
        }elseif (($diff1-5)>0){
          $diff1 =$diff1-10;
      }
      if (($diff1-5)<5 && ($diff1-5)>0) {
      $diff1 =$diff1-5;
      }elseif (($diff1-5)>0){
        $diff1 =$diff1-10;
      }
      $reply = $data->get_mensa(strtoupper(substr($nome_giorno, 0, 4)),"Infanzia_Aut_Inverno",$diff1);
      $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
      $telegram->sendMessage($content);
    	$log=$today. ";mensa scolastica sent;" .$chat_id. "\n";


  	}elseif ($text == "/Primaria_Media_Primavera" || $text == "Primaria_Media_Primavera"){
      $avviso ="\nInterrogazione in corso al database opendata...";

      $content = array('chat_id' => $chat_id, 'text' => $avviso,'disable_web_page_preview'=>true);
      $telegram->sendMessage($content);

      $giorni = array("Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato");
      $mesi = array("Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre","Novembre", "Dicembre");

      // giorno della settimana in italiano
      $numero_giorno_settimana = date("w");
      $nome_giorno = $giorni[$numero_giorno_settimana];
      if ($nome_giorno =="Sabato" || $nome_giorno =="Domenica"){
        $content = array('chat_id' => $chat_id, 'text' => "Sabato e Domenica non si fornisce il servizio mensa",'disable_web_page_preview'=>true);
        $telegram->sendMessage($content);
      }
            function datediff($tipo, $partenza, $fine)
              {
                  switch ($tipo)
                  {
                      case "A" : $tipo = 365;
                      break;
                      case "M" : $tipo = (365 / 12);
                      break;
                      case "S" : $tipo = (365 / 53);
                      break;
                      case "G" : $tipo = 1;
                      break;
                  }
                  $arr_partenza = explode("/", $partenza);
                  $partenza_gg = $arr_partenza[0];
                  $partenza_mm = $arr_partenza[1];
                  $partenza_aa = $arr_partenza[2];
                  $arr_fine = explode("/", $fine);
                  $fine_gg = $arr_fine[0];
                  $fine_mm = $arr_fine[1];
                  $fine_aa = $arr_fine[2];
                  $date_diff = mktime(12, 0, 0, $fine_mm, $fine_gg, $fine_aa) - mktime(12, 0, 0, $partenza_mm, $partenza_gg, $partenza_aa);
                  $date_diff  = floor(($date_diff / 60 / 60 / 24) / $tipo);
                  return $date_diff;
              }
              $diff=1;
              $diff1=-datediff("S", date("d/m/Y"), "05/10/2015");
              if ($diff1 == 10 || $diff1 == 15 || $diff1 == 20|| $diff1 == 25|| $diff1 == 30|| $diff1 == 35|| $diff1 == 40) $diff1 = 5;

                if (($diff1-5)<5 && ($diff1-5)>0) {
                $diff1 =$diff1-5;
              }elseif (($diff1-5)>0){
                    $diff1 =$diff1-10;
              }
              if (($diff1-5)<5 && ($diff1-5)>0) {
              $diff1 =$diff1-5;
            }elseif (($diff1-5)>0){
                  $diff1 =$diff1-10;
            }
              if (($diff1-5)<5 && ($diff1-5)>0) {
              $diff1 =$diff1-5;
              }elseif (($diff1-5)>0){
                $diff1 =$diff1-10;
            }
            if (($diff1-5)<5 && ($diff1-5)>0) {
            $diff1 =$diff1-5;
            }elseif (($diff1-5)>0){
              $diff1 =$diff1-10;
            }
      $reply = $data->get_mensa(strtoupper(substr($nome_giorno, 0, 4)),"Primaria_Media_Primavera",$diff1);
      $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
      $telegram->sendMessage($content);
    	$log=$today. ";mensa scolastica sent;" .$chat_id. "\n";


  	}elseif ($text == "/Infanzia_Primavera" || $text == "Infanzia_Primavera"){
      $avviso ="\nInterrogazione in corso al database opendata...";

      $content = array('chat_id' => $chat_id, 'text' => $avviso,'disable_web_page_preview'=>true);
      $telegram->sendMessage($content);

      $giorni = array("Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato");
      $mesi = array("Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre","Novembre", "Dicembre");

      // giorno della settimana in italiano
      $numero_giorno_settimana = date("w");
      $nome_giorno = $giorni[$numero_giorno_settimana];
      if ($nome_giorno =="Sabato" || $nome_giorno =="Domenica"){
        $content = array('chat_id' => $chat_id, 'text' => "Sabato e Domenica non si fornisce il servizio mensa",'disable_web_page_preview'=>true);
        $telegram->sendMessage($content);
      }
            function datediff($tipo, $partenza, $fine)
              {
                  switch ($tipo)
                  {
                      case "A" : $tipo = 365;
                      break;
                      case "M" : $tipo = (365 / 12);
                      break;
                      case "S" : $tipo = (365 / 53);
                      break;
                      case "G" : $tipo = 1;
                      break;
                  }
                  $arr_partenza = explode("/", $partenza);
                  $partenza_gg = $arr_partenza[0];
                  $partenza_mm = $arr_partenza[1];
                  $partenza_aa = $arr_partenza[2];
                  $arr_fine = explode("/", $fine);
                  $fine_gg = $arr_fine[0];
                  $fine_mm = $arr_fine[1];
                  $fine_aa = $arr_fine[2];
                  $date_diff = mktime(12, 0, 0, $fine_mm, $fine_gg, $fine_aa) - mktime(12, 0, 0, $partenza_mm, $partenza_gg, $partenza_aa);
                  $date_diff  = floor(($date_diff / 60 / 60 / 24) / $tipo);
                  return $date_diff;
              }
              $diff=1;
              $diff1=-datediff("S", date("d/m/Y"), "05/10/2015");
              if ($diff1 == 10 || $diff1 == 15 || $diff1 == 20|| $diff1 == 25|| $diff1 == 30|| $diff1 == 35|| $diff1 == 40) $diff1 = 5;

                if (($diff1-5)<5 && ($diff1-5)>0) {
                $diff1 =$diff1-5;
              }elseif (($diff1-5)>0){
                    $diff1 =$diff1-10;
              }
              if (($diff1-5)<5 && ($diff1-5)>0) {
              $diff1 =$diff1-5;
            }elseif (($diff1-5)>0){
                  $diff1 =$diff1-10;
            }
              if (($diff1-5)<5 && ($diff1-5)>0) {
              $diff1 =$diff1-5;
              }elseif (($diff1-5)>0){
                $diff1 =$diff1-10;
            }
            if (($diff1-5)<5 && ($diff1-5)>0) {
            $diff1 =$diff1-5;
            }elseif (($diff1-5)>0){
              $diff1 =$diff1-10;
            }
      $reply = $data->get_mensa(strtoupper(substr($nome_giorno, 0, 4)),"Infanzia_Primavera",$diff1);
      $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
      $telegram->sendMessage($content);
    	$log=$today. ";mensa scolastica sent;" .$chat_id. "\n";


  	}elseif ($text == "/Primaria_Media_Aut_Inverno" || $text == "Primaria_Media_Aut_Inverno"){
      $avviso ="\nInterrogazione in corso al database opendata...";

      $content = array('chat_id' => $chat_id, 'text' => $avviso,'disable_web_page_preview'=>true);
      $telegram->sendMessage($content);


      $giorni = array("Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato");
      $mesi = array("Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre","Novembre", "Dicembre");

      // giorno della settimana in italiano
      $numero_giorno_settimana = date("w");
      $nome_giorno = $giorni[$numero_giorno_settimana];
      if ($nome_giorno =="Sabato" || $nome_giorno =="Domenica"){
        $content = array('chat_id' => $chat_id, 'text' => "Sabato e Domenica non si fornisce il servizio mensa",'disable_web_page_preview'=>true);
        $telegram->sendMessage($content);
      }
            function datediff($tipo, $partenza, $fine)
              {
                  switch ($tipo)
                  {
                      case "A" : $tipo = 365;
                      break;
                      case "M" : $tipo = (365 / 12);
                      break;
                      case "S" : $tipo = (365 / 53);
                      break;
                      case "G" : $tipo = 1;
                      break;
                  }
                  $arr_partenza = explode("/", $partenza);
                  $partenza_gg = $arr_partenza[0];
                  $partenza_mm = $arr_partenza[1];
                  $partenza_aa = $arr_partenza[2];
                  $arr_fine = explode("/", $fine);
                  $fine_gg = $arr_fine[0];
                  $fine_mm = $arr_fine[1];
                  $fine_aa = $arr_fine[2];
                  $date_diff = mktime(12, 0, 0, $fine_mm, $fine_gg, $fine_aa) - mktime(12, 0, 0, $partenza_mm, $partenza_gg, $partenza_aa);
                  $date_diff  = floor(($date_diff / 60 / 60 / 24) / $tipo);
                  return $date_diff;
              }
              $diff=1;
              $diff1=-datediff("S", date("d/m/Y"), "05/10/2015");
              if ($diff1 == 10 || $diff1 == 15 || $diff1 == 20|| $diff1 == 25|| $diff1 == 30|| $diff1 == 35|| $diff1 == 40) $diff1 = 5;

                if (($diff1-5)<5 && ($diff1-5)>0) {
                $diff1 =$diff1-5;
              }elseif (($diff1-5)>0){
                    $diff1 =$diff1-10;
              }
              if (($diff1-5)<5 && ($diff1-5)>0) {
              $diff1 =$diff1-5;
            }elseif (($diff1-5)>0){
                  $diff1 =$diff1-10;
            }
              if (($diff1-5)<5 && ($diff1-5)>0) {
              $diff1 =$diff1-5;
              }elseif (($diff1-5)>0){
                $diff1 =$diff1-10;
            }
            if (($diff1-5)<5 && ($diff1-5)>0) {
            $diff1 =$diff1-5;
            }elseif (($diff1-5)>0){
              $diff1 =$diff1-10;
            }
      $reply = $data->get_mensa(strtoupper(substr($nome_giorno, 0, 4)),"Primaria_Media_Aut_Inverno",$diff1);
      $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
      $telegram->sendMessage($content);
    	$log=$today. ";mensa scolastica sent;" .$chat_id. "\n";


  	}elseif ($text == "/eventi culturali" || $text == "eventi culturali") {
      $img = curl_file_create('lecce2015.jpg','image/jpg');
      $contentp = array('chat_id' => $chat_id, 'photo' => $img);
      $telegram->sendPhoto($contentp);
      sleep(1);
      $avviso ="\nInterrogazione in corso al database opendata...";

      $content = array('chat_id' => $chat_id, 'text' => $avviso,'disable_web_page_preview'=>true);
      $telegram->sendMessage($content);

        $reply = "Eventi culturali in programmazione:\n";
        $reply .= $data->get_events();
        //  echo $reply;
        $reply .="\n\nInfo e testi completi su www.lecce-events.it\n";

       //$reply .=$data->get_traffico("lecce");
       $chunks = str_split($reply, self::MAX_LENGTH);
       foreach($chunks as $chunk) {
      	// $forcehide=$telegram->buildForceReply(true);
      		 //chiedo cosa sta accadendo nel luogo
      		 $content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
      		 $telegram->sendMessage($content);

       }
      //  $content = array('chat_id' => $chat_id, 'text' => $reply);
      //  $telegram->sendMessage($content);
				$log=$today. ";eventi sent;" .$chat_id."\n";
			}
			//crediti
			elseif ($text == "/informazioni" || $text == "informazioni") {
				 $reply = ("openDataLecceBot e' un servizio sperimentale e dimostrativo per riuso degli openData del Comune di Lecce.
				 Applicazione sviluppata da Francesco Piero Paolicelli @piersoft. Codice sorgente per il riuso gratuito: https://goo.gl/bmUNbK
          \nFonti:
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
          ");

				 $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
				 $telegram->sendMessage($content);
				 $log=$today. ";crediti sent;" .$chat_id. "\n";
			}
			//richiede la temperatura
			elseif ($text == "/temperatura" || $text == "temperatura") {

	 			$log=$today. ";temp requested;" .$chat_id. "\n";
				$this->create_keyboard_temp($telegram,$chat_id);
				exit;
			}
			elseif ($text =="Lecce" || $text == "/temp-lecce")
			{
				 $reply = "Temperatura misurata in zona Lecce centro : " .$data->get_temperature("Lecce centro");
				 $content = array('chat_id' => $chat_id, 'text' => $reply);
				 $telegram->sendMessage($content);
				 $log=$today. ";temperatura Lecce sent;" .$chat_id. "\n";
			}
			elseif ($text =="Nardò" || $text == "/temp-vaianosofignano")
			{
				 $reply = "Temperatura misurata in zona Nardò : " .$data->get_temperature("Nardò");
				 $content = array('chat_id' => $chat_id, 'text' => $reply);
				 $telegram->sendMessage($content);
				 $log=$today. ";temperatura Nardò sent;" .$chat_id. "\n";
			}
			elseif ($text =="Lequile" || $text == "/temp-vaianoschignano")
			{
				 $reply = "Temperatura misurata in zona Lequile : " .$data->get_temperature("Lequile");
				 $content = array('chat_id' => $chat_id, 'text' => $reply);
				 $telegram->sendMessage($content);
				 $log=$today. ";temperatura Lequile sent;" .$chat_id. "\n";
			}
			elseif ($text =="Galatina" || $text == "/temp-montepianovernio")
			{
				 $reply = "Temperatura misurata in zona Galatina : " .$data->get_temperature("Galatina");
				 $content = array('chat_id' => $chat_id, 'text' => $reply);
				 $telegram->sendMessage($content);
				 $log=$today. ";temperatura Galatina sent;" .$chat_id. "\n";

			}

			elseif ($text=="notifiche on" || $text =="/on")
			{
				//abilita disabilita le notifiche automatiche del servizio
				//memorizza lo user_id
            	$statement = "INSERT INTO " . DB_TABLE ." (user_id) VALUES ('" . $user_id . "')";
            	$db->exec($statement);
		//		$reply = "Notifiche da openDataLecceBot abilitate. Per disabilitarle digita /off";
        $reply = "Funzione non ancora implementata";

      	$content = array('chat_id' => $chat_id, 'text' => $reply);
				$telegram->sendMessage($content);
				$log=$today. ";notification set;" .$chat_id. "\n";
			}
			elseif ($text=="notifiche off" || $text =="/off")
			{
				//abilita disabilita le notifiche automatiche del servizio
				//memorizza lo user_id
            	$statement = "DELETE FROM ". DB_TABLE ." where user_id = '" . $user_id . "'";
            	$db->exec($statement);
			//	$reply = "Notifiche da openDataLecceBot disabilitate. Per abilitarle digita /on";
        $reply = "Funzione non ancora implementata";

      	$content = array('chat_id' => $chat_id, 'text' => $reply);
				$telegram->sendMessage($content);
				$log=$today. ";notification reset;" .$chat_id. "\n";
			}
      elseif ($text=="l'acchiappalibro" || $text =="/l'acchiappalibro")
      {
    //  $forcehide=$telegram->buildForceReply(true);
    $img = curl_file_create('bancadelibro.png','image/png');
    $contentp = array('chat_id' => $chat_id, 'photo' => $img);
    $telegram->sendPhoto($contentp);

      $content = array('chat_id' => $chat_id, 'text' => "Istituita presso la sede dell’Assessorato alla Pubblica Istruzione in viale Ugo Foscolo 31/a, con la finalità di allargare la comunità dei lettori offre nuovo servizio acquisisce attraverso spontanee liberalità da parte di enti, cittadini, imprenditori, libri per tutte le età, da destinare alle biblioteche scolastiche, alle famiglie in situazioni di disagio e a tutti coloro che in generale amano la lettura. I libri usati ed in ottimo stato di conservazione sono a disposizione di chiunque.Si può quindi ritirare un libro usato e portarne un altro. Giorni e orari del servizio: martedì pomeriggio dalle alle 16.00 alle ore 17.30 mercoledì mattina dalle alle 10.00 alle ore 12.00. Per ricercare un libro, basta anteporre il carattere - (meno) alla parola da cercare; può essere sia una parte del titolo che dell'autore. Esempio -Tamaro piuttosto che -Cuore.\nAttenzione i caratteri maiuscoli e minuscoli sono differenti.\n( -Cuore è diverso da -cuore ).\nPer l'elenco completo puoi visitare questo link: http://goo.gl/JBCSAb", 'reply_to_message_id' =>$bot_request_message_id,'disable_web_page_preview'=>true);

      $telegram->sendMessage($content);

      exit;
			//----- gestione segnalazioni georiferite : togliere per non gestire le segnalazioni georiferite -----
    }
      elseif ($text=="spese correnti" || $text =="/spese correnti")
      {
    //  $forcehide=$telegram->buildForceReply(true);


      $content = array('chat_id' => $chat_id, 'text' => "Inserisci la voce di spesa corrente da cercare anteponendo il simbolo ? esempio ?spese postali ", 'reply_to_message_id' =>$bot_request_message_id);

      $telegram->sendMessage($content);
      exit;
			//----- gestione segnalazioni georiferite : togliere per non gestire le segnalazioni georiferite -----
    }elseif($location!=null)
      {
      //  $reply = "Funzione non ancora implementata";

    //    $content = array('chat_id' => $chat_id, 'text' => $reply);
    //    $telegram->sendMessage($content);
        $this->location_manager($latgl,$longl,$db,$telegram,$user_id,$chat_id,$location);
        exit;

      }

			elseif($reply_to_msg != NULL)
			{
    //    $reply ="prova";
    //    $content = array('chat_id' => $chat_id, 'text' => $reply);
    //    $telegram->sendMessage($content);
				//inserisce la segnalazione nel DB delle segnalazioni georiferite

        $response=$telegram->getData();

    $type=$response["message"]["video"]["file_id"];
    $text =$response["message"]["text"];
    $risposta="";
    $file_name="";
    $file_path="";
    $file_name="";

    if ($type !=NULL) {
    $file_id=$type;
    $text="video allegato";
    $risposta="ID dell'allegato:".$file_id;
    }

    $file_id=$response["message"]["photo"][0]["file_id"];

    if ($file_id !=NULL) {

    $telegramtk=TELEGRAM_BOT; // inserire il token
    $rawData = file_get_contents("https://api.telegram.org/bot".$telegramtk."/getFile?file_id=".$file_id);
    $obj=json_decode($rawData, true);
    $file_path=$obj["result"]["file_path"];
    $caption=$response["message"]["caption"];
    if ($caption != NULL) $text=$caption;
    $risposta="ID dell'allegato: ".$file_id;

    }
    $typed=$response["message"]["document"]["file_id"];

    if ($typed !=NULL){
    $file_id=$typed;
    $file_name=$response["message"]["document"]["file_name"];
    $text="documento: ".$file_name." allegato";
    $risposta="ID dell'allegato:".$file_id;

    }

    $typev=$response["message"]["voice"]["file_id"];
    if ($typev !=NULL){
    $file_id=$typev;
    $text="audio allegato";
    $risposta="ID dell'allegato:".$file_id;

    }


  $csv_path=dirname(__FILE__).'/./map_data.csv';
  $db_path=dirname(__FILE__).'/./db/lecceod.sqlite';

    $username=$response["message"]["from"]["username"];
    $first_name=$response["message"]["from"]["first_name"];

    $db1 = new SQLite3($db_path);
    $q = "SELECT lat,lng FROM ".DB_TABLE_GEO ." WHERE bot_request_message='".$reply_to_msg['message_id']."'";
    $result=	$db1->query($q);
    $row = array();
    $i=0;



  //  $content = array('chat_id' => $chat_id, 'text' => $row[0]);
  //  $telegram->sendMessage($content);

    while($res = $result->fetchArray(SQLITE3_ASSOC)){

    						if(!isset($res['lat'])) continue;

    						 $row[$i]['lat'] = $res['lat'];
    						 $row[$i]['lng'] = $res['lng'];
    						 $i++;
    				 }

    		//inserisce la segnalazione nel DB delle segnalazioni georiferite
    			$statement = "UPDATE ".DB_TABLE_GEO ." SET text='".$text."',file_id='". $file_id ."',filename='". $file_name ."',first_name='". $first_name ."',file_path='". $file_path ."',username='". $username ."' WHERE bot_request_message ='".$reply_to_msg['message_id']."'";
    			print_r($reply_to_msg['message_id']);
    			$db->exec($statement);

    if ($text==="location" || $text==="benzine" || $text==="farmacie" || $text==="musei" || $text==="fermate" || $text==="sosta" || $text==="defibrillatori1"|| $text==="DAE"|| $text==="dae" || $text=="hotspot" || $text=="Hot Spot"|| $text=="hot spot")
    {
      $around=AROUND;
    	$tag="amenity=pharmacy";

    if ($text==="sosta") {
          $lon=$row[0]['lng'];
          $lat=$row[0]['lat'];

        //   $reply =$data->get_sosta($lat,$lon);

            $reply .="\nClicca qui per la risposta: http://dati.comune.lecce.it/bot/sosta/sosta.php?lat=".$lat."&lon=".$lon;

            $content = array('chat_id' => $chat_id, 'text' => $reply);
            $telegram->sendMessage($content);

              $log=$today. ";sosta sent;" .$chat_id. "\n";
              exit;
          }
          if ($text==="location") {
                 $lon=$row[0]['lng'];
                $lat=$row[0]['lat'];


              //   $reply =$data->get_sosta($lat,$lon);

                  $reply .="\nlat=".$lat." lon=".$lon;

                  $content = array('chat_id' => $chat_id, 'text' => $reply);
                  $telegram->sendMessage($content);


                    exit;
                }
    if ($text==="musei") $tag="tourism=museum";
    if ($text==="benzine") $tag="amenity=fuel";
    if ($text==="fermate") {
    $tag="highway=bus_stop";
    $around=500;
    }
    if ($text==="defibrillatori" || $text==="dae" || $text==="DAE"){
      $tag="emergency=defibrillator";
      $around=500;
    }
    if ($text==="hotspot" || $text==="Hot Spot" || $text=="hot spot"){
      $tag="internet_access=wlan";
      $nome="Hot Spot Lecce Wireless";
      $around=500;
    }


    	      $lon=$row[0]['lng'];
    				$lat=$row[0]['lat'];
    	//prelevo dati da OSM sulla base della mia posizione
    					$osm_data=give_osm_data($lat,$lon,$tag,$around);

    					//rispondo inviando i dati di Openstreetmap
    					$osm_data_dec = simplexml_load_string($osm_data);

    					//per ogni nodo prelevo coordinate e nome
    					foreach ($osm_data_dec->node as $osm_element) {
    						$nome="";
                $dist="";
                $miles="";
                $lat10="";
                $long10="";
                $data="";
    						foreach ($osm_element->tag as $key) {
                  //print_r($key);
    							if ($key['k']=='name' || $key['k']=='wheelchair' || $key['k']=='phone' || $key['k']=='addr:street' || $key['k']=='bench'|| $key['k']=='shelter')
    							{
                    $valore=utf8_encode($key['v']);
                    $valore=str_replace("yes","si",$valore);

    							if ($key['k']=='wheelchair')
    									{

    											$valore=str_replace("limited","con limitazioni",$valore);
    											$nome .="Accessibile in carrozzella: ".$valore;
    									}
    							if ($key['k']=='phone')	$nome  .="Telefono: ".utf8_encode($key['v'])."\n";
    							if ($key['k']=='addr:street')	$nome .="Indirizzo: ".utf8_encode($key['v'])."\n";
    							if ($key['k']=='name')	$nome  .="Nome: ".utf8_encode($key['v'])."\n";
	                if ($key['k']=='bench')	$nome  .="Panchina: ".$valore."\n";
                  if ($key['k']=='shelter')	$nome  .="Pensilina: ".$valore."\n";

                  }

    						}
    						//gestione musei senza il tag nome
    						if($nome=="")
    						{
                  if ($text==="hotspot" || $text==="Hot Spot" || $text=="hot spot") $nome="Hot Spot Lecce Wireless";

    							//	$nome=utf8_encode("Luogo non presente o identificato su Openstreetmap");
    							//	$content = array('chat_id' => $chat_id, 'text' =>$nome);
    							//	$telegram->sendMessage($content);
    						}
                $nome=utf8_decode($nome);
    						$content = array('chat_id' => $chat_id, 'text' =>$nome);
    						$telegram->sendMessage($content);

  $longUrl = "http://www.openstreetmap.org/?mlat=".$osm_element['lat']."&mlon=".$osm_element['lon']."#map=19/".$osm_element['lat']."/".$osm_element['lon']."/".$_POST['qrname'];

  $apiKey = APIT;
//  $apiKey="AIzaSyDe4R1jRRqId46Zl54IPWdCHpF0xotdRIU";
  $postData = array('longUrl' => $longUrl, 'key' => $apiKey);
  $jsonData = json_encode($postData);

  $curlObj = curl_init();

  curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key='.$apiKey);
  curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($curlObj, CURLOPT_HEADER, 0);
  curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
  curl_setopt($curlObj, CURLOPT_POST, 1);
  curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

  $response = curl_exec($curlObj);

  // Change the response json string to object
  $json = json_decode($response);

  curl_close($curlObj);
//  $reply="Puoi visualizzarlo su :\n".$json->id;
  $shortLink = get_object_vars($json);
//return $json->id;

$long10=floatval($osm_element['lon']);
$lat10=floatval($osm_element['lat']);
$theta = floatval($lon)-floatval($long10);
$dist =floatval( sin(deg2rad($lat)) * sin(deg2rad($lat10)) +  cos(deg2rad($lat)) * cos(deg2rad($lat10)) * cos(deg2rad($theta)));
$dist = floatval(acos($dist));
$dist = floatval(rad2deg($dist));
$miles = floatval($dist * 60 * 1.1515 * 1.609344);
$data=0.0;
if ($miles >=1){
  $data =number_format($miles, 2, '.', '')." Km";
} else $data =number_format(($miles*1000), 0, '.', '')." mt";

  $reply ="Puoi visualizzarlo su :\n".$shortLink['id']."\nDista: ".($data)."\n_________";

                $chunks = str_split($reply, self::MAX_LENGTH);
                foreach($chunks as $chunk) {
                 // $forcehide=$telegram->buildForceReply(true);
                    //chiedo cosa sta accadendo nel luogo
                    $content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
                    $telegram->sendMessage($content);

                }
            //		$content = array('chat_id' => $chat_id, 'text' => $reply);
    				//		$telegram->sendMessage($content);
    					 }
              //if ($text=="farmacie") $around="5km";
              if ($around==500) $around="500 mt";
              if ($around!=500) $around="5km";

    					//crediti dei dati
    					if((bool)$osm_data_dec->node)
    					{
    						$content = array('chat_id' => $chat_id, 'text' => utf8_encode($text." attorno alla tua posizione entro ".$around." \n(dati forniti tramite OpenStreetMap. Licenza ODbL (c) OpenStreetMap contributors)"));
    						$bot_request_message=$telegram->sendMessage($content);
    					}else
    					{
    						$content = array('chat_id' => $chat_id, 'text' => utf8_encode("Non ci sono sono luoghi vicini, mi spiace! Se ne conosci uno nelle vicinanze mappalo su www.openstreetmap.org"),'disable_web_page_preview'=>true);
    						$bot_request_message=$telegram->sendMessage($content);
    					}
    }


   else{


    			$reply = "La segnalazione è stata Registrata.\n".$risposta."\nGrazie! ";

          // creare una mappa su umap, mettere nel layer -> dati remoti -> il link al file map_data.csv
    			$longUrl= "http://umap.openstreetmap.fr/it/map/segnalazioni-con-opendataleccebot-x-interni_54105#19/".$row[0]['lat']."/".$row[0]['lng']."/".$_POST['qrname'];
          $apiKey = APIT;
        //  $apiKey="AIzaSyDe4R1jRRqId46Zl54IPWdCHpF0xotdRIU";
          $postData = array('longUrl' => $longUrl, 'key' => $apiKey);
          $jsonData = json_encode($postData);

          $curlObj = curl_init();

          curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key='.$apiKey);
          curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
          curl_setopt($curlObj, CURLOPT_HEADER, 0);
          curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
          curl_setopt($curlObj, CURLOPT_POST, 1);
          curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

          $response = curl_exec($curlObj);

          // Change the response json string to object
          $json = json_decode($response);

          curl_close($curlObj);
        //  $reply="Puoi visualizzarlo su :\n".$json->id;
          $shortLink = get_object_vars($json);
        //return $json->id;

          $reply .="Puoi visualizzarlo su :\n".$shortLink['id'];




    			$content = array('chat_id' => $chat_id, 'text' => $reply);
    			$telegram->sendMessage($content);
    			$log=$today. ";information for maps recorded;" .$chat_id. "\n";
          $csv_path=dirname(__FILE__).'/./map_data.csv';
          $db_path=dirname(__FILE__).'/./lecceod.sqlite';

    			exec(' sqlite3 -header -csv '.$db_path.' "select * from segnalazioni;" > '.$csv_path. ' ');
    }

    		}
			//comando errato
			else{

				 $reply = "Hai selezionato un comando non previsto";
				 $content = array('chat_id' => $chat_id, 'text' => $reply);
				 $telegram->sendMessage($content);
				 $log=$today. ";wrong command sent;" .$chat_id. "\n";
			 }

			//gestione messaggi in broadcast : al momento gestisce il database per iscrizione delle notifiche automatiche ma non invia nessuna notifica
			//da commentare per disabilitare la gestione delle notifiche automatiche
		//  	$this->broadcast_manager($db,$telegram);



			//aggiorna tastiera
			$this->create_keyboard($telegram,$chat_id);

			//log
			file_put_contents(dirname(__FILE__).'/./db/telegram.log', $log, FILE_APPEND | LOCK_EX);

			//db
		//	$statement = "INSERT INTO " . DB_TABLE_LOG ." (date, text, chat_id, user_id, location, reply_to_msg) VALUES ('" . $today . "','" . $text . "','" . $chat_id . "','" . $user_id . "','" . $location . "','" . $reply_to_msg . "')";
    //        $db->exec($statement);

	}


	// Crea la tastiera
	 function create_keyboard($telegram, $chat_id)
		{
			//	$option = array(["meteo oggi","previsioni"],["bollettini rischi","temperatura"],["eventi culturali","qualità aria"],["mensa scuole","orari scuole"],["tariffasosta","monumenti"],["defibrillatori","traffico"],["spese correnti","l'acchiappialibro"],["informazioni"]);
        $option = array(["meteo oggi","previsioni"],["temperatura","qualità aria"],["bollettini rischi","traffico"],["eventi culturali","l'acchiappalibro"],["monumenti","tariffasosta"],["mensa scuole","orari scuole"],["bandi e gare","spese correnti"],["Hot Spot","defibrillatori"],["News","informazioni"]);
      	$keyb = $telegram->buildKeyBoard($option, $onetime=false);
				$content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[seleziona un'etichetta oppure clicca sulla graffetta \xF0\x9F\x93\x8E e poi 'posizione'. ]");
				$telegram->sendMessage($content);
		}

	//crea la tastiera per scegliere la zona temperatura
	 function create_keyboard_temp($telegram, $chat_id)
		{
				$option = array(["Lecce","Lequile"],["Nardò", "Galatina"]);
				$keyb = $telegram->buildKeyBoard($option, $onetime=false);
				$content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[Seleziona la località. ]");
				$telegram->sendMessage($content);
		}
    //crea la tastiera per scegliere tipo di scuola
  	 function create_keyboard_temp_orari($telegram, $chat_id)
  		{
  				$option = array(["nido comun.","inf.comun."],["inf.statale","inf.paritaria"],["primaria","primaria paritaria"],["secondaria primogrado"]);
  				$keyb = $telegram->buildKeyBoard($option, $onetime=false);
  				$content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[Seleziona la tipologia di scuola. ]");
  				$telegram->sendMessage($content);
  		}
      function create_keyboard_temp_mensa($telegram, $chat_id)
       {
           $option = array(["Infanzia_Aut_Inverno","Infanzia_Primavera"],["Primaria_Media_Aut_Inverno","Primaria_Media_Primavera"]);
           $keyb = $telegram->buildKeyBoard($option, $onetime=false);
           $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[Seleziona la tipologia di scuola. ]");
           $telegram->sendMessage($content);
       }
    //crea la tastiera per farmacie
     function create_keyboard_poi($telegram, $chat_id)
      {
          $option = array(["farmacie","benzine"],["musei"]);
          $keyb = $telegram->buildKeyBoard($option, $onetime=false);
          $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[Seleziona il luogo di interesse. ]");
          $telegram->sendMessage($content);
      }
	//controlla le condizioni per gestire le notifiche automatiche
	function broadcast_manager($db,$telegram)
		{
			//gestione allarmi da completare.
			if(check_alarm())
			{
				sendMessagetoAll($db,$telegram,'message','Prova messaggio broadcast');
			}
		}



  function location_manager($latgl,$longl,$db,$telegram,$user_id,$chat_id,$location)
  	{


  			$lon=$location["longitude"];
  			$lat=$location["latitude"];

      	//rispondo
  			$response=$telegram->getData();

  			$bot_request_message_id=$response["message"]["message_id"];
  			$time=$response["message"]["date"]; //registro nel DB anche il tempo unix

  			$h = "1";// Hour for time zone goes here e.g. +7 or -4, just remove the + or -
  			$hm = $h * 60;
  			$ms = $hm * 60;
  			$timec=gmdate("Y-m-d\TH:i:s\Z", $time+($ms));
  			$timec=str_replace("T"," ",$timec);
  			$timec=str_replace("Z"," ",$timec);
  			//nascondo la tastiera e forzo l'utente a darmi una risposta

	$forcehidek=$telegram->buildKeyBoardHide(true);
  $content = array('chat_id' => $chat_id, 'text' => "[Cosa vuoi comunicarci su questo posto? oppure digita:\n\ndae o farmacie o musei o benzine o sosta\n(tutto minuscolo).\n\nLe indicheremo quelli più vicini] ", 'reply_markup' =>$forcehide, 'reply_to_message_id' =>$bot_request_message_id);

  $bot_request_message=$telegram->sendMessage($content);

      	$forcehide=$telegram->buildForceReply(true);

  			//chiedo cosa sta accadendo nel luogo
// 		$content = array('chat_id' => $chat_id, 'text' => "[Scrivici cosa sta accadendo qui]", 'reply_markup' =>$forcehide, 'reply_to_message_id' =>$bot_request_message_id);

        $content = array('chat_id' => $chat_id, 'text' => "[digita: dae o farmacie o musei o benzine o sosta]", 'reply_markup' =>$forcehide, 'reply_to_message_id' =>$bot_request_message_id);

        $bot_request_message=$telegram->sendMessage($content);


  			//memorizzare nel DB
  			$obj=json_decode($bot_request_message);
  			$id=$obj->result;
  			$id=$id->message_id;

  			//print_r($id);
    		$statement = "INSERT INTO ". DB_TABLE_GEO. " (lat,lng,user,username,text,bot_request_message,time,file_id,file_path,filename,first_name) VALUES ('" . $lat . "','" . $lon . "','" . $user_id . "',' ',' ','". $id ."','". $timec ."',' ',' ',' ',' ')";
        $db->query($statement);


        }


  }

  ?>
