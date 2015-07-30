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
	session_name ("KIMBCMS");
	//Session Cookie setzen
	session_start();
	//Fehlermeldungen & HTTP Header
	error_reporting( 0 );
	header('X-Robots-Tag: '.$robots);
	header('Content-Type: '.$cont.'; charset=utf-8');
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

	//sende Mail und gebe zurück
	$f = fopen( __DIR__.'/mail.txt', 'a+' );
	fwrite( $f, $to.'-------------------'.$inhalt."\r\n\r\n" );
	fclose( $f );

	return true;

	//return mail($to, 'Nachricht von: '.$allgsysconf['sitename'], $inhalt, $header);

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

//backendlogin prüfen und error ausgeben
//	$number => englische Zahl für BE Seiten
//	$permiss => more,less,none
//	$die => true,false (soll der Ablauf abgebrochen werden und Error 403 angezeigt werden wenn keine Rechte)
function check_backend_login( $number , $permiss = 'less', $die = true ){
	global $sitecontent, $allgsysconf;

	//Allgemein eingeloggt?
	if( $_SESSION['loginokay'] == $allgsysconf['loginokay'] && $_SESSION["ip"] == $_SERVER['REMOTE_ADDR'] && $_SESSION["useragent"] == $_SERVER['HTTP_USER_AGENT'] ){

		//Hat User Permission more oder less und ist dies als Parameter gegeben?
		if( ( $_SESSION['permission'] == 'more' || $_SESSION['permission'] == 'less' ) && ( $permiss == 'more' || $permiss == 'less' ) ){
			//wenn more gegeben, aber User nicht more -> keine Rechte
			if( $permiss == 'more' && $_SESSION['permission'] != 'more' ){
				//die oder nur return false
				if( $die ){
					//Seiteninhalt per Klasse, dann nutzen, sonst ohne
					if( is_object( $sitecontent ) ){
						$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen!' , '403');
						$sitecontent->output_complete_site();
					}
					else{
						echo( '403 - Sie haben keine Rechte diese Seite zu sehen!' );
					}
					die;
				}
				else{
					return false;
				}
			}
			
			//Permission less übergeben und User muss more oder less sein -> OK
			return true;
		}
		//kein more oder less bei dem User oder nicht als Parameter gegeben
		else{
			//lese BE Leveldatei
			$levellist = new KIMBdbf( 'backend/users/level.kimb' );
			//lese die englischen Zahlen der Nutzergruppe des Users
			$permissteile = $levellist->read_kimb_one( $_SESSION['permission'] );
			//Nutzergruppe vorhanden?
			if( !empty( $permissteile ) ){
					//Zerteile den Sting mit den englischen Zahlen in einzelne, packe in eine Array
					$permissteile = explode( ',' , $permissteile );
					
					//ist in der Nutzergruppe (Array $permissteile) die englische Zahl (Parameter) vorhanden?
					if( !in_array( $number , $permissteile ) ){
						//keine Rechte
						
						//die oder nur return false
						if( $die ){
							//Seiteninhalt per Klasse, dann nutzen, sonst ohne
							if( is_object( $sitecontent ) ){
								$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen!' , '403');
								$sitecontent->output_complete_site();
							}
							else{
								echo( '403 - Sie haben keine Rechte diese Seite zu sehen!' );
							}
							die;
						}
						else{
							return false;
						}
					}
					else{
						//Nutzergruppe des Users hat Rechte für gegebene englische Zahl (Paramter $number)
						return true;
					}

			}
			//Nutzergruppe existiert nicht
			else{
				//die oder nur return false
				if( $die ){
					//Seiteninhalt per Klasse, dann nutzen, sonst ohne
					if( is_object( $sitecontent ) ){
						$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen!' , '403');
						$sitecontent->output_complete_site();
					}
					else{
						echo( '403 - Sie haben keine Rechte diese Seite zu sehen!' );
					}
					die;
				}
				else{
					return false;
				}
			}
		}
	}
	//gar nicht eingeloggt -> keine Rechte
	else{
		//die oder nur return false
		if( $die ){
			//Seiteninhalt per Klasse, dann nutzen, sonst ohne
			if( is_object( $sitecontent ) ){
					$sitecontent->echo_error( 'Sie haben keine Rechte diese Seite zu sehen!' , '403');
					$sitecontent->output_complete_site();
			}
			else{
				echo( '403 - Sie haben keine Rechte diese Seite zu sehen!' );
			}
			die;
		}
		else{
			return false;
		}
	}
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

//rekursiv leoschen
//	$dir => Verzeichnis
function rm_r($dir){
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
				rm_r($dir.'/'.$file);
			}
			else{
				//lösche Datei direkt
				unlink($dir.'/'.$file);
			}
		}
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
		$stelle = mt_rand('0', $anzahl);
		//Ausgabe erweitern 
		$output .= $chars{$stelle};
		$i++;
	}
	//Ausgeben
	return $output;
}

