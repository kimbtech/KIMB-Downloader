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

//Backend Konfiguration & Infos

$addonurl = $allgsysconf['siteurl'].'/backend.php?todo=module&amp;module=autoupdate';

//eigentlich schon von funcclass geladen
if( !is_object( $module_autoupdate_infofile ) ){
	$module_autoupdate_infofile = new KIMBdbf( 'module/module_autoupdate_infofile.kimb' );
}

//Auf der Seite Konfiguration wird oben eine Liste mit den verschiedenen Möglichkeiten anzeigt
//	laden der Buttons
$sitecontent->add_html_header('<script> $(function() { $( "a#dohinw" ).button(); }); </script>');
//	HTML Code
$sitecontent->add_site_content('<center><br />
<a href="'.$addonurl.'&amp;sys" id="dohinw">Downloader Update</a>
<a href="'.$addonurl.'&amp;modup" id="dohinw">Module Update</a>
<a href="'.$addonurl.'&amp;moddown" id="dohinw">Module Installation</a>
</center><br /><hr /><br />');

//HTTP Requests müssen PHP erlaubt sein
if( !ini_get('allow_url_fopen') ) {
	//Fehlermedlung
	$sitecontent->echo_error( 'PHP muss "allow_url_fopen" erlauben!' );
	//unten keine Auswahl erlauben
	unset( $_GET['sys'], $_GET['modup'], $_GET['moddown']);
}
else{
	if( isset( $_GET['reload_api'] ) ){
		api_check_for_updates( false );
	}
}

//Ist der Ordner /temp/ vorhanden?
if( !is_writable( __DIR__.'/temp/' ) || !is_dir( __DIR__.'/temp/' )  ){
	//wenn nicht dann erstellen
	mkdir( __DIR__.'/temp/' );
	//Rechte einstellen
	chmod( __DIR__.'/temp/' , (fileperms( __DIR__ ) & 0777));
}

//Wahl des Aufgabenbereichs erkennen 
if( isset( $_GET['sys'] ) ){
	
	$sitecontent->add_site_content('<h2>Downloader Update</h2>');
	$sitecontent->add_site_content('Führen Sie hier ein Update des Systems durch.<br />');
	
	if( isset( $_GET['do'] ) ){
		$sitecontent->add_site_content('<p style="color:blue">Update auf Version '.$module_autoupdate_infofile->read_kimb_one( 'sys_newversion' ).'</p>');
		
		require_once( __DIR__.'/do_update.php' );
	}
	else{
		
		if( $module_autoupdate_infofile->read_kimb_one( 'sys_update' ) == 'yes' ){
			$updlogo = '<span class="ui-icon ui-icon-circle-check" title="Update verfügbar!"></span>';
		}
		else{
			$updlogo = '<span class="ui-icon ui-icon-circle-close" title="Update nicht verfügbar!"></span>';
		}
		
		$sitecontent->add_site_content('<br /><br /><table>');
		$sitecontent->add_site_content('<tr> <td>Update</td> <td>'.$updlogo.'</td> </tr>');
		$sitecontent->add_site_content('<tr> <td>Systemversion</td> <td>'.$allgsysconf['build'].'</td> </tr>');
		$sitecontent->add_site_content('<tr> <td>Aktuelle Version</td> <td>'.$module_autoupdate_infofile->read_kimb_one( 'sys_newversion' ).'</td> </tr>');
		$sitecontent->add_site_content('</table><br /><br />');
		
		if( $module_autoupdate_infofile->read_kimb_one( 'sys_update' ) == 'yes' ){
			$sitecontent->add_site_content('<a href="'.$addonurl.'&amp;sys&amp;do"><button>Update durchführen</button></a>');
		}
		else{
			$sitecontent->add_site_content('<button disabled="disabled">Kein Update</button>');
		}
		
		//alte Update Dateien aus dem Temp-Ordner löschen
		foreach( scandir( __DIR__.'/temp/' ) as $zip ){
			if( $zip != '.' && $zip != '..'){
				unlink( __DIR__.'/temp/'.$zip );
			}
		}
	}
	
}
elseif( isset( $_GET['modup'] ) ){
	
	$sitecontent->add_site_content('<h2>Module aktualisieren</h2>');
	$sitecontent->add_site_content('Führen Sie hier Updates Ihrer Module mit einem Klick durch.<br />');
	
	if( !empty( $_GET['install'] ) ){
		$_GET['install'] = preg_replace( "/[^a-z]/" , "" , $_GET['install'] );
		
		if( get_and_install_module( $_GET['install'] ) ){
			$sitecontent->echo_message( 'Modul wurde installiert!' );
			$sitecontent->add_site_content('<br /><br /><a href="'.$addonurl.'&amp;modup"><span class="ui-icon ui-icon-arrowreturn-1-w"></span> Zurück</a>');
		}
		else{
			$sitecontent->add_site_content('<br /><br /><a href="'.$addonurl.'&amp;modup&amp;install='.$_GET['install'].'"><span class="ui-icon ui-icon-arrowrefresh-1-e"></span> Erneut versuchen</a>');
			$sitecontent->add_site_content('<br /><br /><a href="'.$addonurl.'&amp;modup"><span class="ui-icon ui-icon-arrowreturn-1-w"></span> Zurück</a>');
		}
	}
	else{
		//CSS für Tabelle
		$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
		
		$sitecontent->add_site_content( '<table width="100%">' );
		$sitecontent->add_site_content( '<tr> <th>Name</th> <th>Installierte Version</th> <th>Aktuelle Version</th> </tr>' );
		
		//Module auf System lesen
		$mods = scandir( __DIR__.'/../' );
		$modstable = false;
		
		//API Daten laden
		$addonsarray = json_decode( $module_autoupdate_infofile->read_kimb_one( 'module_json' ), true );
		//wenn keine Module in API, dann Array leer
		if( !is_array( $addonsarray ) ){
			$addonsarray = array();
		}
		//nur die ToDos in Array
		$api_todos = array_column($addonsarray, 'modul');
		
		//alle durchgehen
		foreach( $mods as $mod ){
			//keine Dateisystemzeichen
			if( $mod != '.' && $mod != '..' ){
				//nur Module nehmen
				if( is_file( __DIR__.'/../'.$mod.'/info_about.php' ) ){
					
					//Modul Infos laden
					require_once( __DIR__.'/../'.$mod.'/info_about.php' );
					
					//Infos aus API suchen
					$api_i = array_search( $mod, $api_todos );
					
					//beide Versionen da und Array Key nicht false?
					if( !empty( $module_info_version ) && !empty( $addonsarray[$api_i]['version'] ) && $api_i !== false ){
						
						//Version des installierten älter als Version in der API?
						if( compare_cms_vers( $module_info_version, $addonsarray[$api_i]['version'] ) == 'older'  ){
					
							//Tabellenzeile mit Button für Update
							$sitecontent->add_site_content( '<tr>
								<td>'.$addonsarray[$api_i]['name'].'</td>
								<td>'.$module_info_version.'</td>
								<td>'.$addonsarray[$api_i]['version'].'</td>
								<td><a href="'.$addonurl.'&amp;modup&amp;install='.$addonsarray[$api_i]['modul'].'">Update</a></td>
							</tr>' );
					
							$modstable = true;
							
						}
					}
				}
			}
		}
		
		//Tabelle beenden
		$sitecontent->add_site_content( '</table>' );
		
		if( !$modstable ){
			$sitecontent->echo_message( 'Alle Module sind aktuell!', 'Glückwunsch' );
		}
		
		//alte Update Dateien aus dem Temp-Ordner löschen
		foreach( scandir( __DIR__.'/temp/' ) as $zip ){
			if( $zip != '.' && $zip != '..'){
				unlink( __DIR__.'/temp/'.$zip );
			}
		}
	}
	
}
elseif( isset( $_GET['moddown'] ) ){
	
	$sitecontent->add_site_content('<h2>Module installieren</h2>');
	$sitecontent->add_site_content('Installieren Sie hier mit einem Klick Module auf dem System.<br />');
	
	if( !empty( $_GET['install'] ) ){
		$_GET['install'] = preg_replace( "/[^a-z]/" , "" , $_GET['install'] );
		
		if( get_and_install_module( $_GET['install'] ) ){
			$sitecontent->echo_message( 'Modul wurde installiert!' );
			$sitecontent->add_site_content('<br /><br /><a href="'.$addonurl.'&amp;moddown"><span class="ui-icon ui-icon-arrowreturn-1-w"></span> Zurück</a>');
		}
		else{
			$sitecontent->add_site_content('<br /><br /><a href="'.$addonurl.'&amp;moddown&amp;install='.$_GET['install'].'"><span class="ui-icon ui-icon-arrowrefresh-1-e"></span> Erneut versuchen</a>');
			$sitecontent->add_site_content('<br /><br /><a href="'.$addonurl.'&amp;moddown"><span class="ui-icon ui-icon-arrowreturn-1-w"></span> Zurück</a>');
		}
	}
	else{
		
		//CSS für Tabelle
		$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
		
		$addonsarray = json_decode( $module_autoupdate_infofile->read_kimb_one( 'module_json' ), true );
		
		if( !is_array( $addonsarray ) ){
			$addonsarray = array();
		}
		
		$sitecontent->add_site_content( '<table width="100%">' );
		$sitecontent->add_site_content( '<tr> <th>Name</th> </tr>' );
		
		$modstable = false;
		
		foreach( $addonsarray as $ad ){
	
			if( strpos( $ad['modul'] , ".." ) !== false ){
			}
			else{
				if( !is_dir( __DIR__.'/../'.$ad['modul'] ) ){
					$sitecontent->add_site_content( '<tr> <td><a href="https://downloaderwiki.kimb-technologies.eu/module/'.$ad['modul'].'" title="Seite des Add-ons im WIKI" target="_blank">'.$ad['name'].'</a></td> <td><a href="'.$addonurl.'&amp;moddown&amp;install='.$ad['modul'].'">Modul installieren</a></td> </tr>' );
					
					$modstable = true;
				}
			}
		}
		
		$sitecontent->add_site_content( '</table>' );
		
		if( !$modstable ){
			$sitecontent->echo_message( 'Keine Module zur Installation gefunden!' );
		}
		
		//alte Update Dateien aus dem Temp-Ordner löschen
		foreach( scandir( __DIR__.'/temp/' ) as $zip ){
			if( $zip != '.' && $zip != '..'){
				unlink( __DIR__.'/temp/'.$zip );
			}
		}
	}
	
}
else{
	//nichts gewünscht
	$sitecontent->add_site_content('Bitte wählen Sie oben einen Bereich aus, den Sie angezeigt bekommen wollen.');
	
	//alte Update Dateien aus dem Temp-Ordner löschen
	foreach( scandir( __DIR__.'/temp/' ) as $zip ){
		if( $zip != '.' && $zip != '..'){
			unlink( __DIR__.'/temp/'.$zip );
		}
	}
}

$sitecontent->add_site_content('<hr /><a href="'.$addonurl.'&amp;reload_api"><span class="ui-icon ui-icon-refresh"  style="display:inline-block;" title="Daten von der API neu laden?"></span></a>');
$sitecontent->add_site_content('Letzte Prüfung: '.date( 'd.m.Y H:i:s ', $module_autoupdate_infofile->read_kimb_one( 'lastcheck' ) ) );

?>
