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

$sitecontent->add_site_content( '<h1>Themes</h1>' );

//Login prüfen
check_backend_login( true, true );

//CSS für Tabelle
$sitecontent->add_html_header('<style>td { border:1px solid #000000; padding:2px;} td a { text-decoration:none; }</style>');

//neues Theme hochgeladen?
if( isset( $_FILES['userfile']['name'] ) ){

	//Datei entzippen
	$zip = new ZipArchive;
	if ($zip->open($_FILES["userfile"]["tmp_name"]) === TRUE) {
		//alle Dateien in den load Ordner der Themes entpacken
		$zip->extractTo( __DIR__.'/../../load/system/theme/' );
		$zip->close();
	}
	else{
		//Fehlermeldung wenn nicht möglich Datei zu öffnen
		$sitecontent->echo_error( 'Die Installation schlug fehl!<br />Die Installationsdatei lässt sich nicht öffnen.' , 'unknown');
		$sitecontent->output_complete_site();
		die;
	}

	//den Namen des Themes herausfinden
	$name = file_get_contents( __DIR__.'/../../load/system/theme/name.info' );
	$name = preg_replace("/[^a-z_]/","", $name);
	//die Datei mit dem Namen löschen
	unlink( __DIR__.'/../../load/system/theme/name.info' );

	//sind die für Themes nötigen Dateien in der Installationdatei gewesen?
	if( file_exists( __DIR__.'/../../load/system/theme/output_menue_'.$name.'.php' ) && file_exists( __DIR__.'/../../load/system/theme/output_site_'.$name.'.php' ) ){
		//beide PHP Dateien an den richtigen Platz verschieben
		rename ( __DIR__.'/../../load/system/theme/output_menue_'.$name.'.php' , __DIR__.'/../theme/output_menue_'.$name.'.php' );
		rename ( __DIR__.'/../../load/system/theme/output_site_'.$name.'.php' , __DIR__.'/../theme/output_site_'.$name.'.php' );

		//Meldung
		$sitecontent->echo_message( 'Das Theme "'.$name.'" wurde installiert!' );
	}
	else{
		//Medlung wenn die Dateien fehlen
		$sitecontent->echo_error( 'Die Installation schlug fehl!<br />Das Theme scheint fehlerhaft.' , 'unknown');
	}
}
//soll ein Theme gelöscht werden?
if( isset( $_GET['del'] ) ){
	//Übergabe mit Namen säubern
	$_GET['del'] = preg_replace( "/[^a-z_]/" , "" , $_GET['del']);	

	//wenn PHP-Datei vorhanden diese einfach löschen
	//	Menüdatei
	if( file_exists( __DIR__.'/../theme/output_menue_'.$_GET['del'].'.php' ) ){
		unlink( __DIR__.'/../theme/output_menue_'.$_GET['del'].'.php' );
	}
	//	Seitendatei
	if( file_exists( __DIR__.'/../theme/output_site_'.$_GET['del'].'.php' ) ){
		unlink( __DIR__.'/../theme/output_site_'.$_GET['del'].'.php' );
	}
	//Die Dateien im Ordern load bleiben, sie werden bei einer Neuinstallation überschrieben

	//Medlung
	$sitecontent->echo_message( 'Das Theme "'.$_GET['del'].'" wurde gelöscht!' );
}
//das aktivierte Theme ändern
if( isset( $_GET['chdeak'] ) && isset( $_GET['theme'] ) ){
	
	//evtl. dbf laden
	if( !is_object( $conffile ) ){
		$conffile = new KIMBdbf('config.kimb');
	}
	
	//Übergabe mit Namen säubern
	$_GET['theme'] = preg_replace( "/[^a-z_]/" , "" , $_GET['theme'] );
	//der Name des aktuell aktivierten Themes wird in der allgsysconf gespeichert
	//	Änderung des Wertes
	if( $conffile->write_kimb_id( '001' , 'add' , 'theme' , $_GET['theme'] ) ){
		//Medlung
		$sitecontent->echo_message( 'Das Theme "'.$_GET['theme'].'" wurde aktiviert!' );
		//allgsysconf neu laden
		$allgsysconf = $conffile->read_kimb_id('001');
	}
}

