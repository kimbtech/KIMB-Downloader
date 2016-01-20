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

//URL Leiste
$sitecontent->add_site_content( make_breadcrumb( false, true) );

//Readme des Verzeichnisses anzeigen
$folderfile = new KIMBdbf( 'readme/folderlist.kimb' );
$fileid = $folderfile->search_kimb_xxxid( $urlfrag, 'path' );
$urlfraghier = $urlfrag;
while( $fileid == false ){
	$urlfraghier = substr($urlfraghier, '0', strlen($urlfraghier) - strlen(strrchr($urlfraghier, '/')));
	if( empty( $urlfraghier ) ){
		break;
	}
	$fileid = $folderfile->search_kimb_xxxid( $urlfraghier, 'path' );
}

if( $fileid == false ){
	$fileid = $folderfile->search_kimb_xxxid( '/', 'path' );
}

$readmefile = new KIMBdbf( 'readme/folder_'.$fileid.'.kimb' );
	
$readme = $readmefile->read_kimb_one('markdown' );

if( !empty( $readme ) ){
	$sitecontent->add_site_content( MarkdownExtra::defaultTransform($readme) );
               
               //Infoseite eines höheren Ordners??
               if( $urlfraghier != $urlfrag ){
                              //Canonical anpassen (evtl. )
                              $sitecontent->add_canonical_header( 'info'.$urlfraghier );
               }
}
else{
	$sitecontent->echo_error( 'Es konnten keine Infos gefunden werden!', true );
	$errormessset = true;
}

?>
