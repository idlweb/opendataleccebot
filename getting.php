<?php

//Wrapper delle fonti #emergenzeprato e preparazione dati di interesse per i vari bot
//questa classe deve essere istanziata nei vari JOB che vogliono usare i dati
//by MT

const PROT_CIV = 'http://page2rss.com/rss/28dbb41c5e425167e4d73bf1b00dd7cd';


class getdata {
	//monitoraggio temperatura
	public function get_forecast($where)
	{

		switch ($where) {

			 //Lecce centro
			 case "Lecce":
			$json_string = file_get_contents("http://api.wunderground.com/api/b3f95b06a21229ff/forecast/lang:IT/q/pws:IPUGLIAL7.json");
			$parsed_json = json_decode($json_string);
			$temp_c1 = $parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[0]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[0]->{'fcttext_metric'};
			$temp_c2 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[1]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[1]->{'fcttext_metric'};
			$temp_c3 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[2]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[2]->{'fcttext_metric'};
			$temp_c4 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[3]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[3]->{'fcttext_metric'};
			$temp_c5 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[4]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[4]->{'fcttext_metric'};
			$temp_c6 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[5]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[5]->{'fcttext_metric'};

		break;
		case "Lecceoggi":
	 $json_string = file_get_contents("http://api.wunderground.com/api/b3f95b06a21229ff/forecast/lang:IT/q/pws:IPUGLIAL7.json");
	 $parsed_json = json_decode($json_string);
	 $temp_c1 = $parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[0]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[0]->{'fcttext_metric'};
	 $temp_c2 = "\n".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[1]->{'title'}.", ".$parsed_json->{'forecast'}->{'txt_forecast'}->{'forecastday'}[1]->{'fcttext_metric'};

	break;

	}
	 return $temp_c1.$temp_c2.$temp_c3.$temp_c4.$temp_c5.$temp_c6;

	}
	//scraping dal sito web della PPC Lecce
	public function get_allertameteo($where)
	{

		switch ($where) {

	case "Lecceoggi":

	$html = file_get_contents('http://ppc-lecce.3plab.it');
	$html = iconv('ASCII', 'UTF-8//IGNORE', $html);
	$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
	$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
	$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
	$html =str_replace("Consulta il","<!--",$html);
	$html =str_replace("Commenti disabilitati","-->",$html);
	$html =str_replace("Estratto, per la Zona di Allerta del Comune, del Messaggio di Allerta","",$html);
	$html =str_replace("larea","l&#39;area",$html);
	$html =str_replace("Articoli meno recenti","",$html);
	$html =str_replace("←","",$html);
	$html =str_replace("Criticit","Criticit&#224;",$html);
	$html =str_replace("Visibilit","Visibilit&#224;",$html);
	$html =str_replace("Luned","Luned&#236;",$html);
	$html =str_replace("Marted","Marted&#236;",$html);
	$html =str_replace("Mercoled","Mercoled&#236;",$html);
	$html =str_replace("Gioved","Gioved&#236;",$html);
	$html =str_replace("Venerd","Venerd&#236;",$html);
	$html =str_replace("Viabilit","Venerd&#236;",$html);




	$doc = new DOMDocument;
	$doc->loadHTML($html);

	$xpa    = new DOMXPath($doc);


	$divs   = $xpa->query('//div[starts-with(@id, "post")]');
	$allerta="";

	foreach($divs as $div) {
	    $allerta .= "\n".$div->nodeValue;
	}

	break;

	}
	 return $allerta;

	}


