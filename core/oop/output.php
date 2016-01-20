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

//Für Themes!!
define("KIMB_CMS", "Clean Request");

//an den Downloader anpassen
//Add-on Area behalten
//wie die 4 Menüpunkte?
//mehr Design für die 4 Parts in extra Dateien?

//Diese Klasse ist die zentrale Ausgabeklasse des CMS Frontends.
//Alle Ausgaben werden hier zusammengefasst und zum Ende dem Theme übergeben
//Die Klasse ist im Frontend immer als $sitecontent verfügbar!

class system_output{

	//Klasse init
	//Systemvars
	protected $sonderfile, $allgsysconf, $sitecontent;
	//Inhaltvars
	protected $title, $footer, $description, $keywords, $hidden_menu, $header, $addon, $htmlheader, $canonical_meta;

	public function __construct($allgsysconf){
		$this->allgsysconf = $allgsysconf;
		
		$this->sonderfile = new KIMBdbf('sonder.kimb');
		$this->footer = $this->sonderfile->read_kimb_one('footer')."\r\n";
		$this->allgsysconf['description'] = $this->sonderfile->read_kimb_one('description');
		$this->allgsysconf['lang'] = 'off';
		$this->header = '<meta name="keywords" content="'.$this->sonderfile->read_kimb_one('keywords').'">'."\r\n";
		$this->htmlheader = $this->sonderfile->read_kimb_one('htmlheader')."\r\n";
	}

	//Menüeinträge hinzufügen
	//	Für jeden dauerhaften Menüpunkt ein Array (Keys Link & Click [yes/no])
	public function menue( $info, $explorer, $vorschau = false , $download = false ){
		
		//Daten in einem Array aufbereiten (für CMS Theme)
		$menues[] = array(
			'name' => 'Info',
			'link' => $info['link'],
			'niveau' => 1,
			'clicked' => $info['click']
		);
		
		$menues[] = array(
			'name' => 'Explorer',
			'link' => $explorer['link'],
			'niveau' => 1,
			'clicked' => $explorer['click']
		);
		
		if( $vorschau != false ){
			$menues[] = array(
				'name' => 'Vorschau',
				'link' => $vorschau['link'],
				'niveau' => 1,
				'clicked' => $vorschau['click']
			);
		}
		
		if( $download != false ){
			$menues[] = array(
				'name' => 'Download',
				'link' => $download['link'],
				'niveau' => 1,
				'clicked' => $download['click']
			);
		}
		
		foreach( $menues as $menu ){
			//Die Menüerstellung erfolgt durch eine Datei des Themes
			
			//CMS Theme Vars erstellen
			$name = $menu['name'];
			$link = $menu['link'];
			$niveau = $menu['niveau'];
			$clicked  = $menu['clicked'];
				
			//nach CMS Theme gucken, Fallback auf Standard
			if( isset( $this->allgsysconf['theme'] ) ){
				if( file_exists( __DIR__.'/../theme/output_menue_'.$this->allgsysconf['theme'].'.php' ) ){
					require(__DIR__.'/../theme/output_menue_'.$this->allgsysconf['theme'].'.php');
				}
				else{
					require(__DIR__.'/../theme/output_menue_norm.php');
				}
			}
			else{
				require(__DIR__.'/../theme/output_menue_norm.php');
			}
		}
	}

	//Seiteninhalt hinzufügen
	public function add_site_content($content){
		$this->sitecontent .= $content."\r\n";
	}

	//Add-on Area hinzufügen
	public function add_module_area($inhalt, $style = '', $cssclass = ''){
		$this->addon .= '<div id="apps" class="'.$cssclass.'" style="'.$style.'">'.$inhalt.'</div>'."\r\n";
	}

	//Footer hinzufügen
	public function add_footer($inhalt){
		$this->footer .= $inhalt."\r\n";
	}

	//HTML Header hinzufügen
	public function add_html_header($inhalt){
		$this->header .= $inhalt."\r\n";
	}
               
