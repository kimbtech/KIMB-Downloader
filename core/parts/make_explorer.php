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
//	Titel aus KIMBdbf
/**********************/


$sitecontent->add_site_content( make_breadcrumb( true ) );

//Ordner [öffnen,(Beschreibung aus readme)] & Dateien [view, download, (Beschreibung)]

//CSS
$sitecontent->add_html_header( ' <link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/explorer.css" media="all">' );

//aktuellen Ordner auslesen
$files = scandir( $folder );
//nach ABC sortieren
sort( $files );

//Title lesen
$folderfile = new KIMBdbf( 'title/folderlist.kimb' );
$fileid = $folderfile->search_kimb_xxxid( $urlfrag, 'path' );

if( $fileid != false ){
	$titlefile = new KIMBdbf( 'title/folder_'.$fileid.'.kimb' );
}

//Liste beginnen
$sitecontent->add_site_content( '<div class="explorer list explorer_list"><ul>' );

foreach( $files as $file ){
	
	$is_file = false;
	$is_dir = false;
	
	if( $file != '.' && $file != '..' && $folder.'/'.$file != $codefolder .'/.htaccess' && check_rights( $folder.'/'.$file )){
	
		$list_element =  '<li>'."\r\n";
		
		$list_element .= KIMBtechnologies_Fileicons\get_fileicon( $folder.'/'.$file )."\r\n";
		
		if( $urlfrag != '/' ){
			$urlfraghier = $urlfrag.'/'.$file;
		}
		else{
			$urlfraghier = $urlfrag.$file;
		}
		
		if( isset( $titlefile) ){
			$search = $titlefile->search_kimb_xxxid( $file, 'name' );
			if( $search != false ){
				$titel = $titlefile->read_kimb_id( $search, 'title' );
			}
			else{
				$titel = '';
			}
		}
		else{
			$titel = '';
		}
		
		if( $allgsysconf['urlrewrite'] == 'on' ){
			$grundurl = $allgsysconf['siteurl'].'/';
		}
		else{
			$grundurl = $allgsysconf['siteurl'].'/?pfad=';
			$urlfraghier = urlencode( $urlfraghier );
		}
		
		if( is_dir( $folder.'/'.$file ) ){
			$list_element .= '<a href="'.$grundurl.'explorer'.$urlfraghier.'" class="name_file_outer"><span class="name" title="Ordner öffnen">'.$file.'</span></a>'."\r\n";
			$list_element .= '<a href="'.$grundurl.'info'.$urlfraghier.'" class="info_icon_outer" ><span class="info"><span title="Informationen zum Ordner" class="info_icon"></span></span></a>'."\r\n";
			$list_element .= '<span class="dummy"></span>'."\r\n";
			if( !empty( $titel ) ){
				$list_element .= '<span class="titel">'.$titel.'</span>'."\r\n";
			}
			
			$is_dir = true;
		}
		elseif( is_file( $folder.'/'.$file ) ){
			$list_element .= '<a href="'.$grundurl.'view'.$urlfraghier.'" class="name_file_outer"><span class="name" title="Datei ansehen">'.$file.'</span></a>'."\r\n";
			$list_element .= '<a href="'.$grundurl.'view'.$urlfraghier.'" class="view_icon_outer"><span class="icon"><span class="view_icon" title="Datei ansehen" ></span></span></a>'."\r\n";
			$list_element .= '<a href="'.$grundurl.'download'.$urlfraghier.'" class="download_icon_outer"><span class="icon"><span class="download_icon" title="Datei herunterladen"></span></span></a>'."\r\n";
			if( !empty( $titel ) ){
				$list_element .= '<span class="titel">'.$titel.'</span>'."\r\n";
			}
			
			$is_file = true;
		}
		
		$list_element .= '</li>'."\r\n";
		
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
