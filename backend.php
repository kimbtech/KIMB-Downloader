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
require_once(__DIR__.'/core/conf/conf_backend.php');

//System initialisiert!

//Konfigurator laufengelassen?
if( empty( $allgsysconf['siteurl'] ) ){
	open_url( 'configurator.php' );
}

//ToDo gewählt?
if( !empty( $_GET['todo'] ) ){
	
	//gibt es das ToDo?
	if( in_array( $_GET['todo'], $todos_list )){
		//gewolltes ToDo sichern
		$todorequ = $_GET['todo'];
	}
	else{
		//ToDo nicht gefunden -> Meldung
		$sitecontent->echo_error( 'ToDo not found!', 404 );
		$errorset = true;
	}
	
}
else{
	//kein ToDo gewählt -> Login
	$todorequ = 'login';
}

//Ist ein ToDo definiert? und Datei für ToDo vorhanden?
if( !empty( $todorequ ) && is_file( __DIR__.'/core/backend/todo_'.$todorequ.'.php' ) ){

	//Ist ToDo Login nicht gewählt?
	if( $todorequ != 'login' ){
		//man muss eingeloggt sein!
		
		//Login prüfen
		check_backend_login();
		
		//ToDo laden
		require_once( __DIR__.'/core/backend/todo_'.$todorequ.'.php' );
	}
	else{
		//nicht eingeloggt und Login gewählt?
		//	Login laden
		require_once( __DIR__.'/core/backend/todo_login.php' );
	}
}
else{
	if( !$errorset ){
		//Fehler im ToDo 
		$sitecontent->echo_error( 'ToDo Error!', 404 );
	}
}

//Seite ausgeben
$sitecontent->output_complete_site();

?>