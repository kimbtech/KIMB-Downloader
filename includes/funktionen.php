<?php

/*************************************************/
//KIMB-technologies
//KIMB Downloader
//KIMB-technologies.blogspot.com
/*************************************************/
//CC BY-ND 4.0
//http://creativecommons.org/licenses/by-nd/4.0/
//http://creativecommons.org/licenses/by-nd/4.0/legalcode
/*************************************************/
//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
//BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
//IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
//WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
//IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
/*************************************************/



//allgemeine Funktionen

function open_url($url){
	global $allgconfurlweitermeth;
	if($allgconfurlweitermeth == '1'){
		header('Location: '.$url);
		return 'true';
	}
	elseif($allgconfurlweitermeth == '2'){
		echo('<meta http-equiv="Refresh" content="0; URL='.$url.'">');
		return 'true';
		}
	}

function email_to($toadr, $text){
	global $allgconfmail, $allgconfsitename, $allgconfadminmail;
	if($allgconfmail['maildata']=='mail'){
		mail($toadr, 'Neue Nachricht von '.$allgconfsitename, $text, 'From: '.$allgconfsitename.' <'.$allgconfmail['abs'].'>');
		return 'true';
	}
	elseif($allgconfmail['maildata']=='data'){
		$writetext = "mail($toadr, Neue Nachricht von $allgconfsitename, $text, Sent by: $allgconfsitename <".$allgconfmail['abs'].">)\n\r\n";
		$handle = fopen($allgconfmail['logofdatapath'].'/mail.log','a+');
		fwrite($handle, $writetext);
		fclose($handle);
		return 'true';
	}
	else{ return 'false'; }
}


//KIMB-Dateien lesen

function read_kimb_one($datei, $teil){  //bei mehreren teilen, oberster treffer
	global $allgconfserversitepath;
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' '),array('ae','oe','ue','ss','Ae','Oe','Ue', ''), $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	$teiltext = '<['.$teil.']>';
	$inhaltdatei = file_get_contents($allgconfserversitepath.'/kimb-data/'.$datei);
	$teile = explode($teiltext, $inhaltdatei);
	return $teile[1];
	}

function read_kimb_all($datei, $teil, $trenner){
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' '),array('ae','oe','ue','ss','Ae','Oe','Ue', ''), $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	global $allgconfserversitepath;
	$teiltext = '<['.$teil.']>';
	$inhaltdatei = file_get_contents($allgconfserversitepath.'/kimb-data/'.$datei);
	$teile = explode($teiltext, $inhaltdatei);
	$count = '0';
	foreach ($teile as $teil) {
		if ($count % 2 != 0){
			$return .= $teil.$trenner;
		}
		$count++;
	}
	$laenge = strlen($return)-strlen($trenner);
	$returnneu = substr($return, 0, $laenge);
	return $returnneu;
	}
	
function read_kimb_search($datei, $teil, $search){
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' '),array('ae','oe','ue','ss','Ae','Oe','Ue', ''), $datei);
	global $allgconfserversitepath;
	$teiltext = '<['.$teil.']>';
	$inhaltdatei = file_get_contents($allgconfserversitepath.'/kimb-data/'.$datei);
	$teile = explode($teiltext, $inhaltdatei);
	foreach ($teile as $teil) {
		if ($count % 2 != 0){
			if($teil == $search){return 'true';}
		}
		$count++;
	}
	return 'false';
	}

//KIMB-Dateien lesen $teil++

function read_kimb_search_teilpl($datei, $teil, $search){
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' '),array('ae','oe','ue','ss','Ae','Oe','Ue', ''), $datei);
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	$count = '1';
	$gelesen = 'start';
	while($gelesen != ''){
		$teilread = $teil.$count;
		$gelesen = read_kimb_one($datei, $teilread);
		if($gelesen == $search){return 'true';}
		$count++;
	}
	return 'false';
	}

function read_kimb_all_teilpl($datei, $teil, $trenner){
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' '),array('ae','oe','ue','ss','Ae','Oe','Ue', ''), $datei);
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	$count = '1';
	$gelesen = 'start';
	while($gelesen != ''){
		$teilread = $teil.$count;
		$gelesen = read_kimb_one($datei, $teilread);
		if($gelesen != 'entfernt'){$return .= $gelesen.$trenner;}
		$count++;
	}
	$laenge = strlen($return)-(2*strlen($trenner));
	$returnneu = substr($return, 0, $laenge);
	return $returnneu;
	}

//KIMB-Dateien schreiben
	
