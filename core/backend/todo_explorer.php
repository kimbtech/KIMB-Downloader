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

$sitecontent->add_site_content( '<h1>Explorer</h1>' );

//Login prüfen
check_backend_login( true, false );

$sitecontent->add_site_content('<div id="explorerarea">');

//Einstellungen dafür laden
//	gesichert
$secured = 'off';
//	Pfad zum Ordner
$grpath = __DIR__.'/../../files/';
//	dem User ein "Protokoll" anzeigen (soll wissen in welchem Ordner)
$vorneprot = 'files://';

//Das Dateisystem sichern, mit "./../"" könnte man sonst in tiefere Ordner gelangen 
if(  (strpos($_GET['path'], "..") !== false) || (strpos($_GET['del'], "..") !== false) || (strpos($_POST['newfolder'], "..") !== false) ){
	echo ('Do not hack me!!');
	die;
}

//neuen Ordner erstellen
if( !empty( $_POST['newfolder'] ) ){
	//Ordner sollte noch nicht existieren
	if(!is_dir( $grpath.$_GET['path'].'/'.$_POST['newfolder'].'/' ) ){
		//Ordner erstellen
		mkdir( $grpath.$_GET['path'].'/'.$_POST['newfolder'].'/' );
		//Rechte des Ordners auf die des Grundverzeichnisses setzen
		chmod( $grpath.$_GET['path'].'/'.$_POST['newfolder'].'/' , (fileperms( $grpath ) & 0777));
		//Medlung
		$sitecontent->echo_message( 'Ordner erstellt' );
	}
}
//etws löschen?
if( !empty( $_GET['del'] ) ){
	//einen Ordner?
	if($_GET['art'] == 'folder'){
		//Alles in dem Ordner, und ihn auch, löschen
		rm_r( $grpath.$_GET['del'].'/', true, $_GET['del'] );
		
		//Meldung
		$sitecontent->echo_message( 'Ordner gelöscht' );
	}	
	//nur eine Datei?
	else{
		//löschen
		unlink( $grpath.$_GET['del'] );
		
		//Meldung
		$sitecontent->echo_message( 'Datei gelöscht' );
	}
}

//neue Dateien hochladen?
if ( !empty( $_FILES['file']['name'] ) ){

	//Dateien werden per Dropzone.js geliefert
	
	//erstmal den Dateinamen aufbereiten
	//	Umlaute, Leerzeichen usw. sind böse, sollten aber auch nicht einfach verschwinden (das verwirrt so machen User)
	$finame = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '/', '<', '>', '|', '?', ':', '|', '*'),array('ae','oe','ue','ss','Ae','Oe','Ue', '_', '', '', '', '', '', '', '', ''), $_FILES['file']['name'] );
	//	alles was jetzt nicht passt muss dann aber weg 
	$finame = preg_replace( "/[^A-Za-z0-9_.-]/" , "" , $finame );
	//	wir wollen wirklich nur den Dateinamen
	$finame = basename($finame);
	//	nur den Dateinamen sauber halten für später
	$filena = $finame;

	//gibt es diesen Dateinamen schon im gewählten Verzeichnis?
	if(file_exists($grpath.$_GET['path'].'/'.$finame)){
		//also ja
			
		//vorne vor den Namen eine Zahl setzen
		//	los geht's mit der 1
		$ii = '1';
		//das wäre der neue Name mit absolutem Pfad
		$fileneu = $grpath.$_GET['path'].'/'.$finame;
		//solange eine Datei existiert weiter einen Namen suchen
		while(file_exists($fileneu)){
			//wieder ein neuer absoluter Pfad zum Testen
			$fileneu = $grpath.$_GET['path'].'/'.$ii.$finame;
			//und ein neuer Pfad relativ zum gewählten Ordner (secured, offen)
			$filedd = $_GET['path'].'/'.$ii.$finame;
			//den Index erhöhen
			$ii++;
		}
		//freier Dateiname gefunden :-)
		$finame = $fileneu;
	}
	//den Dateinamen gibt es nicht
	else{
		//Pfade erstellen (absolut und relativ zum gewählten Ordner (secured, offen))
		$filedd = $_GET['path'].'/'.$finame;
		$finame = $grpath.$_GET['path'].'/'.$finame;
	}

	//versuchen die Datei an ihren neuen Platz zu kopieren
	if(move_uploaded_file($_FILES["file"]["tmp_name"] , $finame)){
		//wenn okay Meldung
		echo 'Upload erfolgreich';
	}
	else{
		//wenn fehlerhaft aber auch Meldung
		echo 'Upload fehlerhaft!';
	}
	
	die;
}

