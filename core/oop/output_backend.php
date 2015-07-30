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

//an das Backend anpassen
//keine Userlevel
//Menü aus todos_list.php
//ASCII Art anpassen

//Diese Klasse ist die zentrale Ausgabeklasse des CMS Backends.
//Alle Ausgaben werden hier zusammengefasst und zum Ende ausgegeben.
//Die Klasse ist im Backend immer als $sitecontent verfügbar!

class backend_output{

	//Klasse init
	protected $header, $allgsysconf, $sitecontent, $sonderfile;

	public function __construct($allgsysconf){
		$this->allgsysconf = $allgsysconf;
		$this->sonderfile = new KIMBdbf('sonder.kimb');
	}

	//Seiteninhalte hinzufügen
	public function add_site_content($content){
		$this->sitecontent .= $content."\r\n";
	}

	//HTML Header hinzufügen
	public function add_html_header($inhalt){
		$this->header .= $inhalt."\r\n";
	}

	//Meldung ausgeben
	public function echo_message($message, $heading = 'Meldung' ){
		$this->sitecontent .= '<div class="ui-widget" style="position: relative;">'."\r\n";
		$this->sitecontent .= '<div class="ui-state-highlight ui-corner-all" style="padding:10px;">'."\r\n";
		$this->sitecontent .= '<span class="ui-icon ui-icon-info" style="position:absolute; left:20px; top:7px;"></span>'."\r\n";
		$this->sitecontent .= '<h1>'.$heading.'</h1>'.$message."\r\n";
		$this->sitecontent .= '</div></div>'."\r\n";
	}

	//Fehler ausgeben
	//	404, 403 oder ?
	public function echo_error($message = '', $art = 'unknown', $heading = 'Error - Fehler'){
		$this->sitecontent .= '<div class="ui-widget" style="position: relative;">'."\r\n";
		$this->sitecontent .= '<div class="ui-state-error ui-corner-all" style="padding:10px;">'."\r\n";
		$this->sitecontent .= '<span class="ui-icon ui-icon-alert" style="position:absolute; left:20px; top:7px;"></span>'."\r\n";
		if( $art == '404' ){
			$errmsg = $this->sonderfile->read_kimb_one('error-404');
			$this->sitecontent .= '<h1>Error - 404</h1>'.$errmsg.'<br /><br /><i>'.$message.'</i>'."\r\n";
			header("HTTP/1.0 404 Not Found");

		}
		elseif( $art == '403' ){
			$errmsg = $this->sonderfile->read_kimb_one('error-403');
			$this->sitecontent .= '<h1>Error - 403</h1>'.$errmsg.'<br /><br /><i>'.$message.'</i>'."\r\n";
			header('HTTP/1.0 403 Forbidden');
		}
		else{
			$this->sitecontent .= '<h1>'.$heading.'</h1>'.$message."\r\n";
		}
		$this->sitecontent .= '</div></div>'."\r\n";
	}