               //Canonical Meta hinzufügen
               public function add_canonical_header( $url ){
                              
                              if( $this->allgsysconf['urlrewrite'] == 'on' ){
                                             $url = $this->allgsysconf['siteurl'].'/'.$url;
		}
		else{
                                             $url = $this->allgsysconf['siteurl'].'/?path='.$url;
		}
                              
                              $this->canonical_meta = '<link rel="canonical" href="'.$url.'">'."\r\n";
               }

	//Seitentitel setzen
	public function set_title($title){
		$this->title = $title;
	}

	//Fehlermeldung ausgeben
	public function echo_error($message, $e404 = false , $heading =  'Error' ){
		//Fehler 404 mit Vorgabetext
		if( $e404 ){
			//Standard lesen
			$errmsg = $this->sonderfile->read_kimb_one('error-404');
			//ausgeben
			$this->sitecontent .= '<div id="errorbox"><h1>Error - 404</h1>'.$errmsg.'<br /><br /><i>'.$message.'</i></div>'."\r\n";
			//Header und Title
			header("HTTP/1.0 404 Not Found");
			$this->set_title( 'Error - 404' );

		}
		//eigene Überschrift und Text
		else{
			$this->sitecontent .= '<div id="errorbox"><h1>'.$heading.'</h1>'.$message.'</div>'."\r\n";
			$this->set_title( 'Error' );
		}

	}

	//abschließende Ausgabe der Seite
	public function output_complete_site( $allgsys_trans ){
		
		//Allgemeinen Header & Canonical hinzufügen
		$this->header = $this->htmlheader.$this->canonical_meta.$this->header;

		//einfügen von JavaScript Code für Platzhalter
		$jsapicodes = array(
			'<!-- jQuery -->' => '<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/jquery/jquery.min.js"></script>',
			'<!-- jQuery UI -->' => '<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/jquery/jquery-ui.min.css" >'."\r\n".'<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/jquery/jquery-ui.min.js"></script>',
			'<!-- Hash -->' => '<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/hash.js"></script>',
			'<!-- Prism -->' => '<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/prism/prism.css" >'."\r\n".'<script language="javascript" src="'.$this->allgsysconf['siteurl'].'/load/prism/prism.js"></script>'
		);

		//jede JS Datei soll nur einmal dabei sein
		//	jQuery-UI benötigt jQuery
		$add = '';
		$dones = array();
		//alle JavaScript Platzhalter durchgehen
		foreach( $jsapicodes as $key => $code ){
			//wenn Platzhalter gefunden 
			if( strpos( $this->header , $key ) !== false ){
				//im Array $dones werden alle schon hinzugefügten abgelegt
				
				//bei jQuery UI überprüfen ob jQuery schon geladen, wenn nicht beides hinzufügen
				if( $key == '<!-- jQuery UI -->' && !in_array( '<!-- jQuery -->', $dones ) && !in_array( '<!-- jQuery UI -->', $dones ) ){
					
					//beide Scripte hinzufügen
					$add .= $jsapicodes['<!-- jQuery -->']."\r\n";
					$add .= $code."\r\n";

					//beide als erledigt speichern
					$dones[] = '<!-- jQuery -->';
					$dones[] = '<!-- jQuery UI -->';
				}
				//alle anderen einfach einfügen und als erledigt markieren
				elseif( !in_array( $key, $dones ) ){
					$add .= $code."\r\n";
					$dones[] = $key;
				}
			}
		}

		//JavaScript dem Header anfügen
		$this->header = $add.$this->header;

		//alles dem Theme übergeben
		//	wenn Theme nicht gefunden Fallback auf Standard
		if( isset( $this->allgsysconf['theme'] ) ){
			if( file_exists( __DIR__.'/../theme/output_site_'.$this->allgsysconf['theme'].'.php' ) ){
				require_once(__DIR__.'/../theme/output_site_'.$this->allgsysconf['theme'].'.php');
			}
			else{
				require_once(__DIR__.'/../theme/output_site_norm.php');
			}
		}
		else{
			require_once(__DIR__.'/../theme/output_site_norm.php');
		}
	}


}

?>