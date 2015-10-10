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
	$module_autoupdate_infofile = new KIMBdbf( 'module_autoupdate_infofile.kimb' );
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
	}
	
}
elseif( isset( $_GET['modup'] ) ){
	
}
elseif( isset( $_GET['moddown'] ) ){
	
	$sitecontent->add_site_content('<h2>Module installieren</h2>');
	$sitecontent->add_site_content('Installieren Sie hier mit einem Klick Module auf dem System.<br />');
	
	//CSS für Tabelle
	$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');
	
	$addonsarray = json_decode( $module_autoupdate_infofile->read_kimb_one( 'module_json' ), true );
	
	if( !is_array( $addonsarray ) ){
		$addonsarray = array();
	}
	
	$sitecontent->add_site_content( '<table>' );
	$sitecontent->add_site_content( '<tr> <th>Name</th> </tr>' );
	
	$modstable = false;
	
	foreach( $addonsarray as $ad ){

		if( strpos( $ad['todo'] , ".." ) !== false ){
			
		}
		else{
			if( !is_dir( __DIR__.'/../'.$ad['todo'] ) ){
				$sitecontent->add_site_content( '<tr> <td><a href="https://downloaderwiki.kimb-technologies.eu/module/'.$ad['todo'].'" title="Seite des Add-ons im WIKI" target="_blank">'.$ad['name'].'</a></td> <td><a href="'.$addonurl.'&amp;moddown&amp;install='.$ad['todo'].'">Modul installieren</a></td> </tr>' );
				
				$modstable = true;
			}
		}
	}
	
	$sitecontent->add_site_content( '</table>' );
	
	if( !$modstable ){
		$sitecontent->echo_message( 'Keine Module zur Installation gefunden!' );
	}
	
}
else{
	//nichts gewünscht
	$sitecontent->add_site_content('Bitte wählen Sie oben einen Bereich aus, den Sie angezeigt bekommen wollen.');
}

$sitecontent->add_site_content('<hr /><a href="'.$addonurl.'&amp;reload_api"><span class="ui-icon ui-icon-refresh"  style="display:inline-block;" title="Daten von der API neu laden?"></span></a>');
$sitecontent->add_site_content('Letzte Prüfung: '.date( 'd.m.Y H:i:s ', $module_autoupdate_infofile->read_kimb_one( 'lastcheck' ) ) );

?>
