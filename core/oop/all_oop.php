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

//Hier wird das Autoload für alles Klassen registriert.

function autoload_classes( $class ){
	//Alle autoloads in Array
	//	Klassenname => Dateiname (ohne Endung, evtl. mit Pfad)
	$classarray = array(
		'KIMBdbf' => 'kimbdbf',
		'system_output' => 'output',
		'backend_output' => 'output_backend',
		'Markdown' => 'markdown/load',
		'MarkdownExtra' => 'markdown/load'
	);
	
	//laden der gewünschten Klasse
	require_once( __DIR__.'/'.$classarray[$class].'.php' );
}

//Autoload Funktion registrieren
spl_autoload_register('autoload_classes');

?>
