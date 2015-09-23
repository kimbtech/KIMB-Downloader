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

//an das Backend anpassen
//keine Userlevel
//Menü aus todos_list.php
//ASCII Art anpassen

//Diese Klasse ist die zentrale Ausgabeklasse des CMS Backends.
//Alle Ausgaben werden hier zusammengefasst und zum Ende ausgegeben.
//Die Klasse ist im Backend immer als $sitecontent verfügbar!

class backend_output{

	//Klasse init
	protected $header, $allgsysconf, $sitecontent, $sonderfile, $backend_todos, $downloader_modules;

	public function __construct($allgsysconf, $backend_todos, $downloader_modules){
		$this->allgsysconf = $allgsysconf;
		$this->sonderfile = new KIMBdbf('sonder.kimb');
		$this->backend_todos = $backend_todos;
		$this->downloader_modules = $downloader_modules;
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
			$this->sitecontent .= '<h1>Error - 403</h1>'.$message."\r\n";
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
			echo ('<script>'."\r\n");
			echo (' $( function () {'."\r\n");
			
			if( !check_backend_login( false ) ){
				echo ('	$( "#menu li" ).addClass("ui-state-disabled");'."\r\n");
			}
			
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
					echo("<pre>\r\n _  _____ __  __ ____        ____                      _                 _           \r\n| |/ /_ _|  \/  | __ )      |  _ \  _____      ___ __ | | ___   __ _  __| | ___ _ __ \r\n| ' / | || |\/| |  _ \ _____| | | |/ _ \ \ /\ / / '_ \| |/ _ \ / _` |/ _` |/ _ \ '__|\r\n| . \ | || |  | | |_) |_____| |_| | (_) \ V  V /| | | | | (_) | (_| | (_| |  __/ |   \r\n|_|\_\___|_|  |_|____/      |____/ \___/ \_/\_/ |_| |_|_|\___/ \__,_|\__,_|\___|_|   \r\n</pre>"."\r\n");
				echo('</div>'."\r\n");
				echo('<div id="page">'."\r\n");
				//jQuery UI Menue
				//	ul li aller Todos
				echo('<div id="menue">'."\r\n");
				echo('<ul id="menu">'."\r\n");
				foreach( $this->backend_todos as $todo ){
					echo( '<li><span class="ui-icon ui-icon-'.$todo['icon'].'"></span><a href="'.$this->allgsysconf['siteurl'].'/backend.php?todo='.$todo['todo'].'" title="'.$todo['name'].'">'.$todo['name'].'</a>' );
					if( $todo['todo'] == 'module' ){
						echo ( "\r\n".'<ul>' );
						foreach( $this->downloader_modules as $modul ){
							echo( '<li><span class="ui-icon ui-icon-'.$modul['icon'].'"></span><a href="'.$this->allgsysconf['siteurl'].'/backend.php?todo=module&amp;module='.$modul['todo'].'" title="Module">'.$modul['name'].'</a>'."\r\n" );
						}
						echo ( '</ul>' );
					}
					echo( '</li>'."\r\n");
				}
				echo( '</ul>'."\r\n");
				echo ('</div>'."\r\n");
				//kleiner Kasten links unten 
				echo ('<div id="version">'."\r\n");
					//CMS Infos & Links für nicht-Backend-Nutzer				
					echo ('<b>KIMB-technologies Downloader<br />V. '.$this->allgsysconf['systemversion'].'</b><hr />'."\r\n");
					if( check_backend_login( false ) ){
							echo ('Hallo '.$_SESSION['name'].'!<br />'."\r\n");
							echo ('<a href="'.$this->allgsysconf['siteurl'].'/backend.php?todo=login&amp;logout" style="float:right;" title="Loggen Sie sich aus."><span style="display:inline-block;" class="ui-icon ui-icon-power"></span> Logout</a>'."\r\n");
					}
					else{
						echo ('<i>Diese Seite ist nur für Administratoren!</i><br />'."\r\n");
						echo ('<a href="'.$this->allgsysconf['siteurl'].'/backend.php?todo=login">Backend Login</a><br />'."\r\n");
						echo ('<a href="'.$this->allgsysconf['siteurl'].'/">Downloader Haupseite</a><br />'."\r\n");
					}
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
