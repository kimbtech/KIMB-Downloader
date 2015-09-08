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

/**********************/
//ToDo
//	Hoch
//	Titel aus KIMBdbf
/**********************/



//Ordner [öffnen,(Beschreibung aus readme)] & Dateien [view, download, (Beschreibung)]

//CSS
$sitecontent->add_html_header( ' <link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/explorer.css" media="all">' );

//aktuellen Ordner auslesen
$files = scandir( $folder );
//nach ABC sortieren
sort( $files );

//Liste beginnen
$sitecontent->add_site_content( '<div class="explorer list explorer_list"><ul>' );

foreach( $files as $file ){
	
	$is_file = false;
	$is_dir = false;
	
	if( $file != '.' && $file != '..' ){
	
		$list_element =  '<li>' ;
		
		$list_element .= KIMBtechnologies_Fileicons\get_fileicon( $folder.'/'.$file );
		
		if( $urlfrag != '/' ){
			$urlfraghier = $urlfrag.'/'.$file;
		}
		else{
			$urlfraghier = $urlfrag.$file;
		}
		
		//$titel = 'dfdsf sdf sdf sd fsdfsdf sdf sdf sdf sd fsdfdsfsd fsd fsd fsr werwerwerwe rwe rrtewr ';
		
		if( is_dir( $folder.'/'.$file ) ){
			$list_element .= '<a href="'.$allgsysconf['siteurl'].'/explorer'.$urlfraghier.'"><span class="name" title="Ordner öffnen">'.$file.'</span></a>' ;
			$list_element .= '<a href="'.$allgsysconf['siteurl'].'/info'.$urlfraghier.'"><span class="icon"><span title="Informationen zum Ordner" class="info_icon"></span></span></a>' ;
			$list_element .= '<span class="dummy"></span>' ;
			if( !empty( $titel ) ){
				$list_element .= '<span class="titel">'.$titel.'</span>' ;
			}
			
			$is_dir = true;
		}
		elseif( is_file( $folder.'/'.$file ) ){
			$list_element .= '<a href="'.$allgsysconf['siteurl'].'/view'.$urlfraghier.'"><span class="name" title="Datei ansehen">'.$file.'</span></a>' ;
			$list_element .= '<a href="'.$allgsysconf['siteurl'].'/view'.$urlfraghier.'"><span class="icon"><span class="view_icon" title="Datei ansehen" ></span></span></a>' ;
			$list_element .= '<a href="'.$allgsysconf['siteurl'].'/download'.$urlfraghier.'"><span class="icon"><span class="download_icon" title="Datei herunterladen"></span></span></a>' ;
			if( !empty( $titel ) ){
				$list_element .= '<span class="titel">'.$titel.'</span>' ;
			}
			
			$is_file = true;
		}
		
		$list_element .= '</li>' ;
		
		if( $is_file ){
			$list_files .= $list_element;
		}
		elseif( $is_dir ){
			$list_dirs .= $list_element;
		}
	}
	
}

$sitecontent->add_site_content( $list_dirs );
$sitecontent->add_site_content( $list_files );

$sitecontent->add_site_content( '</ul></div>' );


?>