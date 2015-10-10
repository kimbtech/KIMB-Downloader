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
	
	$_GET['module'] = preg_replace( "/[^a-z]/" , "" , $_GET['module'] );
	
	if( is_file( __DIR__.'/../module/'.$_GET['module'].'/be_conf.php' ) ){
		require_once( __DIR__.'/../module/'.$_GET['module'].'/be_conf.php' );
	}
	else{
		$sitecontent->echo_error( 'Das von Ihnen gewünscht Modul wurde nicht gefunden!' );
		$sitecontent->add_site_content( '<br /><a href="'.$allgsysconf['siteurl'].'/backend.php?todo=module">&larr; Zurück</a>' );
	}
}
else{
	$sitecontent->add_site_content( '<h1>Module</h1>' );
	$sitecontent->add_site_content( '<br />');
	$sitecontent->echo_message( 'Um die Einstellungen eines Moduls zu ändern, wählen Sie es bitte im Menü aus.<br />Sie können nur aktivierte Module einstellen.', 'Einstellungen' );
	$sitecontent->add_site_content( '<br />');
	
	if( !empty( $_GET['deakch'] ) ){
		
		$_GET['deakch'] = preg_replace( "/[^a-z]/" , "" , $_GET['deakch'] );
		
		//Modul aktiviert?
		if( in_array( $_GET['deakch'], $modules_todos_list ) ){
			
			//deaktivieren
			
			//alle aktivierten Module durchgehen
			foreach( $downloader_modules as $mod ){
				//nicht das Modul, welches deaktiviert werden soll?
				if( $_GET['deakch'] != $mod['todo'] ){
					$modsneu[] = $mod;
				}
				else{
					$modulename = $mod['name'];
				}
			}
			
			//aktivierte Module verändern (ohne zu deaktivierendes)
			//	Module Liste anpassen
			$downloader_modules = $modsneu;
			//	nur die ToDos in Array (anpassen)
			$modules_todos_list = array_column($downloader_modules, 'todo');
			
			//Medlung
			if( file_put_contents( __DIR__.'/../module/modules_list.json', json_encode( $downloader_modules ) ) ){		
				$sitecontent->echo_message( 'Das Modul "'.$modulename.'" wurde deaktiviert!' );
				$sitecontent->add_site_content( '<br />');
			}

		}
		else{
			//aktivieren
			if( is_file( __DIR__.'/../module/'.$_GET['deakch'].'/info_about.php' ) ){
			
				//Daten des Modul laden
				include(  __DIR__.'/../module/'.$_GET['deakch'].'/info_about.php' );
			
				//Modul Array erstellen
				$downloader_modules[] = array( 'name' => $module_info_name, 'todo' => $module_info_todo, 'icon' => $module_info_icon, 'parts' => $module_info_parts, 'version' => $module_info_version );
				
				//Modul Array speichern
				if( file_put_contents( __DIR__.'/../module/modules_list.json', json_encode( $downloader_modules ) ) ){
					$sitecontent->echo_message( 'Das Modul "'.$module_info_name.'" wurde aktiviert!' );
					$sitecontent->add_site_content( '<br />');
				}
			}
		}
	}
	elseif( !empty( $_GET['del'] ) ){
		
		$_GET['del'] = preg_replace( "/[^a-z]/" , "" , $_GET['del'] );
		
		//Modul vorhanden und nicht aktiviert?
		if( is_dir( __DIR__.'/../module/'.$_GET['del'].'/' ) && !in_array( $_GET['del'], $modules_todos_list ) ){
			
			//Modul Ordner löschen
			if( rm_r( __DIR__.'/../module/'.$_GET['del'].'/' ) ){
				$sitecontent->echo_message( 'Das gewählte Modul wurde gelöscht!' );
				$sitecontent->add_site_content( '<br />');
			}
		}
		
	}
	elseif( isset( $_GET['install'] ) ){
		
		if( install_module( $_FILES["modfile"]["tmp_name"] ) ){
			$sitecontent->echo_message( 'Modul wurde installiert!' );
			$sitecontent->add_site_content( '<br />');
		}
		
	}
	
	//CSS für Tabelle
	$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
	
	$sitecontent->add_site_content( '<h2>Installierte Module</h2>' );
	$sitecontent->add_site_content( '<br />');
	
	$sitecontent->add_site_content( '<table width="100%">' );
	$sitecontent->add_site_content( '<tr><th><u>Name</u></th><th><u>Version</u></th><th><u>Status</u></th><th><u>Löschen</u></th><tr>' );
	
	$sitecontent->add_site_content( '<tr><td colspan="4"><b>Aktivierte Module:</b></td><tr>' );
	
	//alle aktivierten Module (aus todos_list.php lesen)
	foreach( $downloader_modules as $mod ){
		
		$status = '<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=module&amp;deakch='.$mod['todo'].'" title="Modul ist aktiviert! (click &rarr; ändern)"><span class="ui-icon ui-icon-check"></span></a>';
		$del = '<span title="Bitte deaktiveren Sie das Modul, bevor Sie es löschen!" class="ui-icon ui-icon-notice"></span>';
	
		//Tabellenzeile
		$sitecontent->add_site_content( '<tr><td>'.$mod['name'].'</td><td>'.$mod['version'].'</td><td>'.$status.'</td><td>'.$del.'</td><tr>' );
		
		$aktmod[] = $mod['todo'];		
	}
	
	if( !is_array( $aktmod )){
		$sitecontent->add_site_content( '<tr><td></td><td colspan="3"><i>Keine aktivierten Module</i></td><tr>' );
	}
	
	$sitecontent->add_site_content( '<tr><td colspan="4"><b>Deaktivierte Module:</b></td><tr>' );
	
	//noch keine deaktivierten Module vorhaden
	$deakmod = false;
	
	//Module Ordner lesen
	$other_modules = scandir( __DIR__.'/../module/' );
	
	//alle aktivierten Module (aus todos_list.php lesen)
	foreach( $other_modules as $mod ){
		
		if( $mod != '.' && $mod != '..' && !in_array( $mod, $aktmod  ) ){
			
			if( is_file( __DIR__.'/../module/'.$mod.'/info_about.php' ) ){
		
				//Daten des Modul laden
				include(  __DIR__.'/../module/'.$mod.'/info_about.php' );
		
				$status = '<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=module&amp;deakch='.$mod.'" title="Modul ist deaktiviert! (click &rarr; ändern)"><span class="ui-icon ui-icon-close"></span></a>';
				$del = '<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=module&amp;del='.$mod.'" ><span title="Modul löschen!" class="ui-icon ui-icon-trash"></span></a>';
			
				//Tabellenzeile
				$sitecontent->add_site_content( '<tr><td>'.$module_info_name.'</td><td>'.$module_info_version.'</td><td>'.$status.'</td><td>'.$del.'</td><tr>' );
				
				//deaktivierte Module vorhaden
				$deakmod = true;
			}
		}
	}
	
	if( !$deakmod ){
		$sitecontent->add_site_content( '<tr><td></td><td colspan="3"><i>Keine deaktivierten Module</i></td><tr>' );
	}

	
	$sitecontent->add_site_content( '<table>' );
	
	$sitecontent->add_site_content( '<br />');
	$sitecontent->add_site_content( '<br />');
	$sitecontent->add_site_content( '<h2>Modul installieren</h2>' );
	$sitecontent->add_site_content( '<br />');
	$sitecontent->add_site_content( '<form action="'.$allgsysconf['siteurl'].'/backend.php?todo=module&amp;install" enctype="multipart/form-data" method="post">' );
	$sitecontent->add_site_content('<input name="modfile" type="file" /><br />');
	$sitecontent->add_site_content('<input type="submit" value="Installieren" title="Wählen Sie eine Modul Datei (&apos;*.kimbmod&apos;) von Ihrem Rechner zur Installation." />');
	$sitecontent->add_site_content('</form>');

}

?>