function write_kimb_new($datei, $teil, $inhalt){
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' '),array('ae','oe','ue','ss','Ae','Oe','Ue', ''), $datei);
	$inhalt = preg_replace('/[\r\n]+/', '', $inhalt);
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	global $allgconfserversitepath;
	if(!file_exists ($allgconfserversitepath.'/kimb-data/'.$datei)){
		$writetext .= '<[about:doc]>KIMB Datei V1.0 KIMB-technologies<[about:doc]>';
		}
	$writetext .= "\r".'<['.$teil.']>'.$inhalt.'<['.$teil.']>';
	$handle = fopen($allgconfserversitepath.'/kimb-data/'.$datei,'a+');
	$ok = fwrite($handle, $writetext);
	fclose($handle);
	if($ok == "false"){return 'false';}	
	else{return 'true';}
	}

function write_kimb_replace($datei, $teil, $inhalt){  //teil darf nur einmal vorhanden sein!!
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' '),array('ae','oe','ue','ss','Ae','Oe','Ue', ''), $datei);
	$inhalt = preg_replace('/[\r\n]+/', '', $inhalt);
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	global $allgconfserversitepath;
	$inhalt = preg_replace('/[\r\n]+/', '', $inhalt);
	$teiltext = '<['.$teil.']>';
	$inhaltdatei = file_get_contents($allgconfserversitepath.'/kimb-data/'.$datei);
	$teile = explode($teiltext, $inhaltdatei);
	$writetext .= $teile[0];
	$writetext .= '<['.$teil.']>'.$inhalt.'<['.$teil.']>';
	$writetext .= $teile[2];
	$handle = fopen($allgconfserversitepath.'/kimb-data/'.$datei,'w+');
	$ok = fwrite($handle, $writetext);
	fclose($handle);
	if($ok == "false" && $inhaltdatei == '' && $teile == ''){return 'false';}	
	else{return 'true';}
	}

function write_kimb_delete($datei, $teil){  //teil darf nur einmal vorhanden sein !!
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' '),array('ae','oe','ue','ss','Ae','Oe','Ue', ''), $datei);
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	global $allgconfserversitepath;
	$teiltext = '<['.$teil.']>';
	$inhaltdatei = file_get_contents($allgconfserversitepath.'/kimb-data/'.$datei);
	$teile = explode($teiltext, $inhaltdatei);
	$writetext .= $teile[0];
	$writetext .= $teile[2];
	$handle = fopen($allgconfserversitepath.'/kimb-data/'.$datei,'w+');
	$ok = fwrite($handle, $writetext);
	fclose($handle);
	if($ok == "false" && $inhaltdatei == '' && $teile == ''){return 'false';}	
	else{return 'true';}
	}

//KIMB-Datei $teil++ schreiben

function for_write_kimb_teilpl_add($datei, $teil){
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' '),array('ae','oe','ue','ss','Ae','Oe','Ue', ''), $datei);
	$count = '1';
	$gelesen = 'start';
	while($gelesen != ''){
		$teilread = $teil.$count;
		$gelesen = read_kimb_one($datei, $teilread);
		$count++;
	}
	return $count-2;
	}

function for_write_kimb_teilpl_del($datei, $teil, $search){
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' '),array('ae','oe','ue','ss','Ae','Oe','Ue', ''), $datei);
	$count = '1';
	$gelesen = 'start';
	while($gelesen != ''){
		$teilread = $teil.$count;
		$gelesen = read_kimb_one($datei, $teilread);
		if($gelesen == $search){return $count;}
		$count++;
	}
	return 'false';
	}

function write_kimb_teilpl($datei, $teil, $inhalt, $todo){
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' '),array('ae','oe','ue','ss','Ae','Oe','Ue', ''), $datei);
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$teil = preg_replace('/[\r\n]+/', '', $teil);
	$inhalt = preg_replace('/[\r\n]+/', '', $inhalt);
	if($todo == 'add'){
		$anzahl = for_write_kimb_teilpl_add($datei, $teil)+1;
		$teilneu = $teil.$anzahl;
		if(write_kimb_new($datei, $teilneu, $inhalt)){return 'true';}
		else{return 'false';}
	}
	elseif($todo == 'del'){
		$teilneu = $teil.for_write_kimb_teilpl_del($datei, $teil, $inhalt);
		if(write_kimb_replace($datei, $teilneu, 'entfernt')){return 'true';}
		else{return 'false';}
	}
	else{
		return 'false';
	}
	}

//KIMB-Datei loeschen




function delete_kimb_datei($datei){
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' '),array('ae','oe','ue','ss','Ae','Oe','Ue', ''), $datei);
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	global $allgconfserversitepath;
	if(unlink($allgconfserversitepath.'/kimb-data/'.$datei)){ return 'true';}
	else{ return 'false';}
	}

?>