	public function get_events($where)
	{


	switch ($where) {

	case "eventioggi":

	date_default_timezone_set('Europe/Rome');
	date_default_timezone_set("UTC");
	$today=time();
	// un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
	$csv = array_map('str_getcsv', file("https://docs.google.com/spreadsheets/d/14Bvk3Pc37xg-1ijTFvs_3qwLhsrbDVuikEqlXnxlwE8/pub?output=csv"));
	$i=1;
	//echo $max;
	$count = 0;
	foreach($csv as $data=>$csv1){
	   $count = $count+1;
	}
	$eventi="";
	for ($i=0;$i<$count-2;$i++){

	$html =str_replace("/","-",$csv[$i][7]);
	$from = strtotime($html);
	$html1 =str_replace("/","-",$csv[$i][8]);
	$to = strtotime($html1);


	if ($today >= $from && $today <= $to) {
	$eventi .="\n";
	$eventi .="Titolo: ".$csv[$i][4]."\n";
	$eventi .="Tipologia: ".$csv[$i][5]."\n";
	$eventi .="Organizzatore: ".$csv[$i][3]."\n";
	$eventi .="Email contatto: ".$csv[$i][2]."\n";
	$eventi .="Dettagli: ".$csv[$i][6]."\n";
	$eventi .="Luogo: ".$csv[$i][10]."\n";
	$eventi .="Pagamento: ".$csv[$i][9]."\n";
	$eventi .="Inizio: ".$csv[$i][7]."\n";
	$eventi .="Fine: ".$csv[$i][8]."\n";
	if ($csv[$i][18] !=NULL) $eventi .="Puoi visualizzarlo su :\nhttp://www.openstreetmap.org/?mlat=".$csv[$i][18]."&mlon=".$csv[$i][19]."#map=19/".$csv[$i][18]."/".$csv[$i][19];
	$eventi .="\n";
	}
	}


	break;

		}

	//	$eventi="2";
	 return $eventi;

	}


	public function get_aria($where)
	{
	$homepage="";

		switch ($where) {

	case "lecce":
	// un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
	$csv = array_map('str_getcsv', file("https://docs.google.com/spreadsheets/d/1It2A_VDqWFP01Z7UguDDPDrKGY6xD94AdCl7dWgt5YA/pub?gid=1088545279&single=true&output=csv"));
	$homepage  =$csv[0][0];
	$homepage .="\n";

	for ($i=2;$i<=4;$i++){

	$homepage .="\n";
	$homepage .="Nome Centralina: ".$csv[$i][0]."\n";
	$homepage .= "Valore_Pm10: ".$csv[$i][1]." µg/m³\n";
	$homepage .="Valore_Benzene: ".$csv[$i][2]." µg/m³\n";
	$homepage .="Valore_CO: ".$csv[$i][3]." mg/m³\n";
	$homepage .="Valore_SO2: ".$csv[$i][4]." µg/m³\n";
	$homepage .="Valore_PM_2.5: ".$csv[$i][5]." µg/m³\n";
	$homepage .="Valore_O3: ".$csv[$i][6]." µg/m³\n";
	$homepage .="Valore_NO2: ".$csv[$i][7]." µg/m³\n";
	$homepage .="Superati: ".$csv[$i][8]."\n";


	}



	break;

		}

	 return $homepage;

	}

