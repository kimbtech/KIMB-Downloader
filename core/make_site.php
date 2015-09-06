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

//Seite erstellen
//	Hauptinhalt schon da, aber Menüpunkte fehlen (Info [bei Datei, ihr Ordner], Explorer [bei Datei, ihr Ordner], Download [deaktiviert wenn in einem Ordner], View [deaktiviert wenn in einem Ordner])

if( !isset( $urlerror ) ){
	$urlfrags = array( 'info' => 'info', 'explorer' => 'explorer', 'view' => 'vorschau', 'download' => 'download' );
	
	foreach( $urlfrags as $key => $val ){
		if( $key == $parsed ){
			$dollval['click'] = 'yes';
		}
		else{
			$dollval['click'] = 'no';
		}
	
		if( $parsed == 'info' || $parsed == 'explorer' ){
			
			if( $key != 'view' && $key != 'download' ){
				if( $allgsysconf['urlrewrite'] == 'on' ){
					$dollval['link'] = $allgsysconf['siteurl'].'/'.$key.$urlfrag;
				}
				else{
					$dollval['link'] = $allgsysconf['siteurl'].'/?pfad='.urlencode( $key.$urlfrag );
				}
			}
			else{
				$dollval = false;
			}
		}
		elseif( $parsed == 'download' || $parsed == 'view'  ){
			if( $key == 'view' || $key == 'download' ){
				if( $allgsysconf['urlrewrite'] == 'on' ){
					$dollval['link'] = $allgsysconf['siteurl'].'/'.$key.$urlfrag;
				}
				else{
					$dollval['link'] = $allgsysconf['siteurl'].'/?pfad='.urlencode( $key.$urlfrag );
				}
			}
			else{
				$urlfraghier = dirname( $urlfrag );
				
				if( $allgsysconf['urlrewrite'] == 'on' ){
					$dollval['link'] = $allgsysconf['siteurl'].'/'.$key.$urlfraghier;
				}
				else{
					$dollval['link'] = $allgsysconf['siteurl'].'/?pfad='.urlencode( $key.$urlfraghier );
				}
			}
		}
		else{
			$errormenue = true;
		}
		
		$$val = $dollval;
	}
}
else{
	$errormenue = true;
}

if( $errormenue ){
	$info = array( 'clicked' => 'no', 'link' => $allgsysconf['siteurl'] );
	$explorer = array( 'clicked' => 'no', 'link' => $allgsysconf['siteurl'] );
	$vorschau = false;
	$download = false;
}
	
$sitecontent->menue( $info, $explorer, $vorschau, $download );


//	Footer
//	Header

?>