//Verzeichnis rekursiv auf Fotos duchsuchen und als JSON String ausgeben (für Foto Auswahl von TinyMCE)
//Achtung: Komma am Ende des JSON Strings!! (mit listdirrec(); entfernt)
//	$dir => zu durchsuchendes Verzeichnis auf dem Server
//	$grdir => grundlegende URL zum zu durchsuchenden Verzeichnis auf dem Server
function listdirrec_f( $dir, $grdir ){
	global $allgsysconf;
	
	//Verezichnis lesen
	$files = scandir( $dir );

	//alle Dateien duchgehen
	foreach( $files as $file ){

		if( $file == '..' || $file == '.' ){

		}
		elseif( is_file( $dir.'/'.$file ) ){

			//wenn Datei, dann MIME Type bestimmen
			$mime = mime_content_type ( $dir.'/'.$file );

			//MIME String zuschneiden (nur image bleibt)
			$mime = substr( $mime, 0, 5 ); 

			//wenn MIME image, dann zu JSON Sting hinzufügen
			if( $mime == 'image' ){
				$out .= '{title: "'.$grdir.'/'.$file.'", value: "'.$allgsysconf['siteurl'].$grdir.'/'.$file.'"},';
			}
		}
		elseif( is_dir( $dir.'/'.$file ) ){
			//wenn Verzeichnis, dann dieses durchsuchen
			$out .= listdirrec_f( $dir.'/'.$file , $grdir.'/'.$file );
		}
	}
	//JSON Sting ausgeben
	return $out;
}

//wie listdirrec_f(); nur ohne Komma am Ende und bei mehrfachem Aufruf Speicherung der Strings
function listdirrec( $dir, $grdir ){
	global $listdirrecold;

	if( !isset( $listdirrecold[$dir] ) ){
		$out = listdirrec_f( $dir, $grdir );
		$out = substr( $out, 0, strlen( $out ) - 1 );
		return $listdirrecold[$dir] = $out;
	}
	else{
		return $listdirrecold[$dir];
	}
}

