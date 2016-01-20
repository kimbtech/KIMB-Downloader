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

//URL Parsen und für /parts/make_XXXX.php vorbereiten
//	$parsed enthält part zu machen

//URL-Schema
//	mit URL Rewriting:
//		example.com/downloader/info/folder(unter files) => Inhalt der readme des Ordners
//		example.com/downloader/explorer/folder(unter files) => Elemente des Ordners
//		example.com/downloader/view/folder(unter files)/Datei => eine Datei anschauen (Vorschau)
//		example.com/downloader/download/folder(unter files)/Datei => eine Datei herunterladen
//	ohne:
//		example.com/downloader/?pfad=(siehe oben[info%2Ffolder])


//alte URLs verarbeiten
//	dazu eine Datei explorer.php die auf neue URL weiterleitet!


//Hier werden die Pfade aus dem Request erstellt.
//Unterscheidung zwischen URL-Rewrite und ID Zugriff

//Es kann zwischen URL-Rewriting per .htaccess oder per $_SERVER['REQUEST_URI'] entschieden werden (Wahl in der Konfiguration)
//	Nur wenn $_SERVER['REQUEST_URI'] gesetzt ist, URL-Rewriting aktiviert und  use_request_url erlaubt ist sowie kein ID-Zugriff stattfindet,
//	wird mit use_request_url gearbeitet
if( $allgsysconf['urlrewrite'] == 'on' && $allgsysconf['use_request_url'] == 'ok' && isset($_SERVER['REQUEST_URI'])){
	$_GET['url'] = $_SERVER['REQUEST_URI'];
}
elseif( $allgsysconf['urlrewrite'] == 'on' ){
	if( !isset( $_GET['url'] ) ){
		$_GET['url'] = '/';
	}
}

//	Die URL für das URL-Rewriting befindet sich hier immer in $_GET['url'], sie wird benutzt, wenn kein ID-Zugriff stattfindet und 
//	URL-Rewriting aktiviert ist
if( isset($_GET['url']) && $allgsysconf['urlrewrite'] == 'on' ){
	
	//Das Dateisystem sichern, mit "./../"" könnte man sonst in tiefere Ordner gelangen 
	if( strpos($_GET['url'], "..") !== false ){
		echo ('Do not hack me!!');
		die;
	}

	//Aufteilen der URL in "Verzeichnisse"
	$parseurl = explode( '/' , $_GET['url'] );

	//Der erste Teil des Array mit den URL-Verzeichnissen ist oft leer ($_GET['url'] beginnt mit /)
	$i = 0;
	if( empty( $parseurl[$i] ) ){
		//zum nächsten Teil gehen
		$i++;
	}

}	
elseif( $allgsysconf['urlrewrite'] == 'off' && isset($_GET['pfad']) ){
	
	//Das Dateisystem sichern, mit "./../"" könnte man sonst in tiefere Ordner gelangen 
	if( strpos($_GET['pfad'], "..") !== false ){
		echo ('Do not hack me!!');
		die;
	}


	$i = 0;
	$parseurl = explode( '/' , $_GET['pfad'] );	
}
else{
	$i = 0;
	$parseurl[0] = '';
}

$urlfrags = array( 'info', 'explorer', 'view', 'download' );

if( in_array( $parseurl[$i], $urlfrags ) ){
	$parsed = $parseurl[$i];
	$i++;	
}
else{
	if( empty( $parseurl[$i] ) ){
		$parsed = 'info';
	}
	else{
		$sitecontent->echo_error( 'Die URL ist fehlerhaft.',true );
		$errormessset = true;
		$parsed = '';
		$folder = $codefolder;
		$urlfrag = '/';
	}
}

if( !empty( $parsed ) ){
	if( empty( $parseurl[$i] ) ){
		$folder = $codefolder;
		$urlfrag = '/';
		
                              $sitecontent->add_canonical_header( 'info/' );
	}
	else{
		$folder = $codefolder;
		$urlfrag;
		
		while( !empty( $parseurl[$i] ) ){
			$folder .= '/'.$parseurl[$i];
			$urlfrag .= '/'.$parseurl[$i];
			$i++;
		}
		
		$sitecontent->add_canonical_header( $parsed.$urlfrag );
	}
	
	if( $parsed == 'info' || $parsed == 'explorer'){
		$err = is_dir( $folder );
	}
	else{
		$err = is_file( $folder );
	}
	
	//Rechte prüfen, ob User Ordner/Datei sehen darf
	if( !check_rights( $folder ) ){
		$err = false;
	}
	
	if( $err == false ){
		$sitecontent->echo_error( 'Der Ordner/ die Datei wurde nicht gefunden.', true );
		$errormessset = true;
		$urlerror = true;
		$parsed = '';
		$folder = $codefolder;
	}
}
?>