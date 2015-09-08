<?php

/*************************************************/
//KIMB CMS
//KIMB ContentManagementSystem
//Copyright (c) 2014 by KIMB-technologies
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
//www.KIMB-technologies.eu
//www.bitbucket.org/kimbtech
//http://www.gnu.org/licenses/gpl-3.0
//http://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/


// Diese Datei gibt das Grundgerüst für die Ausgabe
// Folgende Variablen sollten verwendet werden:
//    $this->header, $this->title, $this->menue, $this->addon, $this->sitecontent, $this->footer
//    array( $this->allgsysconf )
// Diese Datei ist Teil eines Objekts

defined('KIMB_CMS') or die('No clean Request');

//wenn mehrsprachige Seiten aktiviert HTML href-lang-Tag setzen
if( $this->allgsysconf['lang'] == 'on' ){
	echo('<!DOCTYPE html> <html lang="'.$this->requestlang['tag'].'"> <head>'."\r\n");
}
else{
	echo('<!DOCTYPE html> <html> <head>'."\r\n");
}

//HTML Header der Seite beginnen
//	Titel
echo ('<title>'.$this->allgsysconf['sitename'].' : '.$this->title.'</title>'."\r\n");
//	Icons
echo ('<link rel="shortcut icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
echo ('<link rel="icon" href="'.$this->allgsysconf['sitefavi'].'" type="image/x-icon; charset=binary">'."\r\n");
//	Generator
echo ('<meta name="generator" content="KIMB-technologies CMS V. '.$this->allgsysconf['systemversion'].'" >'."\r\n");
//	Robots
echo ('<meta name="robots" content="'.$this->allgsysconf['robots'].'">'."\r\n");
//	Description
echo ('<meta name="description" content="'.$this->allgsysconf['description'].'">'."\r\n");
//	charset
echo ('<meta charset="utf-8">'."\r\n");
//	CSS (font, print, screen)
echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/fonts.css" media="all">'."\r\n");
echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/design.css" media="screen">'."\r\n");
echo ('<link rel="stylesheet" type="text/css" href="'.$this->allgsysconf['siteurl'].'/load/system/theme/print.css" media="print">'."\r\n");
//	Touch Icon
echo ('<link href="'.$this->allgsysconf['siteurl'].'/load/system/theme/touch_icon.png" rel="apple-touch-icon" />'."\r\n");
//JavaScript Code
//	Bei vielen Touch Geräten (alle außer iOS) wird das hover des Menüs falsch interpretiert.
//	Beim Klick auf einen Menüpunkt wird sofort der Link geöffnet, auch wenn noch ein Untermenü vorhaden ist, man kann auf
//	dem Untermenü nichts anklicken, es blitzt nur kurz auf.
//	Auf dem Desktop wird zwischen klicken und herüberfahren unterschieden.
//	Bei iOS wird erst beim zweiten Klick auf ein solches Menü der Link geöffnet, daher ist die Bedienung problemlos möglich.
//
//	Jeder Menüpunkt führt als onclick="" eine Funktion aus, welche überprüft ob es sich um ein Touch Gerät ohne iOS handelt, sofern
//	dies der Fall ist wird für jeden Menüpunkt die Anzahl der Klicks gezählt und erst beim zweiten der Link aufgerufen.
echo ('<script> var clicks = new Array(); function menueclick( id ){ var isTouch = (("ontouchstart" in window) || (navigator.msMaxTouchPoints > 0)); var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false ); if( isTouch && !iOS ){ if (!( id in clicks)) { clicks[id] = 0; } clicks[id]++; if( clicks[id] == 2 ){ return true; } else{ return false; } } else{ return true; } }</script>'."\r\n");

	//HTML Header hinzufügen
	echo($this->header);
	echo("\r\n");

echo('</head><body>'."\r\n");
	//Die Seite beginnt
	echo('<div id="page">'."\r\n");
	
		//Header der Seite
		//	Logo und Link zur Startseite sowie Name der Seite	
		echo('<div id="header">'."\r\n");
			echo('<a href="'.$this->allgsysconf['siteurl'].'/">'.$this->allgsysconf['sitename']."\r\n");
			echo('<img src="'.$this->allgsysconf['siteurl'].'/load/system/theme/logo.png" style="border:none; float:right;"></a>'."\r\n");
		echo('</div>'."\r\n");
		//Menü
		//	ul muss geöffnet werden
		echo('<div><ul id="nav">'."\r\n");

			echo($this->menue);
			echo("\r\n");
			//schließendes li anfügen
			echo('</li>');
			//wenn nötig schließende ul anfügen
			echo( str_repeat( '</ul>' , $this->ulauf ) );

		//Suchfunktion im der Menübar anzeigen
		//Ist das Add-on Suche installiert?
		if( is_dir( __DIR__.'/../addons/search_sitemap/' ) ){

			//Suche Konfiguration laden
			$search_sitemap['file'] = new KIMBdbf( 'addon/search_sitemap__conf.kimb' );

			//Ist die Suche aktiviert, auf welcher Seite liegt sie?
			$search_sitemap['searchsiteid'] = $search_sitemap['file']->read_kimb_one( 'searchsiteid' ); // off oder id

			//wenn die Suche aktiviert ist, HTML Form hinzufügen
			if( $search_sitemap['searchsiteid'] != 'off' && !empty( $search_sitemap['searchsiteid'] ) ){

				//als li das Suchfeld anfügen
				echo('<li>'."\r\n");
				echo('<form method="post"  action="'.$allgsysconf['siteurl'].'/index.php?id='.$search_sitemap['searchsiteid'].'">'."\r\n");
				echo('<input style="background-color:#EEc900; color:#000; padding: 8px 20px; border:none;" type="text" name="search" placeholder="'.$allgsys_trans['themesite']['such'].'" value="'.htmlentities( $_REQUEST['search'], ENT_COMPAT | ENT_HTML401,'UTF-8' ).'">'."\r\n");
				echo('</form>'."\r\n");	
				echo('</li>'."\r\n");
			}
		}
		
		//mehrsprachige Seite aktiviert?
		if( $this->allgsysconf['lang'] == 'on' ){
			//als li den Sprachumschalter anfügen
			echo('<li id="lang">'."\r\n");
			
			//Alle Sprachen lesen und Links mit Flaggen erzeugen
			foreach( $this->allglangs as $lang ){
				echo( '<a href="'.$lang['thissite'].'"><img src="'.$lang['flag'].'" title="'.$lang['name'].'" alt="'.$lang['name'].'"></a>' );
			}
			
			echo('</li>'."\r\n");
		}

		//die Menübar beenden
		echo('</ul></div>'."\r\n");

		//Add-on Bereiche anfügen
		if( !empty( $this->addon ) ){
			//Seite beginnen
			echo('<div id="site">'."\r\n");
			
					//Add-on Teile
					echo($this->addon);
					echo("\r\n");

				echo('<div id="contents" >'."\r\n");

					//Seiteninhalte
					echo($this->sitecontent);
					echo("\r\n");

				echo('</div></div>'."\r\n");
			echo('<div id="footer" >'."\r\n");
		}
		//ohne Add-on Bereiche
		else{
			echo('<div id="site">'."\r\n");
				echo('<div id="contentm" >'."\r\n");

					//Seiteninhalte
					echo($this->sitecontent);
					echo("\r\n");

				echo('</div></div>'."\r\n");
			echo('<div id="footer" >'."\r\n");
		}
			//Das aktuelle Jahr mit (c) ausgeben
			echo('&copy; '.date('Y').' ' );		

			//und den Footer anschließen
			echo($this->footer);
			echo("\r\n");

		echo('</div>'."\r\n");
	echo('</div>'."\r\n");

/*
 ==> Beim Downloader nicht nötig!!
	
	//JavaScript Code
	//	Der Breadcrumb sowie die "siteinfos" (unterer Rand des Inhaltes) sollen keinen Inhalt überdecken.
	//	Absolute Abstände des Inhalts vom Rand können hier nicht angegeben werden, durch das responsive Design variieren diese Werte.
	//	Der Code sorgt dafür, dass der padding (Abstabd) des Inhaltes oben und unten ausreichen ist, sodass nichts verdeckt wird.
	//	(Aufgrund dieses Codes kann es nötig sein, die Seite nach einer Veränderung des Anzeigeformates neu zu laden!) 
	echo ('<script>if( document.getElementById( "contentm" ) != null ){ var cont = document.getElementById( "contentm" ); var fooadd = 4; } else{ var cont = document.getElementById( "contents" ); var fooadd = 4; } cont.style.paddingTop = document.getElementById("breadcrumb").clientHeight + 5 + "px";  document.getElementById("footer").style.width = cont.offsetWidth  - fooadd + "px"; if( document.getElementById( "permalink" ) != null ){ cont.style.paddingBottom = document.getElementById("permalink").clientHeight + 5 + "px"; } if( document.getElementById( "usertime" ) != null ){ cont.style.paddingBottom = document.getElementById("usertime").clientHeight + 5 + "px"; }</script>'."\r\n");
*/
echo('</body> </html>');

//feddig
?>
