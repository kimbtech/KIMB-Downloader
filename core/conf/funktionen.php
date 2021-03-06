<?php

/*************************************************/
//KIMB Downloader
//Copyright (c) 2015 by KIMB-technologies
/*************************************************/
//This program is free software: you can redistribute it and/or modify
//it under the terms of the GNU General Public License version 3
//published by the Free Software Foundation.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program.
/*************************************************/
//https://www.KIMB-technologies.eu
//https://www.bitbucket.org/kimbtech
//https://www.github.com/kimbtech
//https://www.gnu.org/licenses/gpl-3.0
//https://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/

defined('KIMB_Downloader') or die('No clean Request');

//Session und HTTP Header des CMS initialisieren
//	$robots => Header robots tag
//	$cont => MIME der Ausgabe
function SYS_INIT( $robots, $cont = 'text/html' ){
	global $allgsysconf;
	
	//Lebenszeit des Cookie aus Konfiguration
	$lifetime = $allgsysconf['coolifetime'];
	//SSL oder nicht?
	if( substr( $allgsysconf['siteurl'], 0, 8) == 'https://' ){
		$secure = true;
	}
	else{
		$secure = false;		
	}
	
	//Domain & Path aus Konfiguration
	//http(s):// abschneiden
	if( $secure ){
		$do = substr( $allgsysconf['siteurl'], 8);	
	}
	else{
		$do = substr( $allgsysconf['siteurl'], 7);
	}
	
	//Ende der Domain am / erkennen
	$po = strpos ( $do, '/' );
	//kein /, dann Ende der Domain = Länge von $do
	if( empty( $po )){
		$po = strlen($do);
	}
	//Domain bestimmen
	$domain = substr( $do, 0, $po );
	if( strpos( $domain, ':') !== false ){
		//Domain Port entfernen
		$dopp = strpos( $domain, ':');
		$domain = substr( $domain, 0, $dopp );
	}
	//Path bestimmen
	$path = substr( $do, $po ).'/';
	
	//Sicherung der Session ID
	ini_set('session.use_only_cookies', 1);
	//Session Cookie vorbereiten
	session_set_cookie_params ( $lifetime, $path, $domain, $secure, true );
	session_name ("KIMBDownloader");
	//Session Cookie setzen
	session_start();
	//Fehlermeldungen & HTTP Header
	error_reporting( 0 );
	header('X-Robots-Tag: '.$robots);
	header('Content-Type: '.$cont.'; charset=utf-8');
}

//sichere Zufallszahlen, neu seit PHP 7
//	$a => Anfang
//	$e => Ende
//	Rückgabe: Nummer
function gen_zufallszahl( $a, $e ){
	
	//neue Funktion von PHP 7 verfügbar?
	if( function_exists( 'random_int') ){
		//nutzen
		return random_int( $a, $e );
	}
	else{
		//alte Zufallszahl nutzen
		return mt_rand( $a, $e );
	}
	
}

//email versenden
// $to => Empfänger
// $inhalt => Inhalt
//$mime => MIME Type (text oder html)
function send_mail($to, $inhalt, $mime = 'plain'){
	global $allgsysconf;
	
	//nicht leer ?
	if( empty( $inhalt ) || empty( $to ) ){
		return false;
	}

	//Header erstellen
	//	Absender
	$header = 'From: '.$allgsysconf['sitename'].' <'.$allgsysconf['mailvon'].'>'."\r\n";
	//	MIME & Charset
	$header .= 'MIME-Version: 1.0' ."\r\n";
	$header .= 'Content-Type: text/'.$mime.'; charset=uft-8' . "\r\n";

	return mail($to, 'Nachricht von: '.$allgsysconf['sitename'], $inhalt, $header);

}

