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

//Klassen Autoload & Konfiguration & Funktionen laden
require_once(__DIR__.'/core/conf/conf.php');

//Pfad zum Datei-/Codeverzeichnis
$codefolder = __DIR__.'/files';

//System initialisiert!

//Konfigurator laufengelassen?
if( empty( $allgsysconf['siteurl'] ) ){
	open_url( 'configurator.php' );
}

//URL -> was zu tun?
require_once(__DIR__.'/core/parse_url.php');

//Module first
require_once(__DIR__.'/core/module/include_fe_first.php');

//richtiges machen
if( $parsed == 'download' ){
	require_once(__DIR__.'/core/parts/make_download.php');
}
elseif( $parsed == 'explorer' ){
	require_once(__DIR__.'/core/parts/make_explorer.php');
}
elseif( $parsed == 'info' ){
	require_once(__DIR__.'/core/parts/make_info.php');
}
elseif( $parsed == 'view' ){
	require_once(__DIR__.'/core/parts/make_view.php');
}
else{
	if( !$errormessset ){
		$sitecontent->echo_error( 'Fehlerhafter Zugriff' );
		$errormessset = true;
	}
}

//Module second
require_once(__DIR__.'/core/module/include_fe_second.php');

//Seite erstellen
require_once(__DIR__.'/core/make_site.php');

//Seite ausgeben
$sitecontent->output_complete_site();

?>