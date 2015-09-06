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
	protected $header, $allgsysconf, $sitecontent, $sonderfile, $backend_todos;

	public function __construct($allgsysconf, $backend_todos){
		$this->allgsysconf = $allgsysconf;
		$this->sonderfile = new KIMBdbf('sonder.kimb');
		$this->$backend_todos = $backend_todos;
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
		
		//HTML Code
		echo('<!DOCTYPE html> <html> <head>'."\r\n");
		//HTML Header
		//	inkl. allen JS & CSS
			echo ('<title>'.$this->allgsysconf['sitename'].' : Backend</title>'."\r\n");
			echo ('<link rel="shortcut icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
			echo ('<link rel="icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
			echo ('<meta name="generator" content="KIMB-technologies Downloader V. '.$this->allgsysconf['systemversion'].'" >'."\r\n");
			echo ('<meta name="robots" content="none">'."\r\n");
			echo ('<meta charset="utf-8">'."\r\n");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/fonts.css" >'."\r\n");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/be.css" >'."\r\n");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/jquery/jquery-ui.min.css" >'."\r\n");
			echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/prism/prism.css" >'."\r\n");
			echo ('<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/jquery/jquery.min.js"></script>'."\r\n");
			echo ('<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/jquery/jquery-ui.min.js"></script>'."\r\n");
			echo ('<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/hash.js"></script>'."\r\n");
			echo ('<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/prism/prism.js"></script>'."\r\n");
			
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
					//KIMB-Downloader Backend Schriftzug
					echo("<pre>\r\n _  _____ __  __ ____         ____ __  __ ____  \r\n| |/ /_ _|  \/  | __ )       / ___|  \/  / ___| \r\n| ' / | || |\/| |  _ \ _____| |   | |\/| \___ \ \r\n| . \ | || |  | | |_) |_____| |___| |  | |___) |\r\n|_|\_\___|_|  |_|____/       \____|_|  |_|____/ \r\n</pre>"."\r\n");
				echo('</div>'."\r\n");
				echo('<div id="page">'."\r\n");
				echo('<div id="userinfo">'."\r\n");
				//kleiner Kasten rechts oben mit Infos
				if( $_SESSION['loginokay'] == $this->allgsysconf['loginokay'] ){
					//bei Login
					
					//Begrüßung
					echo ('Hallo User <i><u>'.$_SESSION['name'].'</u></i>'."\r\n");
					//Logout
					echo ('<br />'); 
					echo ('<a href="'.$this->allgsysconf['siteurl'].'/backend.php?todo=login&amp;logout" title="Abmelden und die Sitzung beenden!"><span class="ui-icon ui-icon-power"></span></a>'."\r\n");
					echo ('<br />'); 
				}
				else{
					//ohne Login nichts zu sehen
					echo('Nicht eingeloggt!<br /><span class="ui-icon ui-icon-cancel"></span>'."\r\n");
				}
				echo('</div>'."\r\n");
				//jQuery UI Menue
				//	ul li aller Todos
				echo('<div id="menue">'."\r\n");
				echo('<ul id="menu">'."\r\n");
				foreach( $this->$backend_todos as $todo ){
					echo( '<li><a href="'.$this->allgsysconf['siteurl'].'/backend.php?todo='.$todo['todo'].'" title="'.$todo['name'].'">'.$todo['name'].'</a></li>'."\r\n");
				}
				echo( '</ul>'."\r\n");
				echo ('</div>'."\r\n");
				//kleiner Kasten links unten 
				echo ('<div id="version">'."\r\n");
					//CMS Infos & Links für nicht-Backend-Nutzer				
					echo ('<b>KIMB-technologies Downloader<br />V. '.$this->allgsysconf['systemversion'].'</b><br />'."\r\n");
					echo ('<i>Diese Seite ist nur für Administratoren!</i><br />'."\r\n");
					echo ('<a href="'.$this->allgsysconf['siteurl'].'/">Zurück</a><br />'."\r\n");
					echo ('<a href="'.$this->allgsysconf['siteurl'].'/backend.php?todo=login">Backend Login</a>'."\r\n");
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
