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

//Login prüfen
check_backend_login( true, true );

if( !empty( $_GET['module'] ) ){
	
	$modulenotfound = false;
	
	if( in_array( $_GET['module'], $modules_todos_list ) ){
		if( is_file( __DIR__.'/../module/'.$_GET['module'].'/be_conf.php' ) ){
			require_once( __DIR__.'/../module/'.$_GET['module'].'/be_conf.php' );
		}
		else{
			$modulenotfound = true;
		}
	}
	else{
		$modulenotfound = true;
	}
	
	if( $modulenotfound ){
		$sitecontent->echo_error( 'Das von Ihnen gewünscht Modul wurde nicht gefunden!' );
		$sitecontent->add_site_content( '<br /><a href="'.$allgsysconf['siteurl'].'/backend.php?todo=module">&larr; Zurück</a>' );
	}
}
else{
	$sitecontent->add_site_content( '<h1>Module</h1>' );
	$sitecontent->add_site_content( '<br />');
	$sitecontent->echo_message( 'Um die Einstellungen eines Moduls zu ändern, wählen Sie es bitte im Menü aus.', 'Einstellungen' );
	$sitecontent->add_site_content( '<br />');
	$sitecontent->add_site_content( '<h2>Module installieren</h2>' );
	
	$sitecontent->add_site_content( '<br />');
	$sitecontent->add_site_content( '<br />');
	$sitecontent->add_site_content( 'Aktuell ist keine automatische Installation möglich. Bitte schauen Sie im Wiki/ in der Dokumentation!' );
}


?>