//Variablen zum Lesen des Verzeichnisses feststellen
//	absoluter Pfad 
$openpath=$grpath.$_GET['path']."/";
//	Pfad relativ
$pathnow=$_GET['path'];
//	einen Ordner zurück
$hochpath = substr($_GET['path'], '0', strlen($_GET['path']) - strlen(strrchr($_GET['path'], '/')));

//JavaScript Code für den Löschen Dialog
$sitecontent->add_html_header('<script>
function delfile( art , del , path, name ) {
	if( art == "folder" ){
			$( "#filemanagerdel" ).html( "<p><span class=\'ui-icon ui-icon-alert\' style=\'float:left; margin:0 7px 20px 0;\'></span>Möchten Sie den Ordner &apos;" + name + "&apos; wirklich löschen?</p>" );
	}
	else{
		$( "#filemanagerdel" ).html( "<p><span class=\'ui-icon ui-icon-alert\' style=\'float:left; margin:0 7px 20px 0;\'></span>Möchten Sie die Datei &apos;" + name + "&apos; wirklich löschen?</p>" );
	}
	$( "#filemanagerdel" ).css( "display", "block" );
	$( "#filemanagerdel" ).dialog({
	resizable: false,
	height:200,
	modal: true,
	buttons: {
		"Löschen": function() {
			$( this ).dialog( "close" );
			window.location = "'.$allgsysconf['siteurl'].'/backend.php?todo=explorer&del=" + del + "&art=" + art + "&path=" + path ;
			return true;
		},
		"Abbrechen": function() {
			$( this ).dialog( "close" );
			return false;
		}
	}
	});
}
</script>');

//Der zu öffnenden Pfad sollte vorhanden sein!
if( is_dir( $openpath ) ){
	//Text des Löschen Dialoges
	$sitecontent->add_site_content('<div style="display:none;"><div id="filemanagerdel" title="Löschen?">Löschen?</div></div>');

	//Zurück/ Hoch Button
	$sitecontent->add_site_content ('<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=explorer&amp;path='.urlencode($hochpath).'"><button style="padding:2px; border:2px solid red; border-radius:5px; background-color:#ff4c4c;" title="&lArr; In den darunter liegenden Ordner wechseln." ><span class="ui-icon ui-icon-arrowthick-1-w" style="display:inline-block;" ></span></button></a>');
	
	//Edit Readme
	$sitecontent->add_site_content ('<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=infos&amp;readme&amp;path='.urlencode( $pathnow ).'"><button style="padding:2px; border:2px solid #b2b200; border-radius:5px; background-color:#ff0;" title="Infoseite des aktuellen Ordners bearbeiten." ><span style="display:inline-block;" class="ui-icon ui-icon-pencil"></span> Infoseite</button></a><br />');
	
	$sitecontent->add_site_content ('<hr />');

	//Erstellung der Leiste oben
	//	erstmal noch alles da
	$restpath = $pathnow;
	//	wir beginnen hinten
	//		den aktuellen Ordner anfügen
	//			Pfad
	$a['url'] = $allgsysconf['siteurl'].'/backend.php?todo=explorer&amp;path='.urlencode($restpath);
	//			Name
	$a['name'] = substr($restpath, - strlen(strrchr($restpath, '/')) );
	//	Daten im Array speichern
	$seepatha[] = $a;
	
	//in einer Schleife solange die Leiste erstellen, bis kein Ordner im Pfad mehr vorhanden 
	while( strpos( $restpath , '/' ) !== false ){
		//dem vorhandenen Pfad den hintersten Ordner abschneiden (dieser ist schon in der Leiste)
		$restpath = substr($restpath, '0', strlen($restpath) - strlen(strrchr($restpath, '/')));
		//den Namen des neuen Ordners feststellen
		$name = substr($restpath, - strlen(strrchr($restpath, '/')) );
		//Name und URL ins erste Array
		$a['url'] = $allgsysconf['siteurl'].'/backend.php?todo=explorer&amp;path='.urlencode($restpath);
		$a['name'] = $name;
		//beides ins zweite Array (von vor der Schleife)
		$seepatha[] = $a;
	}
	//wir hatten hinten begonnen, jetzt müssen wir das "zweite" Array umdrehen
	$seepatha = array_reverse( $seepatha );

	//erstmal Leiste leer
	$seepath = '';
	//das "zweite" Array durchgehen
	foreach( $seepatha as $ar ){
		if( empty( $ar['name'] ) ){
			//namenlos ist nur das Grundverzeichnis
			$ar['name'] = '/';
		}
		//die Leiste nach und nach füllen
		$seepath .= '&nbsp;&nbsp;&nbsp;<a href="'.$ar['url'].'">'.$ar['name'].'</a>';
	}

	//Die Leiste in rot ausgeben
	$sitecontent->add_site_content ('<div style="margin: 5px 0; padding: 5px;  border:2px solid red; border-radius:5px; background-color:#ff4c4c;" title="Aktueller Pfad: Klicken Sie auf einen Ordner um dorthin zu gehen!" >'.$vorneprot.$seepath.'</div>');

	//eine Tabelle mit für die Ordner und Dateien des Verzeichnissen beginnen
	$sitecontent->add_site_content('<table width="100%">');

	//Verzeichnis Dateien lesen
	$allfiles = scandir( $openpath );

	//nach ABC sortieren
	sort( $allfiles );
	
	//Texte zu den Dateien lesen
	$folderfile = new KIMBdbf( 'title/folderlist.kimb' );
	if( !empty($pathnow) ){
		$pathnowlooktitle = $pathnow;
	}
	else{
		$pathnowlooktitle = '/';
	}
	$fileid = $folderfile->search_kimb_xxxid( $pathnowlooktitle, 'path' );
	
	if( $fileid != false ){
		$titlefile = new KIMBdbf( 'title/folder_'.$fileid.'.kimb' );
	}

	//Verzeichnis auslesen und durchgehen
	foreach( $allfiles as $file ){
		//keine Punkte
		if ($file != "." && $file != ".." ) {
			
			//Titel bestimmen
			
			//	Link zum Anpassen
			$filetitle = '<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=infos&amp;title&amp;path='.urlencode( $pathnow ).'"><span style="display:inline-block;" class="ui-icon ui-icon-pencil"></span></a>';
			
			//	Titel der Datei
			if( isset( $titlefile) ){
				$search = $titlefile->search_kimb_xxxid( $file, 'name' );
				if( $search != false ){
					$title = htmlentities( $titlefile->read_kimb_id( $search, 'title' ), ENT_COMPAT | ENT_HTML401,'UTF-8' );
					$filetitle .= substr( $title, 0, 100 );
				}
				else{
					$filetitle .= '<i>Kein Titel</i>';
				}
			}
			else{
				$filetitle .= '<i>Kein Titel</i>';
			}
			
			//ist es bei diesem Durchgang ein Ordner?
			if(is_dir($openpath.$file)){
				//Tabellenzeile für Ordnern erstellen
				//	Icon und Orange
				$table_dirs .= '<tr style="padding:10px; background-color:lightgreen; height: 40px;"><td width="38px"><span class="ui-icon ui-icon-folder-collapsed"></span></td>'."\r\n";
				//	Löschen Button
				$table_dirs .= '<td width="93px"><span onclick="delfile( \'folder\' , \''.urlencode($pathnow.'/'.$file).'\' , \''.urlencode($pathnow).'\', \''.$file.'\' );" class="ui-icon ui-icon-trash" title="Diesen Ordner löschen. (Achtung, es werden alle Dateien im Ordner gelöscht!)" style="display:inline-block;" ></span></td>'."\r\n";
				
				//	Link um Ordner zu öffnen
				$table_dirs .= '<td width="150px"><a href="'.$allgsysconf['siteurl'].'/backend.php?todo=explorer&amp;path='.urlencode($pathnow.'/'.$file).'">'.$file.'</a></td>'."\r\n";
				
				//	Text zum Ordner
				$table_dirs .= '<td>'.$filetitle.'</td>'."\r\n";
				
				$table_dirs .= '</tr>'."\r\n";
			}
			//oder eine Datei
			else{ 
				//Tabellenzeile für Datei erstellen
				//	Icon und Grau
				$table_dats .= '<tr style="background-color: grey; padding:10px; height: 40px;"><td width="38px"><span class="ui-icon ui-icon-document"></span></td>'."\r\n";
				
				//Link zur Datei erstellen
				if( $allgsysconf['urlrewrite'] == 'on' ){
					$fileviewurl = $allgsysconf['siteurl'].'/view'.$pathnow.'/'.$file;
				}
				else{
					$fileviewurl = $allgsysconf['siteurl'].'/?pfad='.urlencode( 'view'.$pathnow.'/'.$file  );
				}
				
				//	Löschen Button
				//	Link zur Datei anzeigen
				$table_dats .= '<td width="93px"><span onclick="delfile( \'\' , \''.urlencode($pathnow.'/'.$file).'\' , \''.urlencode($pathnow).'\', \''.$file.'\' ); " class="ui-icon ui-icon-trash" title="Diese Datei löschen." style="display:inline-block;" ></span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$fileviewurl.'" target="_blank"><span class="ui-icon ui-icon-extlink" title="Öffnet die Datei im Frontend des Downloaders." style="display:inline-block;" ></span></a></td>'."\r\n";
				
				//Dateiname
				$table_dats .= '<td width="150px">'.$file.'</td>'."\r\n";
				
				//	Text zur Datei anzeigen
				$table_dats .= '<td>'.$filetitle.'</td>'."\r\n";
				
				$table_dats .= '</tr>'."\r\n";
			}
		}
	}
	
	//Ordner anfügen (sollen immer oben sein)
	$sitecontent->add_site_content( $table_dirs );
	//Dateien anfügen
	$sitecontent->add_site_content( $table_dats );
	//Tabelle beenden
	$sitecontent->add_site_content('</table>');

	//Formular für neuen Ordner
	$sitecontent->add_site_content ('<form style="padding:10px; background-color:#ffc966; border:2px solid orange;  border-radius:5px; margin-top:5px;" action="'.$allgsysconf['siteurl'].'/backend.php?todo=explorer&amp;path='.urlencode($pathnow).'" method="post">');
	$sitecontent->add_site_content ('<input type="text" name="newfolder" placeholder="Neuer Ordner">');
	$sitecontent->add_site_content ('<input type="submit" value="Erstellen" title="Erstellen Sie einen neuen Ordner."></form>');
	
	$sitecontent->add_site_content('</div>');
	
	$sitecontent->add_html_header( '	<script>
		Dropzone.autoDiscover = false;
		$(function() {
			var uploadblock = "notdone";
  			var ExplorerDropzone = new Dropzone("#dropzone");
  			ExplorerDropzone.on( "addedfile", function(file) {
				if( uploadblock != "done" ){
	    				$( "div#explorerarea" ).hide( "blind", { direction: "up" }, "slow", function() {
					 	   $( "div#explorerarea" ).html( \'<div class="ui-widget" style="position: relative;"><div class="ui-state-highlight ui-corner-all" style="padding:10px;"><span class="ui-icon ui-icon-info" style="position:absolute; left:20px;top:7px;"></span><h1>Uploadvorgang</h1>Bitte warten Sie bis der Uploadvorgang abgeschlossen ist, dann wird der Explorer wieder angezeigt!</div></div>\' );
						   $( "div#explorerarea" ).show();
					 });
					 uploadblock = "done";
				}
			});
			ExplorerDropzone.on( "queuecomplete", function(file){
				window.open( "'.$allgsysconf['siteurl'].'/backend.php?todo=explorer&path='.urlencode($pathnow).'", "_self" );
			});
		});
	</script>' );
	
	$sitecontent->add_site_content( '<br /><br /><h2>Dateien hochladen</h2>' );
	$sitecontent->add_site_content( '<br /><form action="'.$allgsysconf['siteurl'].'/backend.php?todo=explorer&amp;path='.urlencode($pathnow).'" class="dropzone" id="dropzone">');
	$sitecontent->add_site_content( '<img src="'.$allgsysconf['siteurl'].'/load/dropzone/upload.png" style="margin-left:  230px;" title="Ziehen Sie zum Hochladen Dateien über dieses Feld oder klicken Sie!" class="dz-message">');
	$sitecontent->add_site_content( '</form><br />');

}
else{

	//Fehlermedlung wenn Datei nicht gefunden
	$sitecontent->echo_error( 'Das von Ihnen gewählte Verzeichnis wurde nicht gefunden!' , 'unknown', 'Datei nicht gefunden' );
	$sitecontent->add_site_content( '<br /><br /><a href="'.$allgsysconf['siteurl'].'/backend.php?todo=explorer">Zurück</a><br /><br /><br />');

}

?>