//einfaches Hinzufügen von TinyMCE in eine Textarea
//Die benötigten JavaScript Dateien werden im Backend automatisch geladen, im Frontend ist das Hinzufügen des HTML-Headers '<!-- TinyMCE -->' nötig!
//	$big => großes Feld aktivieren (mit TinyMCE Menü) [boolean]
//	$small => kleines Feld aktivieren (ohne TinyMCE menü) [boolean]
//	$ids => Array ()'big' => ' HTML ID des Textarea für großes Feld ', 'small' => ' HTML ID des Textarea für kleines Feld ' ) 
function add_tiny( $big = false, $small = false, $ids = array( 'big' => '#inhalt', 'small' => '#footer' ) ){
	global $sitecontent, $allgsysconf, $tinyoo;

	//$tinyoo => gibt an, ob JS Funktion tinychange(); schon in der Ausagabe 

	//JavaScript Ausgabe beginnen
	$sitecontent->add_html_header('<script>');

	//Funktion tinychange(); schon da?
	if( !$tinyoo ){
		//wenn nicht, dann hinzufügen
		//	Die Funktion tinychange(); verändert den Status des TinyMCE Editoren. Bei einem Aufruf von tinychange( <<HTML ID der Textarea>> ); wird deren TinyMCE Status verändert.
		//	TinyMCE wird entweder ausgeblendet oder eingeblendet.
		$sitecontent->add_html_header('
		var tiny = [];

		function tinychange( id ){
			if( !tiny[id] ){
				tinymce.EditorManager.execCommand( "mceAddEditor", true, id);
				tiny[id] = true;
			}
			else{
				tinymce.EditorManager.execCommand( "mceRemoveEditor", true, id)
				tiny[id] = false;
			}
		}
		
		function disabletooltips(){ 
			$( "iframe" ).tooltip({ disabled: true });
		}
		');
		$tinyoo = true;
	}

	//großer TinyMCE gewünscht?
	if( $big ){
		//Initialisierung von TinyMCE
		//	http://www.tinymce.com
		//	Angabe der Textarea ID aus $ids
		$sitecontent->add_html_header('
		tinymce.init({
			selector: "'.$ids['big'].'",
			theme: "modern",
			plugins: [
				"advlist autosave autolink lists link image charmap preview hr anchor pagebreak",
				"searchreplace wordcount visualblocks visualchars fullscreen",
				"insertdatetime media nonbreaking save table contextmenu directionality",
				"emoticons paste textcolor colorpicker textpattern codemagic"
			],
			toolbar1: "styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent hr",
			toolbar2: "fontselect | undo redo | forecolor backcolor | link image emoticons | preview fullscreen | codemagic searchreplace",
			image_advtab: true,
			language : "de",
			width : 680,
			height : 300,
			resize: "horizontal",
			content_css : "'.$allgsysconf['siteurl'].'/load/system/theme/design_for_tiny.css",
			browser_spellcheck : true,
			image_list: function( success ) {
				success( [ '.listdirrec( __DIR__.'/../../load/userdata', '/load/userdata' ).' ] );
			},
			autosave_interval: "20s",
			autosave_restore_when_empty: true,
			autosave_retention: "60m",
			menubar: "file edit insert view format table",
			convert_urls: false,
			init_instance_callback : "disabletooltips"
		});
		tiny[\''.substr( $ids['big'], 1 ).'\'] = true;
		');

	}
	//kleiner TinyMCE gewünscht?
	if( $small ){
		//Initialisierung von TinyMCE
		//	http://www.tinymce.com
		//	Angabe der Textarea ID aus $ids
		$sitecontent->add_html_header('
		tinymce.init({
			selector: "'.$ids['small'].'",
			theme: "modern",
			plugins: [
				"advlist autosave autolink lists link image charmap preview hr anchor pagebreak",
				"searchreplace wordcount visualblocks visualchars fullscreen",
				"insertdatetime media nonbreaking save table contextmenu directionality",
				"emoticons paste textcolor colorpicker textpattern codemagic"
			],
			toolbar1: "styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent hr",
			toolbar2: "fontselect | undo redo | forecolor backcolor | link image emoticons | preview fullscreen | codemagic searchreplace",
			image_advtab: true,
			language : "de",
			width : 680,
			height : 100,
			resize: "horizontal",
			content_css : "'.$allgsysconf['siteurl'].'/load/system/theme/design_for_tiny.css",
			browser_spellcheck : true,
			menubar : false,
			autosave_interval: "20s",
			autosave_restore_when_empty: true,
			autosave_retention: "60m",
			image_list: function( success ) {
				success( [ '.listdirrec( __DIR__.'/../../load/userdata', '/load/userdata' ).' ] );
			},
			convert_urls: false,
			init_instance_callback : "disabletooltips"
		});
		tiny[\''.substr( $ids['small'], 1 ).'\'] = true;
		');
	}
	//Script beenden
	$sitecontent->add_html_header('</script>');
}

//CMS und KIMB-Software Versionsstings vergleichen
//	Verhaeltnis von $v1 zu $v2, z.B.:
//		return 'newer'	-> 	$v1 neuer als $v2
//		return 'older'	-> 	$v1 aelter als $v2
//		return 'same'	-> 	$v1 gleich wie $v2
//		return false	-> 	$v1 oder $v2 haben eine fehlerhafte Syntax
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

		//Patch
		$papos = stripos( $ver , '-p', $lpos );
	
		$patch = substr( $ver , $papos + 2 );

		$kpos = strrpos( $patch , ',' );

		if( $kpos !== false ){
			$patch = substr( $patch , $kpos + 1 );
		}

		$patch = preg_replace( "/\D/", '', $patch );  

		$teil['patch'] = $patch;

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

// Funktionen von Modulen hinzufügen
require_once( __DIR__.'/../module/include_funcclass.php' );
?>
