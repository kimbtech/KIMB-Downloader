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
$urlfrag = $_GET['file'];
$parsed = 'getfile';
$folder = $codefolder.'/'.$urlfrag;
$filename = basename( $urlfrag );

$filesize = filesize( $folder );

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimetype = finfo_file($finfo, $folder);
finfo_close($finfo);

//Module first
require_once(__DIR__.'/core/module/include_fe_first.php');

if( is_file( $folder ) ){
	header('Content-type: '.$mimetype.'; charset=utf-8');
	if( isset( $_GET['inline'] ) ){
		header('Content-Disposition: inline; filename="'.$filename.'"');
	}
	else{
		header('Content-Disposition: attachment; filename="'.$filename.'"');
	}
	header( 'Content-Length: '.$filesize);
	readfile( $folder );
	die;
}
else{
	//$sitecontent->echo_error( 'Fehlerhafter Zugriff' );
	echo( 'Fehlerhafter Zugriff' );
	$errormessset = true;
}


//Module second
require_once(__DIR__.'/core/module/include_fe_second.php');
?>