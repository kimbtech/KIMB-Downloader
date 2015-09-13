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

//Menüpunkte erstellen

//Fehler im Aufruf?
if( !isset( $urlerror ) ){
	//Array mit Zuordung 'parsed/url'' => 'Menue Name'
	$urlfrags = array( 'info' => 'info', 'explorer' => 'explorer', 'view' => 'vorschau', 'download' => 'download' );
	
	//alle URL Teile durchgehen
	foreach( $urlfrags as $key => $val ){
		//Ist dieser Teil der aktuelle?
		if( $key == $parsed ){
			//geklickt
			$dollval['click'] = 'yes';
		}
		else{
			//nicht geklickt
			$dollval['click'] = 'no';
		}
	
		//Ist aktuell eine Readme/ der Explorer aufgerufen?
		if( $parsed == 'info' || $parsed == 'explorer' ){
			//Nur Menüpunkte für explorer und view erstellen
			
			//view und download ignorieren
			if( $key != 'view' && $key != 'download' ){
				//Unterscheidung des Links zwischen URL Rewrite und GET URL
				if( $allgsysconf['urlrewrite'] == 'on' ){
					//URL für rewriting
					$dollval['link'] = $allgsysconf['siteurl'].'/'.$key.$urlfrag;
				}
				else{
					//URL mit GET
					$dollval['link'] = $allgsysconf['siteurl'].'/?pfad='.urlencode( $key.$urlfrag );
				}
			}
			else{
				//view und download ignorieren
				$dollval = false;
			}
		}
		//Ist der aktuelle Aufruf download oder view?
		elseif( $parsed == 'download' || $parsed == 'view'  ){
			//Links für view oder download erstellen
			if( $key == 'view' || $key == 'download' ){
				//Unterscheidung des Links zwischen URL Rewrite und GET URL
				if( $allgsysconf['urlrewrite'] == 'on' ){
					//URL für rewriting
					$dollval['link'] = $allgsysconf['siteurl'].'/'.$key.$urlfrag;
				}
				else{
					//URL mit GET
					$dollval['link'] = $allgsysconf['siteurl'].'/?pfad='.urlencode( $key.$urlfrag );
				}
			}
			else{
				//Links für Info oder Explorer
				 
				//Dateinamen aus der URL entfernen
				$urlfraghier = dirname( $urlfrag );
				
				//Unterscheidung des Links zwischen URL Rewrite und GET URL
				if( $allgsysconf['urlrewrite'] == 'on' ){
					//URL für rewriting
					$dollval['link'] = $allgsysconf['siteurl'].'/'.$key.$urlfraghier;
				}
				else{
					//URL mit GET
					$dollval['link'] = $allgsysconf['siteurl'].'/?pfad='.urlencode( $key.$urlfraghier );
				}
			}
		}
		else{
			//kein passender parsed?
			// => Fehler
			$errormenue = true;
		}
		
		//die Variablen für die menue Methode erstellen
		$$val = $dollval;
	}
}
else{
	//Fehler in der URL
	// => Fehler
	$errormenue = true;
}

//Fehler?
if( $errormenue ){
	//Fehlermenü
	//nur Explorer von / und Info von /
	
	//Unterscheidung des Links zwischen URL Rewrite und GET URL
	if( $allgsysconf['urlrewrite'] == 'on' ){
		//URL für rewriting
		$explorerurl = $allgsysconf['siteurl'].'/explorer';
		$infourl = $allgsysconf['siteurl'].'/info';
	}
	else{
		//URL mit GET
		$explorerurl = $allgsysconf['siteurl'].'/?pfad=explorer';
		$infourl = $allgsysconf['siteurl'].'/?pfad=info';
	}
	//Arrays für menue Methode vorbereiten
	$info = array( 'clicked' => 'no', 'link' => $infourl );
	$explorer = array( 'clicked' => 'no', 'link' => $explorerurl );
	$vorschau = false;
	$download = false;
}

//Menue Methode ausfrufen (mit vars von oben)
$sitecontent->menue( $info, $explorer, $vorschau, $download );


//Seitentitel
//	parsed name - aktueller Ordner
$parttitles = array( 'info' => 'Info', 'explorer' => 'Explorer', 'view' => 'Vorschau', 'download' => 'Download' );
$sitecontent->set_title( $parttitles[$parsed].' - '.$urlfrag );

//Header
//	CSS Fileicons
$sitecontent->add_html_header( ' <link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/icons/fileicons.css" media="all">' );
//	CSS allgemein Downloader
$sitecontent->add_html_header( ' <link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/downloader.css" media="all">' );

?>