	//gesamte Seite ausgeben
	public function output_complete_site(){
		
		//den URL-Placeholder duch die aktuelle URL ersetzen
		$this->header = str_replace( '<!--SYS-SITEURL-->', $this->allgsysconf['siteurl'], $this->header );
		$this->sitecontent = str_replace( '<!--SYS-SITEURL-->', $this->allgsysconf['siteurl'], $this->sitecontent );
		
		//HTML Code
		echo('<!DOCTYPE html> <html> <head>'."\r\n");
		//HTML Header
		//	inkl. allen JS & CSS
			echo ('<title>'.$this->allgsysconf['sitename'].' : Backend</title>'."\r\n");
			echo ('<link rel="shortcut icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
			echo ('<link rel="icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
			echo ('<meta name="generator" content="KIMB-technologies CMS V. '.$this->allgsysconf['systemversion'].'" >'."\r\n");
			echo ('<meta name="robots" content="none">'."\r\n");
			echo ('<meta charset="utf-8">'."\r\n");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/fonts.css" >'."\r\n");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/be.css" >'."\r\n");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/jquery/jquery-ui.min.css" >'."\r\n");
			echo ('<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js"></script>'."\r\n");
			echo ('<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/jquery/jquery-ui.min.js"></script>'."\r\n");
			echo ('<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/hash.js"></script>'."\r\n");
			echo ('<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/nicEdit.js"></script>'."\r\n");
			echo ('<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/system/tinymce/tinymce.min.js"></script>'."\r\n");
			
			//Menü disabled, je nach Rechten
			echo ('<script>'."\r\n");
			echo ('$(function() {'."\r\n");
			if( $_SESSION['permission'] == 'more' ){
				//alles aktiviert
			}
			elseif( $_SESSION['permission'] == 'less' ){
				//nur admin deaktiviert
				echo ('	$( "ul#menu li.admin" ).addClass("ui-state-disabled");'."\r\n");

			}
			elseif( $_SESSION['loginokay'] == $this->allgsysconf['loginokay'] ){
				//Systemspezifisches Level
				//	alles nach und nach nachschauen und dann evtl. deaktivieren

				//Level suchen
				if( !is_object( $levellist ) ){
					$levellist = new KIMBdbf( 'backend/users/level.kimb' );
				}
				$permissteile = $levellist->read_kimb_one( $_SESSION['permission'] );
				
				if( !empty( $permissteile ) ){
					//Level in englische Zahlen teilen
					$permissteile = explode( ',' , $permissteile );
					//alle englischen Zahlen lesen und teilen
					$all = $levellist->read_kimb_one( 'all' );
					$all = explode( ',' , $all );
					
					//alles was im Level nicht ist, dafür aber in der Gesamtmenge der englischen Zahlen, ausblenden
					foreach( $all as $teil ){
						if( !in_array( $teil , $permissteile ) ){
							echo ('	$( "ul#menu li.'.$teil.'" ).addClass("ui-state-disabled");'."\r\n");
						}
					}
				}
				else{
					//Userlevel nicht gefunden -> Fehler
					$this->sitecontent = '';
					$this->echo_error( 'Ihr Userlevel ist fehlerhaft!' );
					echo ('	$( "ul#menu li.admin" ).addClass("ui-state-disabled");'."\r\n");
					echo ('	$( "ul#menu li.editor" ).addClass("ui-state-disabled");'."\r\n");
				}

			}
			else{
				//nicht eingeloggt, alles deaktivieren
				echo ('	$( "ul#menu li.admin" ).addClass("ui-state-disabled");'."\r\n");
				echo ('	$( "ul#menu li.editor" ).addClass("ui-state-disabled");'."\r\n");
			}
			//Tooltips und Menü starten
			echo ('	$( document ).tooltip();'."\r\n");
			echo ('	$( "#menu" ).menu();'."\r\n");
			echo ('});'."\r\n");
			echo ('</script>'."\r\n");
			
				//HTML Header
				echo($this->header);
				echo("\r\n");

		echo('</head><body>'."\r\n");
				echo('<div id="header">'."\r\n");
					//KIMB-CMS Backend Schriftzug
					echo("<pre>\r\n _  _____ __  __ ____         ____ __  __ ____  \r\n| |/ /_ _|  \/  | __ )       / ___|  \/  / ___| \r\n| ' / | || |\/| |  _ \ _____| |   | |\/| \___ \ \r\n| . \ | || |  | | |_) |_____| |___| |  | |___) |\r\n|_|\_\___|_|  |_|____/       \____|_|  |_|____/ \r\n</pre>"."\r\n");
				echo('</div>'."\r\n");
				echo('<div id="page">'."\r\n");
				echo('<div id="userinfo">'."\r\n");
				//kleiner Kasten rechts oben mit Infos
				if( $_SESSION['loginokay'] == $this->allgsysconf['loginokay'] ){
					//bei Login
					
					//Begrüßung
					echo ('Hallo User <i><u>'.$_SESSION['name'].'</u></i>'."\r\n");
					//Schnellzugriffe
					echo ('<div style="float:right; position:absolute; right:10px; top:0px;">');
					echo ('<a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit&amp;user='.$_SESSION['user'].'" title="Usereinstellungen bearbeiten"><span class="ui-icon ui-icon-pencil"></span></a>'."\r\n");
					echo ('<a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/index.php?todo=logout" title="Abmelden und die Sitzung beenden!"><span class="ui-icon ui-icon-power"></span></a>'."\r\n");
					echo ('<a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/index.php" title="Hauptseite des Backends ( Login, ... )"><span class="ui-icon ui-icon-home"></span></a>'."\r\n");
					echo ('</div><br />');
					//Userrechte Hinweise
					if( $_SESSION['permission'] == 'more' ){
						echo ('<i title="Sie haben alle Rechte in Backend!" >Admin</i>'."\r\n");
					}
					elseif( $_SESSION['permission'] == 'less' ){
						echo ('<i title="Sie haben eingeschränkte Rechte in Backend, einige Links sind im Menue deaktiviert!" >Editor</i>'."\r\n");
					}
					else{
						echo ('<i title="Sie haben ein von Ihrem Admin erstelles Zugriffslevel!" >Systemspezifisch</i>'."\r\n");
					}
					//Cache leeren Button
					echo ('<div style="float:right; position:absolute; right:40px; bottom:7px;"><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php?todo=purgecache" title="Den Cache leeren. (Dies ist nur nach einer Änderung im Menü oder für bestimmte Add-ons nötig!)"><span class="ui-icon 	ui-icon-refresh"></span></a></div>'."\r\n");
 
				}
				else{
					//ohne Login nichts zu sehen
					echo('Nicht eingeloggt!<br /><span class="ui-icon ui-icon-cancel"></span>'."\r\n");
				}
				echo('</div>'."\r\n");
				//jQuery UI Menue
				//	ul, li verschachtelt
				//	jeder Link hat zwei Klassen, einmal die voreingestellten Rechte (less,more) mit den Klassen (admin, editor), außerdem
				//	findet man die englischen Zahlen (von der Backend Rechteverwaltung) für jeden einzelnen Link 
				echo('<div id="menue">'."\r\n");
echo('
<!-- Menue - jQuery UI -->

			<ul id="menu">
			<li class="editor one" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/sites.php" title="Seiten erstellen, löschen, bearbeiten"><span class="ui-icon ui-icon-document"></span>Seiten</a>
			<ul>
					<li class="editor two" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=new" title="Eine neue Seite erstellen."><span class="ui-icon ui-icon-plusthick"></span>Erstellen</a></li>
					<li class="editor three" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=list" title="Alle Seiten zum Bearbeiten, De-, Aktivieren und Löschen auflisten."><span class="ui-icon ui-icon-calculator"></span>Auflisten</a></li>
			</ul>
			</li>
			<li class="editor four" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/menue.php" title="Menüs erstellen, löschen, bearbeiten"><span class="ui-icon ui-icon-newwin"></span>Menue</a>
				<ul>
					<li class="admin five" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=new" title="Einen neuen Menüpunkt erstellen."><span class="ui-icon ui-icon-plusthick"></span>Erstellen</a></li>
					<li class="editor six" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=connect" title="Einen Seite einem Menüpunkt zuordnen."><span class="ui-icon ui-icon-arrowthick-2-e-w"></span>Zuordnen</a></li>
					<li class="admin seven" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=list" title="Die gesamte Menüstruktur zum Bearbeiten und Löschen darstellen."><span class="ui-icon ui-icon-calculator"></span>Auflisten</a></li>
				</ul>
			</li>
			<li class="admin eight" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/user.php" title="Backenduser erstellen, löschen, bearbeiten"><span class="ui-icon ui-icon-person"></span>User</a>
				<ul>
					<li class="admin nine" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=new" title="Einen neuen Backenduser erstellen."><span class="ui-icon ui-icon-plusthick"></span>Erstellen</a></li>
					<li class="admin ten" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=list" title="Alle Backenduser zum Bearbeiten und Löschen auflisten."><span class="ui-icon ui-icon-calculator"></span>Auflisten</a></li>
				</ul>
			</li>
			<li class="admin eleven" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php" title="Systemkonfiguration anpassen"><span class="ui-icon ui-icon-gear"></span>Konfiguration</a></li>
			<li class="editor twelve" ><span class="ui-icon ui-icon-plusthick"></span>Add-ons
				<ul>
					<li class="editor thirteen" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=less" title="Add-on Nutzung als Editor"><span class="ui-icon ui-icon-plusthick"></span>Nutzung</a></li>
					<li class="admin fourteen" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/addon_conf.php?todo=more" title="Add-on Konfiguration als Admin"><span class="ui-icon ui-icon-wrench"></span>Konfiguration</a></li>
					<li class="admin fiveteen" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php" title="Add-ons installieren, löschen, de-, aktivieren"><span class="ui-icon ui-icon-circle-arrow-n"></span>Installation</a></li>
				</ul>
			</li>
			<li class="editor sixteen" ><span class="ui-icon ui-icon-help"></span>Other
				<ul>
					<li class="editor seventeen" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/other_filemanager.php" title="Dateien zum, Einbinden in Ihrer Seite, hochladen und verwalten, &apos;&apos;sichere&apos;&apos; Speicherung"><span class="ui-icon ui-icon-image"></span>Filemanager</a></li>
					<li class="admin eightteen" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/other_themes.php" title="Verändern Sie das Design des Frontends mit KIMB-CMS Themes"><span class="ui-icon ui-icon-contact"></span>Themes</a></li>
					<li class="admin nineteen" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/other_level.php" title="Erstellen Sie eigene Benutzerlevel für das Backend"><span class="ui-icon  ui-icon-locked"></span>Userlevel Backend</a></li>
					<li class="admin twenty" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/other_umzug.php" title="Erstellen Sie Weiterleitungen von alten Links auf neue Seiten"><span class="ui-icon ui-icon-suitcase"></span>Umzug</a></li>
					<li class="admin twentyone" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/other_lang.php" title="Erweitern Sie diese Seite um weitere Sprachen"><span class="ui-icon ui-icon-flag"></span>Mehrsprachige Seite</a></li>
					<li class="admin twentytwo" ><a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/other_easymenue.php" title="Erlauben Sie Usern, welche nur Seiten erstellen können, zu einer Seite ein Menue zu erstellen!"><span class="ui-icon ui-icon-link"></span>Easy Menue</a></li>
				</ul>
			</li>
			</ul>
<!-- Menue - jQuery UI -->
');
				echo ('</div>'."\r\n");
				//kleiner Kasten links unten 
				echo ('<div id="version">'."\r\n");
					//CMS Infos & Links für nicht-Backend-Nutzer				
					echo ('<b>KIMB-technologies CMS<br />V. '.$this->allgsysconf['systemversion'].'</b><br />'."\r\n");
					echo ('<i>Diese Seite ist nur für Administratoren!</i><br />'."\r\n");
					echo ('<a href="'.$this->allgsysconf['siteurl'].'/">Zurück</a><br />'."\r\n");
					echo ('<a href="'.$this->allgsysconf['siteurl'].'/kimb-cms-backend/index.php">Backend Login</a>'."\r\n");
				echo ('</div>'."\r\n");
					
				//Seiteninhalt
				echo('<div id="content">'."\r\n");

					//ausgeben
					echo($this->sitecontent);
					echo("\r\n");

				echo('</div></div>'."\r\n");
		echo('</body> </html>');
	}
}

//feddig
?>
