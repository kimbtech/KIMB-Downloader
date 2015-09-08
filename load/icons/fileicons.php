<?php

/********************************************************/
/* CSS fuer File Icons					*/
/* Copyright (c) 2015 by KIMB-technologies		*/
/************************************************************************/
/* This program is free software: you can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License version 3	*/
/* published by the Free Software Foundation.				*/
/*									*/
/* This program is distributed in the hope that it will be useful,	*/
/* but WITHOUT ANY WARRANTY; without even the implied warranty of	*/
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the	*/
/* GNU General Public License for more details.				*/
/*									*/
/* You should have received a copy of the GNU General Public License	*/
/* along with this program.						*/
/************************************************************************/
/* www.KIMB-technologies.eu				*/
/* www.bitbucket.org/kimbtech				*/
/* http://www.gnu.org/licenses/gpl-3.0 			*/
/* http://www.gnu.org/licenses/gpl-3.0.txt 		*/
/********************************************************/
/*
	File Icons by https://github.com/teambox/Free-file-icons
		The MIT License

	Folder Icon by https://github.com/numixproject/numix-folders
		GPLv3
*/

namespace KIMBtechnologies_Fileicons;

//Alle Dateiendungen, fuer die es Icons gibt (=> Klasse "fileicon_XXXXX" )
$allfiletypes = array( 'dmg', 'rar', 'zip', 'tgz', 'iso', 'java', 'rb', 'py', 'php', 'c', 'cpp', 'ics', 'exe', 'dat', 'xml', 'yml', 'sql', 'asp', 'h', 'css', 'html', 'js', 'less', 'scss', 'sass', 'kimb', 'ppt', 'pps', 'key', 'odp', 'otp', 'txt', 'rtf', 'doc', 'dot', 'docx', 'odt', 'ott', 'ods', 'ots', 'xls', 'dotx', 'xlsx', 'gif', 'tga', 'eps', 'bmp', 'png', 'jpg', 'tiff', 'ai', 'psd', 'dwg', 'dfx', 'pdf', 'mp4', 'avi', 'mov', 'mpg', 'qt', 'flv', 'm4v', 'mp3', 'wav', 'aiff', 'aac', 'mid', 'mixed', 'blank', 'folder' );

//HTML-Code für ein Icon erstellen, Dateityp muss schon bekannt sein
//	$filetype => eine Dateiendung
//	$folder => Ist es ein Ordner?
//	Rückgabe HTML-Code
function make_html( $filetype, $folder = false ){
	global $allfiletypes;

	if( $folder ){
		return '<span class="foldericon"></span>';
	}
	else{
		$filetype = strtolower( $filetype );

		if( in_array( $filetype , $allfiletypes ) ){
			return '<span class="fileicon fileicon_'.$filetype.'"></span>';
		}
		else{
			return '<span class="fileicon fileicon_blank"></span>';
		}
	}

}

//HTML-Code fuer Icon einer Datei erstellen
//	$file => Name der Datei (Dateityp nach Endung)
//	$path => Ist in $file ein Pfad zur Datei, dann wird auch geprüft ob Ordner oder Datei
//	Rückgabe: HTML-Code
function get_fileicon( $file, $path = true ){
	global $allfiletypes;

	if( $path ){
		if( is_dir( $file ) ){
			return make_html( NULL, true );
		}
	}

	$punkt = strrpos( $file, '.' );

	if( $punkt !== false ){
		$endung = substr( $file, $punkt + 1 );

		return make_html( $endung );
	}
	else{
		return make_html( 'blank' );
	}
}


//Beispiele:
print_r( make_html( 'DOCX' ) );

?>
