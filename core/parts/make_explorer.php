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

//Ordnerstruktur anzeigen

//Pfad
//Hoch
//Ordner [öffnen,(Beschreibung aus readme)] & Dateien [view, download, (Beschreibung)]

//aktuellen Ordner auslesen
$files = scandir( $folder );
//nach ABC sortieren
sort( $files );

//Liste beginnen
$sitecontent->add_site_content( '<div class="explorer list explorer_list"><ul>' );

foreach( $files as $file ){
	
	if( $file != '.' && $file != '..' ){
	
		$sitecontent->add_site_content( '<li>' );
		
		if( is_dir( $folder.'/'.$file ) ){
			$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/explorer"'.$urlfrag.'></a>' );
			$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/info"'.$urlfrag.'></a>' );
		}
		elseif( is_file( $folder.'/'.$file ) ){
			
			$urlfraghier = dirname( $urlfrag );
			
			$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/explorer"'.$urlfraghier.'></a>' );
			$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/info"'.$urlfraghier.'></a>' );
			$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/view"'.$urlfrag.'></a>' );
			$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/download"'.$urlfrag.'></a>' );
		}
		
		$sitecontent->add_site_content( '<li>' );
		
	}
	
}

$sitecontent->add_site_content( '</ul></div>' );


?>
