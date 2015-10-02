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

$sitecontent->add_site_content( '<h1>Infos</h1>' );

//Login prüfen
check_backend_login( true, false );

//Pfad zu den Dateien
$grpath = __DIR__.'/../../files';

//Das Dateisystem sichern, mit "./../"" könnte man sonst in tiefere Ordner gelangen 
if(  (strpos($_GET['path'], "..") !== false) ){
	echo ('Do not hack me!!');
	die;
}

//Pfad leer, aber gegeben?
if( empty( $_GET['path'] ) && isset( $_GET['path'] ) ){
	//auf root setzen
	$_GET['path'] = '/';
}	

//für später in var
$pathnow = $_GET['path'];
//auf dem Dateisystem feststellen

//nur ein / am Anfang
while( substr( $pathnow, 0, 1) == '/' ){
	$pathnow = substr( $pathnow, 1 );		
}

//erste Stelle ohne /, also hinzufügen
$pathnow = '/'.$pathnow;
$openpath = $grpath.$pathnow;

//Link zum Explorer
if( $pathnow == '/' ){
	$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=explorer&amp;path=">&larr; Zurück zum Explorer</a>' );
}
else{
	$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=explorer&amp;path='.urlencode( $pathnow ).'">&larr; Zurück zum Explorer</a>' );
}