//Browser an  andere URL weiterleiten
//	$url => zu öffnende URL
//	$area => insystem ($allgsysconf['siteurl'] + $url)
//	$code => HTTP-Code
function open_url($url, $area = 'insystem', $code = 303 ){
	global $allgsysconf;

	//innerhalb des CMS weiterleiten, also $allgsysconf['siteurl'] vor die URL setzen
	if( $area == 'insystem'){
		$url = $allgsysconf['siteurl'].$url;
	}

	//Weiterleitung per HTTP ?
	if($allgsysconf['urlweitermeth'] == '1'){
		//machen und beenden
		header('Location: '.$url, true, $code );
		die;
	}
	//Weiterleitung per HTML ?
	elseif($allgsysconf['urlweitermeth'] == '2'){
		//machen und beenden
		echo('<meta http-equiv="Refresh" content="0; URL='.$url.'">');
		die;
	}
}

//schauen ob kimb datei vorhanden
//	$datei => KIMB-Datei Name
function check_for_kimb_file($datei){
	//Dateinamen bereinigen
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	$datei = preg_replace( '/[^A-Za-z0-9]_.-/' , '' , $datei );
	//Sicherheit des Dateisystems
	if(strpos($datei, "..") !== false){
		echo ('Do not hack me!!');
		die;
	}
	//Datei vorhanden?
	//Rückgabe
	if(file_exists(__DIR__.'/../oop/kimb-data/'.$datei)){
		return true;
	}
	else{
		return false;
	}
}

// für scan_kimb_dir, alles außer Zahlen aus Sting entfernen
function justnum( $str ) { return preg_replace( "/[^0-9]/" , "" , $str ); }

//alle KIMB-Dateien in einem Verzeichnis ausgeben
// $datei => Verzeichnis
function scan_kimb_dir($datei){
	//Dateinamen bereinigen
	$datei = preg_replace('/[\r\n]+/', '', $datei);
	$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
	$datei = preg_replace( '/[^A-Za-z0-9]_.-/' , '' , $datei );
	//Dateisystem schützen
	if(strpos($datei, "..") !== false){
		echo ('Do not hack me!!');
		die;
	}
	//Verzeichnis in Array lesen
	$files = scandir(__DIR__.'/../oop/kimb-data/'.$datei);
	
	//Rückgabe Array aufbauen
	$i = 0;
	//Alle Dateien durchgehen
	foreach ( $files as $file ){
		//Dateiname werder . noch .. oder index.kimb?
		if( $file != '.' && $file != '..' && $file != 'index.kimb' ){
			//zum Rückgabe Array hinzufügen und Index erhöhen
			$return[$i] .= $file;
			$i++;
		}
	}
	
	//Rückgabe nach ID (Zahl im Dateinamen) sortieren 
	
	//Array mit nur IDs aus Rückgabe Array erstellen
	$returnref = array_map( 'justnum' , $return );
	
	//Rückgabe Array nach dem Array $returnref sortieren
	array_multisort( $returnref, $return);

	//Array Rückgabe ausführen
	return $return;
}

//request URL herausfinden
function get_requ_url(){
	if(isset($_SERVER['HTTPS'])){
		$urlg = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	else{
		$urlg = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	return $urlg;
}

//KIMB-Datei umbenennen/verschieben
//wie PHP rename( $datei1, $datei2  );
function rename_kimbdbf( $datei1 , $datei2 ){
	//Dateinamen bereinigen
	$datei1 = preg_replace('/[\r\n]+/', '', $datei1);
	$datei1 = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei1);
	$datei1 = preg_replace( '/[^A-Za-z0-9]_.-/' , '' , $datei1 );

	$datei2 = preg_replace('/[\r\n]+/', '', $datei2);
	$datei2 = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei2);
	$datei2 = preg_replace( '/[^A-Za-z0-9]_.-/' , '' , $datei2 );

	//Dateisystem schützen
	if(strpos($datei2, "..") !== false || strpos($datei1, "..") !== false){
		echo ('Do not hack me!!');
		die;
	}
	
	//Dateien umbenennen/verschieben
	return rename( __DIR__.'/../oop/kimb-data/'.$datei1 , __DIR__.'/../oop/kimb-data/'.$datei2 );
}