	public function get_traffico($where)
	{
	$homepage="";

		switch ($where) {

	case "lecce":
	// un google sheet fa il parsing del dataset presente su dati.comune.lecce.it
	// servizio sperimentale e Demo.
	$csv = array_map('str_getcsv', file("https://docs.google.com/spreadsheets/d/1IfmPLAFr7Ce0Iyd0fj_LQu1EPR0-vJMY5kaWS7IuRAA/pub?output=csv"));
	//$homepage  =$csv[0][0];
	$homepage .="\n";
	$count = 0;
	foreach($csv as $data=>$csv1){
	   $count = $count+1;
	}
	for ($i=1;$i<$count;$i++){

	$homepage .="\n";
	$homepage .="Tipologia: ".$csv[$i][0]."\n";
	$homepage .="Descrizione: ".$csv[$i][1]."\n";
	$homepage .="Data: ".$csv[$i][2]."\n";
	$homepage .="Luogo: ".$csv[$i][3]."\n";
	$homepage .="Mappa: http://www.openstreetmap.org/#map=19/".$csv[$i][4]."/".$csv[$i][5];
	$homepage .="\n";


	}

	break;

		}

	 return $homepage;

	}


//monitoraggio temperatura
public function get_temperature($where)
{
	switch ($where) {

		 //Lecce centro
		 case "Lecce centro":
		 $json_string = file_get_contents("http://api.wunderground.com/api/b3f95b06a21229ff/conditions/q/pws:IPUGLIAL7.json");
		 $parsed_json = json_decode($json_string);
		 $location = $parsed_json->{'location'}->{'city'};
		 $temp_c = $parsed_json->{'current_observation'}->{'temp_c'};
		 break;

		 //Lequile
		 case "Lequile":
		 $json_string = file_get_contents("http://api.wunderground.com/api/b3f95b06a21229ff/conditions/q/pws:IPUGLIAL3.json");
		 $parsed_json = json_decode($json_string);
		 $location = $parsed_json->{'location'}->{'city'};
		 $temp_c = $parsed_json->{'current_observation'}->{'temp_c'};
		 break;

		 //Galatina
		 case "Galatina":
		 $json_string = file_get_contents("http://api.wunderground.com/api/b3f95b06a21229ff/conditions/q/pws:IPUGLIAG14.json");
		 $parsed_json = json_decode($json_string);
		 $location = $parsed_json->{'location'}->{'city'};
		 $temp_c = $parsed_json->{'current_observation'}->{'temp_c'};
		 break;

		 //Nardò
		 case "Nardò":
		 $json_string = file_get_contents("http://api.wunderground.com/api/b3f95b06a21229ff/conditions/q/pws:IPUGLIAN2.json");
		 $parsed_json = json_decode($json_string);
		 $location = $parsed_json->{'location'}->{'city'};
		 $temp_c = $parsed_json->{'current_observation'}->{'temp_c'};
		 break;

	}
	return $temp_c;
}

//definisci il path dell'immagine
public function get_image_path($image)
{
	return "data/". $image. ".jpg";
}

//preleva ultima allerta del feed protezione civile di Prato o in locale o in remoto e ritorna titolo e data.
public function load_prot($islocal)
{
	date_default_timezone_set('UTC');

	$logfile=(dirname(__FILE__).'/logs/storedata.log');

	if($islocal)
	{
		//carico dati salvati in locale per confrontarli con quelli remoti
		$prot_civ=dirname(__FILE__)."/data/prot.xml";
		echo "carico dati in locale";
		print_r($prot_civ);
	}
	else
	{
		//carico dati salvati in remoto
		$prot_civ=PROT_CIV;
		echo "carico dati da remoto";
		print_r($prot_civ);

	}

	$xml_file=simplexml_load_file($prot_civ);

	if ($xml_file==false)
		{
			print("Errore nella ricerca del file relativo alla protezione civile");
		}

		//ritorna il primo elemento del feed rss
		$data[0]=$xml_file->channel->item->title;
		//print_r($data[0]);
		$data[1]=$xml_file->channel->item->pubDate;
		//print_r($data[1]);
		return $data;
}

public function update_prot($data)
{
	$prot_civ=dirname(__FILE__)."/data/prot.xml";

	// load the document
	$info = simplexml_load_file($prot_civ);

	// update
	$info->channel->item->title = $data[0];
	$info->channel->item->pubDate = $data[1];

	// save the updated document
	$info->asXML($prot_civ);

}


}
//Fonti
//http://www.lamma.rete.toscana.it/…/comuni_web/dati/prato.xml
//http://data.biometeo.it/BIOMETEO.xml
//http://data.biometeo.it/PRATO/PRATO_ITA.xml
//http://www.sir.toscana.it/supports/xml/risks_395/".$today.".xml"
//http://www.wunderground.com/weather/api/
//https://github.com/alfcrisci/WU_weather_list/blob/master/WU_stations.csv
?>