//JavaScript (jQueryUI) für Löschen Dialog
$sitecontent->add_html_header('<script>
function deltheme ( theme ) {
	$( "#deltheme" ).show( "fast" );
	$( "#deltheme" ).dialog({
	resizable: false,
	height:200,
	modal: true,
	buttons: {
		"Delete": function() {
			$( this ).dialog( "close" );
			window.location = "'.$allgsysconf['siteurl'].'/backend.php?todo=themes&del=" + theme;
			return true;
		},
		Cancel: function() {
			$( this ).dialog( "close" );
			return false;
		}
	}
	});
}
</script>');

//Tabelle mit Themes beginnen
$sitecontent->add_site_content('<h2>Themesliste</h2>');
$sitecontent->add_site_content('<span class="ui-icon ui-icon-info" title="Achtung: Themes können sich gegenseitig beeinflussen, es müssen also nicht alle unten aufgeführten Themes funktionstüchtig sein, bitte installieren Sie ein Theme erneut, sollte es Probleme gibt!"></span>');
$sitecontent->add_site_content('<table width="100%"><tr> <th>Code</th> <th>Status</th> <th>Löschen</th> </tr>');

//das Verzeichnis mit den PHP Dateien der Themes lesen
$dir = scandir( __DIR__.'/../theme/' );

//alle Dateien durchgehen
foreach( $dir as $file ){
	//.. und . überspringen
	if( $file != '.' && $file != '..' ){
		//nur die Seitendateien anschauen
		if( strpos( $file , "site" ) == 7 ){
			//den Namen des Themes aus dem Dateinamen extrahieren
			$teil = substr( $file , 12 , -4 );

			//bei allen Themes eine Löschen Button anfügen, nur beim Standardthema nicht
			if( $teil != 'norm' ){
				$del = '<span onclick="deltheme( \''.$teil.'\' ); "><span class="ui-icon ui-icon-trash" title="Dieses Theme löschen." style="display:inline-block;" ></span></span>';
			}
			else{
				$del = '';
			}

			//aktuellen Status des Themes herausfinden
			if ( $allgsysconf['theme'] == $teil ){
				//aktiviert
				$status = '<span class="ui-icon ui-icon-check" title="Dieses Theme ist zu Zeit aktiviert. (Bitte aktivieren Sie ein anderes, um dies zu ändern.)" style="display:inline-block;" ></span>';
			}
			elseif( !isset( $allgsysconf['theme'] ) && $teil == 'norm' ){
				//auch aktiviert (Standardthema wird geladen, wenn keins aktiviert)
				$status = '<span class="ui-icon ui-icon-check" title="Dieses Theme ist zu Zeit aktiviert.  Bitte aktivieren Sie ein anderes, um dies zu ändern.)" style="display:inline-block;" ></span>';
			}
			else{
				//deaktiviert
				//	Link um dieses Theme zu aktivieren
				$status = '<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=themes&amp;theme='.$teil.'&amp;chdeak"><span class="ui-icon ui-icon-close" title="Dieses Theme ist zu Zeit deaktiviert. (click -> aktivieren)" style="display:inline-block;" ></span></a>';
			}

			//Tabellenzeile hinzufügen
			$sitecontent->add_site_content('<tr> <td>'.$teil.'</td> <td>'.$status.'</td> <td>'.$del.'</td> </tr>');
		}
	}
}

$sitecontent->add_site_content('</table>');

//HTML Code für Löschen Dialog
$sitecontent->add_site_content('<div style="display:none;"><div id="deltheme" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie dieses Theme wirklich löschen?</p></div></div>');

//HTML-Form um neues Theme hochzuladen/ zu installieren
$sitecontent->add_site_content('<br /><br /><h2>Theme installieren</h2>');
$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/backend.php?todo=themes" enctype="multipart/form-data" method="post">');
$sitecontent->add_site_content('<input name="userfile" type="file" /><br />');
$sitecontent->add_site_content('<input type="submit" value="Installieren" title="Wählen Sie eine Theme Datei (&apos;*.kimbthe&apos;) von Ihrem Rechner zur Installation." />');
$sitecontent->add_site_content('</form>');

$sitecontent->add_site_content('<br />');
$sitecontent->add_site_content('Es werden die Themes des KIMB-CMS unterstützt!!');

?>
