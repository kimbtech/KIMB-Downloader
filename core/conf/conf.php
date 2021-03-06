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

//Klassen Autoload
require_once( __DIR__.'/../oop/all_oop.php' );

//Konfiguration lesen
$conffile = new KIMBdbf('config.kimb');
$allgsysconf = $conffile->read_kimb_id('001');

//allgemeine Funktionen usw. laden
require_once(__DIR__.'/funktionen.php');

//session, Fehleranzeige, Robots-Header, Content, Codierung
SYS_INIT( $allgsysconf['robots'] );

//wichtige Objekte erstellen

//Seiteninhat
$sitecontent = new system_output($allgsysconf);

//Info über das CMS dem HTML-Code hinzufügen
$kimbcmsinfo = '<!--

	This site is made with KIMB-Downloader!
	http://www.KIMB-technologies.eu
	https://bitbucket.org/kimbtech/kimb-downloader/

	GNU General Public License v3
	http://www.gnu.org/licenses/gpl-3.0

-->';

$sitecontent->add_html_header($kimbcmsinfo);

?>