//Aufruf ohne Ordnervorgabe und Aufgabe
if( !isset( $_GET['path'] ) ){
	//Weiterleiten zu Infoseite für Root
	open_url( '/backend.php?todo=infos&readme&path=%2F' );
	//beenden
	die;
}
elseif( isset( $_GET['readme'] ) ){
	
	$sitecontent->add_site_content( '<h2>Infoseite anpassen</h2>' );

	//Löschen
	if( isset( $_GET['del'] ) ){
		delete_readme( $pathnow );
	}

	//Readmes Liste lesen
	$folderfile = new KIMBdbf( 'readme/folderlist.kimb' );
	$pathinfo = $pathnow;
	
	//neues readme erzeugen
	if( isset( $_GET['new'] ) ){
		$newid = $folderfile->next_kimb_id();
		$folderfile->write_kimb_id( $newid, 'add', 'path', $pathnow );
	}
	
	//Pfad suchen
	$fileid = $folderfile->search_kimb_xxxid( $pathinfo, 'path' );
	
	if( $fileid == false ){
		$sitecontent->echo_error( 'Für den von Ihnen gewählten Ordner existiert keine Infoseite!<br /><a href="'.$allgsysconf['siteurl'].'/backend.php?todo=infos&amp;readme&amp;path='.urlencode( $pathnow ).'&amp;new"><button>Für den Ordner hinzufügen</button></a>', 'unknown', 'Keine Infoseite' );
		$alternative = true;
	}
	
	while( $fileid == false ){
		$pathinfo = substr($pathinfo, '0', strlen($pathinfo) - strlen(strrchr($pathinfo, '/')));
		if( empty( $pathinfo ) ){
			break;
		}
		$fileid = $folderfile->search_kimb_xxxid( $pathinfo, 'path' );
	}
	
	if( $fileid == false ){
		$pathinfo = '/';
		$fileid = $folderfile->search_kimb_xxxid( $pathinfo , 'path' );
	}
	
	//Anzeigen
	if( $alternative ){
		$sitecontent->add_site_content( '<hr />' );
		$sitecontent->add_site_content( '<h3>Wie im Frontend wird diese Infoseite angezeigt!</h3>' );
		$sitecontent->add_site_content( 'Pfad des Infotextes: '.$pathinfo );
		$sitecontent->add_site_content( '<hr />' );
	}
	
	$readmefile = new KIMBdbf( 'readme/folder_'.$fileid.'.kimb' );
	
	if( isset( $_GET['new'] ) ){
		$readmefile->write_kimb_one('markdown', '#Titel' );
	}
	
	//Inhalt MD lesen
	$readme = $readmefile->read_kimb_one('markdown' );
	
	//MD anpassen, wenn per POST neu
	if( !empty( $_POST['infoseite'] ) ){
		if( $_POST['infoseite'] != $readme ){
			if( $readmefile->write_kimb_one('markdown', $_POST['infoseite'] ) ){
				$sitecontent->echo_message( 'Änderungen wurden gespeichert!' );
				
				$readme = $_POST['infoseite'];
			}
		}
	}

	if( !empty( $readme ) ){
		
		add_codemirror( 'infoseite' );
		
		$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/backend.php?todo=infos&amp;readme&amp;path='.urlencode( $pathinfo ).'" method="post">');
		
		$sitecontent->add_site_content('<br /><br />');
		$sitecontent->add_site_content('<textarea id="infoseite" name="infoseite" style="width:100%;">'.htmlspecialchars( $readme , ENT_COMPAT | ENT_HTML401,'UTF-8' ).'</textarea>');
		$sitecontent->add_site_content('<br /><br />');
		
		$sitecontent->add_site_content('<input type="submit" value="Änderungen speichern">');
		
		//Link zur Infoseite hinzufügen
		if( $allgsysconf['urlrewrite'] == 'on' ){
			$infosite = $allgsysconf['siteurl'].'/info'.$pathinfo;
		}
		else{
			$infosite = $allgsysconf['siteurl'].'/?pfad='.urlencode( 'info'.$pathinfo  );
		}
		
		$sitecontent->add_site_content('<a href="'.$infosite.'" target="_blank"><span style="display:inline-block;" class="ui-icon ui-icon-extlink" title="Öffnet die Infoseite im Frontend des Downloaders." style="display:inline-block;" ></span></a>');

		//Readme löschen
		$sitecontent->add_site_content('<span style="display:inline-block;" onclick="delreadme();" class="ui-icon ui-icon-trash" title="Readme löschen" ></span>');

		 //Formular beenden
		 $sitecontent->add_site_content('</form>');
		 
		 //JavaScript Code für den Löschen Dialog
		$sitecontent->add_html_header('<script>
		function delreadme() {
			$( "#readmedel" ).css( "display", "block" );
			$( "#readmedel" ).dialog({
			resizable: false,
			height:200,
			modal: true,
			buttons: {
				"Ja": function() {
					$( this ).dialog( "close" );
					window.location = "'.$allgsysconf['siteurl'].'/backend.php?todo=infos&readme&path='.urlencode( $pathinfo ).'&del";
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
		//HTML-Code
		$sitecontent->add_site_content('<div style="display:none;"><div id="readmedel" title="Wirklich löschen?">Möchten Sie wirklich die Readme des Ordners "'.$pathinfo.'" löschen?</div></div>');

	}
	else{
		$sitecontent->echo_error( 'Es konnte keine Infoseite gefunden werden!' );
	}
	
}
elseif( isset( $_GET['title'] ) ){
	
	$sitecontent->add_site_content( '<h2>Ordner und Dateititel anpassen</h2>' );
	
	//Löschen
	if( !empty( $_GET['del'] ) ){
		if( $_GET['del'] == 'folder'){
			delete_title( $pathnow, 'folder' );
		}
		else{
			delete_title( $pathnow, 'file', $_GET['del'] );
		}
	}
	
	//Texte zu den Dateien lesen
	$folderfile = new KIMBdbf( 'title/folderlist.kimb' );
	$fileid = $folderfile->search_kimb_xxxid( $pathnow, 'path' );
	
	//neues readme erzeugen
	if( isset( $_GET['new'] ) ){
		$newid = $folderfile->next_kimb_id();
		$folderfile->write_kimb_id( $newid, 'add', 'path', $pathnow );
		$fileid = $newid;
	}
	
	//Titlefile des Ordners laden
	if( $fileid != false ){
		$titlefile = new KIMBdbf( 'title/folder_'.$fileid.'.kimb' );
	}

	if( $fileid == false ){
		$sitecontent->echo_error( 'Für den von Ihnen gewählten Ordner existieren keine Titel!<br /><a href="'.$allgsysconf['siteurl'].'/backend.php?todo=infos&amp;title&amp;path='.urlencode( $pathnow ).'&amp;new"><button>Für den Ordner hinzufügen</button></a>', 'unknown', 'Keine Infoseite' );
	}
	else{
		 $sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/backend.php?todo=infos&amp;title&amp;path='.urlencode( $pathnow ).'" method="post">');

		 $sitecontent->add_site_content( '<h3>Dateien und Ordner mit Titel</h3>' );
		 
		if( !empty( $_POST['send'] ) ){
			
			//Werte für existierende Dateien anpassen
			foreach( $_POST['old'] as $id => $val ){
				
				$read = $titlefile->read_kimb_id( $id );
				
				if( $read['title'] != $val ){
					$titlefile->write_kimb_id( $id, 'add', 'title', $val );
					
					$message .= 'Der Titel der Datei "'.$read['name'].'" wurde angepasst.<br />';
				}
				
			}
			
			//Werte für neue Dateien hinzufügen 
			foreach( $_POST['new'] as $file => $val ){
				
				if( !empty( $val ) ){
				
					$newid = $titlefile->next_kimb_id();
					
					$titlefile->write_kimb_id( $newid, 'add', 'name', $file );
					$titlefile->write_kimb_id( $newid, 'add', 'title', $val );
					
					$message .= 'Die Datei "'.$file.'" hat einen Titel erhalten!<br />';
				}
				
			}
			
			if( !empty( $message ) ){
				$sitecontent->echo_message( $message, 'Änderungen' );
			}

		}
		
		//aktuelle IDs lesen
		$allids = $titlefile->read_kimb_all_teilpl(  'allidslist' );
		 
		 //Dateien mit Titel
		 foreach( $allids as $id ){
			 
			 $cont = $titlefile->read_kimb_id( $id );
			 
			 $sitecontent->add_site_content( '<h4>'.$cont['name'].'</h4>' );
			 $sitecontent->add_site_content( '<textarea name="old['.$id.']" style="width:100%;">'.htmlspecialchars( $cont['title'] , ENT_COMPAT | ENT_HTML401,'UTF-8' ).'</textarea><br />' );
			 $sitecontent->add_site_content('<span style="display:inline-block;" onclick="delfolder( \'Möchten Sie wirklich den Titel für '.$cont['name'].' löschen?\', \''.urlencode( $cont['name'] ).'\' );" class="ui-icon ui-icon-trash" title="Titel dieser Datei löschen." ></span><br />');
			 
			 $donefiles[] = $cont['name'];
			 
		 }
		 
		 if( !is_array( $donefiles ) ){
			 $sitecontent->add_site_content( '<i>Keine Dateien/ Ordner in diesem Ordner haben aktuell einen Titel!</i>' );
		 }
		 
		 $sitecontent->add_site_content( '<h3>Dateien und Ordner ohne Titel</h3>' );
		 
		 //Dateien ohne Titel
		 $otherfiles = scandir( $openpath );
		 
		 //bisher keine Dateien
		 $otherdone = false;
		 
		 foreach( $otherfiles as $file ){
			 
			 if( $file != '..' && $file != '.' && !in_array( $file, $donefiles ) ){
				
			 	$sitecontent->add_site_content( '<h4>'.$file.'</h4>' );
			 	$sitecontent->add_site_content( '<textarea name="new['.$file.']" style="width:100%;"></textarea><br />' );
				 
				 $otherdone = true;
				
			 }
			 
		 }
		 
		 //Medlung wenn keine Dateien!
		  if( !$otherdone ){
			 $sitecontent->add_site_content( '<i>Alle Dateien/ Ordner in diesem Ordner haben einen Titel!</i><br />' );
		 }
		 
		 //Button senden
		 $sitecontent->add_site_content('<br /><input type="hidden" value="yes" name="send">');
		 $sitecontent->add_site_content('<input type="submit" value="Änderungen speichern">');
		 
		 //Link zur Frontend-Explorer erstellen
		if( $allgsysconf['urlrewrite'] == 'on' ){
			$exturl = $allgsysconf['siteurl'].'/explorer'.$pathnow;
		}
		else{
			$exturl = $allgsysconf['siteurl'].'/?pfad='.urlencode( 'explorer'.$pathnow  );
		}
		$sitecontent->add_site_content('<a href="'.$exturl.'" target="_blank"><span style="display:inline-block;" class="ui-icon ui-icon-extlink" title="Ordner in Frontend des Downloaders öffnen." style="display:inline-block;" ></span></a>');
		
		//Alle Titel löschen
		$sitecontent->add_site_content('<span style="display:inline-block;" onclick="delfolder( \'Möchten Sie wirklich alle Titel dieses Ordners löschen?\', \'folder\' );" class="ui-icon ui-icon-trash" title="Alle Titel dieses Ordners löschen.." ></span>');
			 
		 //Formular beenden
		 $sitecontent->add_site_content('</form>');
		 
		 //JavaScript Code für den Löschen Dialog
		$sitecontent->add_html_header('<script>
		function delfolder( text, delpar ) {
			$( "#titledel" ).html( text );
			$( "#titledel" ).css( "display", "block" );
			$( "#titledel" ).dialog({
			resizable: false,
			height:200,
			modal: true,
			buttons: {
				"Ja": function() {
					$( this ).dialog( "close" );
					window.location = "'.$allgsysconf['siteurl'].'/backend.php?todo=infos&title&path='.urlencode( $pathnow ).'&del=" + delpar;
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
		//HTML-Code
		$sitecontent->add_site_content('<div style="display:none;"><div id="titledel" title="Wirklich löschen?">Löschen??</div></div>');
	}
}

?>