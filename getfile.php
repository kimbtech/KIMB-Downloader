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

define("KIMB_Downloader", "Clean Request");

//Klassen Autoload
require_once( __DIR__.'/core/oop/all_oop.php' );

//Konfiguration lesen
$conffile = new KIMBdbf('config.kimb');
$allgsysconf = $conffile->read_kimb_id('001');

//allgemeine Funktionen usw. laden
require_once(__DIR__.'/core/conf/funktionen.php');

//session, Fehleranzeige, Robots-Header, Content, Codierung
SYS_INIT( $allgsysconf['robots'] );

//Pfad zum Datei-/Codeverzeichnis
$codefolder = __DIR__.'/files';

//System initialisiert!

//Konfigurator laufengelassen?
if( empty( $allgsysconf['siteurl'] ) ){
	open_url( 'configurator.php' );
}

//Das Dateisystem sichern, mit "./../"" könnte man sonst in tiefere Ordner gelangen 
if( strpos($_GET['file'], "..") !== false ){
	echo ('Do not hack me!!');
	die;
}

//Umgebung nach Request erstellen
//	Pfad zur Datei relativ zu /files/
$urlfrag = $_GET['file'];
//	parsed setzen
$parsed = 'getfile';
//	absoluter Pfad zur Datei
$folder = $codefolder.'/'.$urlfrag;
//	Dateiname
$filename = basename( $urlfrag );

//	Größe der Datei
$filesize = filesize( $folder );

//	MIME Type der Datei
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimetype = finfo_file($finfo, $folder);
finfo_close($finfo);

//Ist eine Datei gefordert?
//Darf der User die Datei sehen?
if( is_file( $folder ) && check_rights( $folder ) ){
	//Header MIME & Encoding
	header('Content-type: '.$mimetype.'; charset=utf-8');
	//Inline (Vorschau) oder Download?
	if( isset( $_GET['inline'] ) ){
		//Vorschau
		header('Content-Disposition: inline; filename="'.$filename.'"');
	}
	else{
		//Download
		header('Content-Disposition: attachment; filename="'.$filename.'"');
	}
	//Dateigröße
	header( 'Content-Length: '.$filesize);
	//Datei ausgeben
	readfile( $folder );
	//beenden
	die;
}
else{
	//Fehler, keine Rechte oder keine Datei!
	echo( 'Fehlerhafter Zugriff' );
}

?> 