//Backend Login testen
//	$die => Soll false ausgegeben werden oder das Programm beenden werden, wenn User nicht eingeloggt
//	Return => false/true/die [Programm beenden]
function check_backend_login( $die = true, $admin = true ){
	global $sitecontent, $allgsysconf;
	if( $_SESSION['loginokay'] == $allgsysconf['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){
		
		if( $admin ){
			
			//Userdaten (Downloader interner Nutzer und Daten für externes Login )
			$beuserfile = new KIMBdbf( 'beuser.kimb' );
			//Name der Gruppe für API lesen
			$cmsgr = $beuserfile->read_kimb_one( 'api_gruppe' );
			
			if( $_SESSION["usergroup"] == $cmsgr.'_admin' ){
				return true;
			}
			else{
				if( $die ){
					$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen, bitte loggen Sie sich ein!', 403 );
					$sitecontent->output_complete_site();
					die;
				}
				else{
					return false;
				}
			}
		}
		else{
			return true;
		}
		
		/*
		Die Session der User enthält:
			name => Name des Users
			user => Username des Users
			loginokay => Loginokay des Systems
			way => Login am Downloader [dow]/ via API_Login von einem CMS [api]
			ip => IP des Users
			useragent => Useragent des Users
			usergeroup => Usergruppe (Admin [down_admin]/ User [down_user])	
		*/
	}
	else{
		if( $die ){
			$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen, bitte loggen Sie sich ein!', 403 );
			$sitecontent->output_complete_site();
			die;
		}
		else{
			return false;
		}
	}
}

//rekursiv leoschen
//	$dir => Verzeichnis
//	$titleandreadme => Soll auch der Titel und die Readme des Ordners im Explorer gelöscht werden? (true/ false)
//	$titleandreadfolder => Ordner, ab dem der Title und die Readme gelöscht werden sollen. (/xxx/xxx/xxx)
function rm_r($dir, $titleandreadme = false, $titleandreadfolder = '' ){
	//lese Verzeichnis
	$files = scandir($dir);
	//gehe Dateien und Ordner durch
	foreach ($files as $file) {
		if($file == '.' || $file == '..'){
			//nichts
		}
		else{
			//Datei oder Ordner?
			if(is_dir($dir.'/'.$file)){
				//lösche den Ordner
				//	Verschachtelung der Funktion			
				rm_r($dir.'/'.$file, $titleandreadme, $titleandreadfolder.'/'.$file );
			}
			else{
				//lösche Datei direkt
				unlink($dir.'/'.$file);
			}
		}
	}
	
	//Löschen der Readme und des Titels wenn gewünscht
	if( $titleandreadme ){
		delete_title( $titleandreadfolder, 'folder' );
		delete_readme( $titleandreadfolder );
	}
	
	//verlasse und lösche Ordner
	return rmdir($dir);
}

//rekursiv zippen
//	$zip => PHP Zip Objekt
//	$dir => zu zippendes Verzeichnis
//	$base => Basis Ordner in der Zip Datei
function zip_r($zip, $dir, $base = '/'){
	//gibt es $dir überhaupt?
	if (!file_exists($dir)){
		return false;
	}
	//lese Verzeichnis
	$files = scandir($dir);

	//gehe Verzeichnis durch
	foreach ($files as $file){
		if ($file == '..' || $file == '.'){
			//nichts
		}
		else{
			//füge Datei direkt in die Zip Datei
			if (is_file($dir.'/'.$file)){
				$zip->addFile($dir.'/'.$file, $base.$file);
			}
			//erstelle einen neuen Ordner in der Zip
			//Verschachtelung der Funktion mit richiger $base
       			elseif (is_dir($dir.'/'.$file)){
				$zip->addEmptyDir($base.$file);
				zip_r($zip, $dir.'/'.$file, $base.$file.'/');
			}
		}
	}
	//fertig
	return true;
}

//rekursiv kopieren
//	$dir => zu kopierender Ordner
//	$dest => Ziel für Kopien
function copy_r( $dir , $dest ){

	//wenn Ziel nicht vorhanden machen einen neuen Ordner mit richtigen Rechten
	if( !is_dir( $dest ) ){
		mkdir( $dest );
		chmod( $dest , ( fileperms( $dest.'/../' ) & 0777));
	}
	
	//ließ alle Dateien im Verzeichnis
	$files = scandir( $dir );
	foreach ($files as $file){
		if ($file == '..' || $file == '.'){
			//nichts
		}
		else{
			//kopiere Dateien direkt
			if ( is_file($dir.'/'.$file) ){
				copy( $dir.'/'.$file , $dest.'/'.$file );
			}
			//Verschachtelung der Funktion bei Ordnern
       			elseif ( is_dir($dir.'/'.$file) ){
				copy_r( $dir.'/'.$file , $dest.'/'.$file );
			}
		}
	}
	//beenden
	return true;
}

//Zufallsstrings erzeugen
//	$laenge => Länge des zu erzeugenden Stings
// 	$chars => Charakter des Stings
//	$wa => voreingestellte Charakter nutzen ('az' = A bis z; 'num' = 0 bis 9; 'numaz' = 0 bis 9 und A bis z; $chars und $wa nicht gegeben = alles inkl. Sonderzeichen)
function makepassw( $laenge , $chars = '!"#%&()*+,-./:;?[\]_0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz', $wa = 'off' ){
	//Sofern voreingestellte Charakter gewünscht, Auswahl dieser
	if( $wa == 'az' ){
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	}
	elseif( $wa == 'num' ){
		$chars = '0123456789';
	}
	elseif( $wa == 'numaz' ){
		$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	}
	
	//Anzahl der möglichen Charakter bestimmen
	$anzahl = strlen($chars);
	//mit dem ersten Charakter geht es los
	$i = '1';
	//noch keine Ausgabe
	$output = '';
	//solange weniger oder genausoviele Charakter wie gwünscht im Sting weiteren erstellen 
	while($i <= $laenge){
		//Charakter zufällig wählen (Zufallszahl als Stelle für $chars nutzen)
		$stelle = gen_zufallszahl('0', $anzahl);
		//Ausgabe erweitern 
		$output .= $chars{$stelle};
		$i++;
	}
	//Ausgeben
	return $output;
}

//
//CMS und KIMB-Software Versionsstings vergleichen
//	Verhaeltnis von $v1 zu $v2, z.B.:
//		return 'newer' -> $v1 neuer als $v2
//		return 'older' -> $v1 aelter als $v2
//		return 'same' -> $v1 gleich wie $v2
//		return false -> $v1 oder $v2 haben eine fehlerhafte Syntax
//
//	Beispiele und Syntax für Versionsstings:
//		V1.0B-p2
//		V2.11A
//		V2.3F-p1,2
//			Aufbau:
//				"V" für Version
//				Zahl für ganze Versionsnummer
//				"." Punkt als Komma
//				Zahl für Unterversionsnummer (max. 4 Stellen)
//				"A" oder "B" oder "F" für Alpha, Beta, Final Version
//				evtl.: 
//					"-p" für Patch
//		 			Nummer des Patches
//					weitere Nummer mit "," angeschlossen (Reihenfolge brachten: 1,2,3; nicht 2,3,1!!)
//
//Version aus KIMB-CMS V2.11F-p1
//
function compare_cms_vers( $v1 , $v2 ) {
	$v[0] = $v1;
	$v[1] = $v2;
	foreach( $v as $ver ){
		//Ganze erste Nummer
		$vpos = stripos( $ver , 'V' );
		$ppos = strpos( $ver , '.', $vpos );
		$lv = $ppos - $vpos;
		$teil['eins'] = substr( $ver , $vpos + 1 , $lv - 1 );
		//Kommastelle & A,B,F
		$ppos = strpos( $ver , '.' , $vpos );
		$apos = stripos( $ver , 'A', $ppos );
		$bpos = stripos( $ver , 'B', $ppos );
		$fpos = stripos( $ver , 'F', $ppos );
		if ( $apos !== false ){
			$lpos = $apos;
			$teil['bst'] = '1';
		}
		elseif( $bpos !== false ){
			$lpos = $bpos;
			$teil['bst'] = '2';
		}
		elseif( $fpos !== false ){
			$lpos = $fpos;
			$teil['bst'] = '3';
		}
		else{
			return false;
		}
		$lv = $lpos - $ppos;
		$teil['komma'] = substr( $ver , $ppos + 1 , $lv - 1 );
		//als Nachkommastelle behandeln (mal 1000 nehmen)
		$teil['komma'] = (int) str_pad( $teil['komma'], 4, 0 );
		//max 4 Stellen
		$teil['komma'] = (int) substr( $teil['komma'], 0, 4 );
		//Patch
		$papos = stripos( $ver , '-p', $lpos );
	
		//wenn -p gegeben, lesen
		if( $papos !== false  ){
			$patch = substr( $ver , $papos + 2 );
	
			$kpos = strrpos( $patch , ',' );
	
			if( $kpos !== false ){
				$patch = substr( $patch , $kpos + 1 );
			}
	
			$patch = preg_replace( "/\D/", '', $patch );  
	
			$teil['patch'] = $patch;
		}
		else{
			//sonst Patch 0
			$teil['patch'] = 0;
		}
		//fertig
		foreach( $teil as $tei ){
			if( !is_numeric( $tei ) ){
				return false;
			}
		}
		$varr[] = $teil;
	}
	//Ganze erste Nummer
	if( $varr[0]['eins'] > $varr[1]['eins'] ){
		return 'newer';
	}
	elseif( $varr[0]['eins'] < $varr[1]['eins'] ){
		return 'older';
	}
	elseif( $varr[0]['eins'] == $varr[1]['eins'] ){
		//Kommastelle
		if( $varr[0]['komma'] > $varr[1]['komma'] ){
			return 'newer';
		}
		elseif( $varr[0]['komma'] < $varr[1]['komma'] ){
			return 'older';
		}
		elseif( $varr[0]['komma'] == $varr[1]['komma'] ){
			//A,B,F
			if( $varr[0]['bst'] > $varr[1]['bst'] ){
				return 'newer';
			}
			elseif( $varr[0]['bst'] < $varr[1]['bst'] ){
				return 'older';
			}
			elseif( $varr[0]['bst'] == $varr[1]['bst'] ){
				//Patch
				if( $varr[0]['patch'] > $varr[1]['patch'] ){
					return 'newer';
				}
				elseif( $varr[0]['patch'] < $varr[1]['patch'] ){
					return 'older';
				}
				elseif( $varr[0]['patch'] == $varr[1]['patch'] ){
					return 'same';
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}

//Die RequestURL ohne alles nach dem ? extrahieren
//	Rückgabe => RequestURL
function get_req_url(){
	if( strpos( $_SERVER['REQUEST_URI'], '?' ) !== false ){
		$req = substr( $_SERVER['REQUEST_URI'] , '0', '-'.strlen(strrchr( $_SERVER['REQUEST_URI'] , '?' )));
	}
	else{
		$req = $_SERVER['REQUEST_URI'];
	}

	return $req;
}

//Textarea mit CodeMirror vershen
//	für eine Textarea
//		$id => ID der Textarea
//		$mode => Hervorhebungsmodus (MIME Type nach CodeMirror)
//	für mehrere Textareas
//		$id => array(
//				array( "id" => "header_eins", "mode" => "text/x-markdown" ),
//				array( "id" => "header_zwei", "mode" => "text/x-markdown" )
//			);

function add_codemirror( $id, $mode = 'text/x-markdown' ){
	global $sitecontent, $allgsysconf;
	
	//JavaScript Code um Codeeditor anzuzeigen
	//	URL zu den Dateien
	$mirrorpath = $allgsysconf['siteurl'].'/load/codemirror';
	//	JS
	$sitecontent->add_html_header('
	<link rel="stylesheet" href="'.$mirrorpath.'/lib/codemirror.css">
	<style>.CodeMirror { height: auto; }</style>
	<script src="'.$mirrorpath.'/lib/codemirror.js"></script>
	<script src="'.$mirrorpath.'/addon/edit/matchbrackets.js"></script>
	<script src="'.$mirrorpath.'/addon/edit/continuelist.js"></script>
	<script src="'.$mirrorpath.'/mode/htmlmixed/htmlmixed.js"></script>
	<script src="'.$mirrorpath.'/mode/xml/xml.js"></script>
	<script src="'.$mirrorpath.'/mode/javascript/javascript.js"></script>
	<script src="'.$mirrorpath.'/mode/css/css.js"></script>
	<script src="'.$mirrorpath.'/mode/clike/clike.js"></script>
	<script src="'.$mirrorpath.'/mode/php/php.js"></script>
	<script src="'.$mirrorpath.'/mode/markdown/markdown.js"></script>
	');
	
	if( is_array( $id ) ){
		$sitecontent->add_html_header('	<script>');
		foreach( $id as $i ){
			$sitecontent->add_html_header( "\t\t\t".'var mirrorid_'.$i['id'].';');
		}
		$sitecontent->add_html_header('		$(function() {');
		foreach( $id as $i ){
			$sitecontent->add_html_header( "\t\t\t".'mirrorid_'.$i['id'].' = CodeMirror.fromTextArea(document.getElementById("'.$i['id'].'"), { lineNumbers: true, matchBrackets: true, lineWrapping: true, mode: "'.$i['mode'].'" });');
		}
		$sitecontent->add_html_header('		});');
		$sitecontent->add_html_header('	</script>');
	}
	else{
		$sitecontent->add_html_header('	<script>$(function() { var mirrorid_'.$id.' = CodeMirror.fromTextArea(document.getElementById("'.$id.'"), { lineNumbers: true, matchBrackets: true, lineWrapping: true, mode: "'.$mode.'" }); });</script>');	
	}
	
	return;	
}

function make_breadcrumb( $explorer = false, $info = false, $viewfile = false ){
	global $allgsysconf,$urlfrag,$parsed;
	
	$html = '<div class="downloader_urlleiste">'."\r\n";
	
	if( $explorer ){
		
		if( $allgsysconf['urlrewrite'] == 'on' ){
			$hochurl = $allgsysconf['siteurl'].'/explorer'.substr($urlfrag, '0', strlen($urlfrag) - strlen(strrchr($urlfrag, '/')));
		}
		else{
			$hochurl = $allgsysconf['siteurl'].'/?pfad=explorer'.urlencode( substr($urlfrag, '0', strlen($urlfrag) - strlen(strrchr($urlfrag, '/'))) );
		}

		$html .= '<div class="downloader_hoch">'."\r\n";
		$html .= '<a href="'.$hochurl.'" title="&lArr; Zurück">'."\r\n";
		$html .= '<span class="foldericon"><span class="hochpfeil">&larr;</span></span>'."\r\n";
		//$html .= KIMBtechnologies_Fileicons\make_html( NULL, true);
		$html .= '</a>'."\r\n";
		$html .= '</div>'."\r\n";
	}
	
	$frags = explode( '/', $urlfrag );
	
	if( $allgsysconf['urlrewrite'] == 'on' ){
		if( $info ){
			$url = $allgsysconf['siteurl'].'/info';
		}
		else{
			$url = $allgsysconf['siteurl'].'/explorer';
		}
	}
	else{
		if( $info ){
			$url = $allgsysconf['siteurl'].'/?pfad=info';
		}
		else{
			$url = $allgsysconf['siteurl'].'/?pfad=explorer';
		}
	}

	$html  .= '<div class="downloader_breadcrumb">' ."\r\n";
	$html .= ' <a href="'.$url.'" title="Home">Home</a>'."\r\n";
	
	if( $parsed == 'view' || $parsed == 'download' ){
		$is = count( $frags );	
	}
	else{
		$is = count( $frags ) + 2;
	}
	
	$i = 1;
	
	foreach ( $frags as $frag ){
		
		if( $i == $is ){
			break;
		}
		elseif( !empty( $frag ) ){
			if( $allgsysconf['urlrewrite'] == 'on' ){
				$fragstr .= '/'.$frag;
			}
			else{
				$fragstr .= urlencode( '/'.$frag );
			}
			
			$html .= ' / <a href="'.$url.$fragstr.'" title="'.$frag.'">'.$frag.'</a>'."\r\n";
		}
		
		$i++;		
	}
	
	if( $viewfile != false ){
		$html .= ' / '.$viewfile."\r\n";
	}
	
	$html .= '</div>'."\r\n";
	
	$html .= '</div>'."\r\n";
	
	return $html;	
}

//Titel eines Ordners löschen
//	$path => Pfad des Ordners (/xxx/xxx/xxx)
//	$art => folder (alle Titel des Ordners)/ file (nur den Titel für eine Datei)
//	$file => wenn $art auf file, dann hier den Dateinamen
function delete_title( $path, $art, $file = false ){
	
	//dbf mit Zuordnung Ordner -> Titeldatei
	$list = new KIMBdbf( 'title/folderlist.kimb' );
	//ID der Titeldatei suchen
	$fileid = $list->search_kimb_xxxid( $path, 'path' );

	//Fehler, wenn Titeldatei nicht gefunden
	if( !$fileid ){
		return false;
	}
	
	//Titeldatei des Ordners laden
	$folder = new KIMBdbf( 'title/folder_'.$fileid.'.kimb' );
	
	//Ordner löschen??
	if( $art == 'folder' ){
		
		//Titeldatei des Ordners löschen
		$folder->delete_kimb_file();
		//ID Titeldatei aus der Liste der Ordnerzuordnungen löschen 
		$list->write_kimb_id( $fileid , 'del' );
		
		//okay, return unten
		
	}
	elseif( $art == 'file' && $file != false ){
		
		//ID der Datei in Titeldatei suchen
		$datid = $folder->search_kimb_xxxid( $file, 'name' );
		
		//Fehler, wenn Datei nicht gefunden
		if( !$datid ){
			return false;
		}
		
		//ID mit Infos zum Titel der Datei aus Titeldatei löschen
		$folder->write_kimb_id( $datid , 'del' );
		
		//okay, return unten
	}
	else{
		return false;
	}
	
	//dbf's unload
	unset( $list, $folder );
	
	//wenn hier angekommen -> okay!!
	return true;
}

//Reame eines Ordners löschen
//	$path => Pfad des Ordners (/xxx/xxx/xxx)
function delete_readme( $path ){
	
	//dbf mit Zuordnung Ordner -> Infodatei
	$list = new KIMBdbf( 'readme/folderlist.kimb' );
	//ID der Infodatei suchen
	$fileid = $list->search_kimb_xxxid( $path, 'path' );

	//Fehler, wenn Infodatei nicht gefunden
	if( !$fileid ){
		return false;
	}
	
	//Infodatei des Ordners laden
	$folder = new KIMBdbf( 'readme/folder_'.$fileid.'.kimb' );
		
	//Infodatei des Ordners löschen
	$folder->delete_kimb_file();
	//ID Infoldatei aus der Liste der Ordnerzuordnungen löschen 
	$list->write_kimb_id( $fileid , 'del' );
	
	//dbf's unload
	unset( $list, $folder );
	
	//wenn hier angekommen -> okay!!
	return true;
}

//Modul installieren
//	$file => Modul Datei
function install_module( $file ){
	global $sitecontent, $allgsysconf;
	
	//das Temp Verzeichnis erstellen
	if( !mkdir( __DIR__.'/../module/temp/' ) ){
		$sitecontent->echo_error( 'Konnte Ordner nicht erstellen.', 'unknown', 'Modulinstallation' );
		rm_r( __DIR__.'/../module/temp/' );
		return false;
	}
	//Rechte des Temp Verzeichnisses anpassen
	if( !chmod( __DIR__.'/../module/temp/' , ( fileperms( __DIR__.'/../module' ) & 0777) ) ){
		rm_r( __DIR__.'/../module/temp/' );
		$sitecontent->echo_error( 'Konnte Ordner keine Rechte geben.', 'unknown', 'Modulinstallation' );
		return false;
	}
	
	//Eine *.kimbmod Datei ist eine ZIP Datei mit dem PHP Dateien
	//	be_conf.php => Backend Konfigurationsseite
	//	fccl.php => Aufgaben für Funcclass
	//	fe_fi.php => Aufgaben für Frontend First
	//	fe_se.php => Aufgaben für Frontend Second
	//	info_about.php => Infos zum Add-on (name, todo, version, parts, icon)
	//	install.php => wird bei der Installation ausgeführt
		
	//Entpacken der Datei in den Temp Ordner 
	$zip = new ZipArchive;
	if ($zip->open( $file ) === TRUE) {
		$zip->extractTo( __DIR__.'/../module/temp/' );
		$zip->close();
	}
	else{
		$sitecontent->echo_error( 'Konnte Datei nicht entpacken.', 'unknown', 'Modulinstallation' );
		rm_r( __DIR__.'/../module/temp/' );
		return false;
	}
	
	//Infos zu Add-on lesen
	require( __DIR__.'/../module/temp/info_about.php' );
	//	$module_info_todo, $module_info_version geladen
	

	//Todo überprüfen
	if( $module_info_todo == 'temp' ){
		$sitecontent->echo_error( 'Module düfen nicht "temp" heißen.', 'unknown', 'Modulinstallation' );
		rm_r( __DIR__.'/../module/temp/' );
		return false;
	}
	
	//Ist das Add-on schon installiert?
	if( is_dir( __DIR__.'/../module/'.$module_info_todo.'/' ) ){
		
		//Version des neuen Add-ons sichern
		$new__module_info_version = $module_info_version;
			
		//Info Datei des installatierten Moduls lesen
		require( __DIR__.'/../module/'.$module_info_todo.'/info_about.php' );
	
		//neues und altes Add-on anhand der Versionen prüfen
		//	Update?
		if( compare_cms_vers( $module_info_version, $new__module_info_version ) == 'older' ){
			//Update -> also okay
			$sitecontent->echo_message( '<p><b>Modul wurde geupdatet.</b></p>Sie müssen das Modul evtl. einmal deaktivieren und danach wieder aktivieren, damit die Änderungen geladen werden!', 'Modulinstallation' );
		}
		else{
			$sitecontent->echo_error( 'Modul gleicher Version schon installiert.', 'unknown', 'Modulinstallation' );
			rm_r( __DIR__.'/../module/temp/' );
			return false;
		}
	}
	
	//Add-on installieren
		
	//Die PHP Dateien an den richigen Ort verschieben
	copy_r( __DIR__.'/../module/temp/' , __DIR__.'/../module/'.$module_info_todo.'/' );
	
	//einige Add-ons haben noch eine install.php, wenn vorhanden ausführen
	if( file_exists( __DIR__.'/../module/'.$module_info_todo.'/install.php' ) ){
		require( __DIR__.'/../module/'.$module_info_todo.'/install.php' );
	}
	
	//Temp Ordner löschen
	if( !rm_r( __DIR__.'/../module/temp/' ) ){
		$sitecontent->echo_error( 'Temp Ordner konnte nicht gelöscht werden.', 'unknown', 'Modulinstallation' );
		rm_r( __DIR__.'/../module/temp/' );
		return false;
	}
	
	//alles okay :-)
	return true;
}

//Funktionen die von Modulen gesetzt werden sollen, um bestimmte Zusatzfunktionen hinzuzufügen.

//Diese Funktion prüft ob ein User eine bestimmte Datei sehen darf.
//	Da der Downloader ohne Module kein Rechtemanagement erlaubt, gibt diese Funktion immer true zurück.
//	Zur richtigen Nutzung muss diese Funktion von einem Modul in funcclass überschrieben werden.
//		$path => Pfad zur Datei
//		Return => true/false
// 	Bei Modulen "check_rights_by_module" in funcclass nutzen!
function check_rights( $path ){
	global $allgsysconf;
	
	if( function_exists( 'check_rights_by_module' ) ){
		return check_rights_by_module( $path );
	}
	else{
		return true;
	}
}

//Diese Funktion wird aufgerufen, bevor eine Datei das Icon für blank (unbenkannt) bekommt.
//	Module müssen die Funktion überschreiben und entweder false (für das blank Icon) oder HTML-Code zurückgeben.
//		$endung => Endung der Datei für die der Downloader kein Icon kennt
//		Return => false/ HTML Code
// 	Bei Modulen "custom_filetypes_check_by_module" in funcclass nutzen!
function custom_filetypes_check( $endung ){
	global $allgsysconf;
	
	if( function_exists( 'custom_filetypes_check_by_module' ) ){
		return custom_filetypes_check_by_module( $endung );
	}
	else{
		return false;
	}
}

// Funktionen von Modulen hinzufügen
require_once( __DIR__.'/../module/include_funcclass.php' );
?>
