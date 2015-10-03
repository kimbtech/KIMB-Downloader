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

//FE First Module laden (Werte aus Liste)

//alle Module durchgehen
foreach( $downloader_modules as $mod ){
	
	//benötigt Module funcclass?
	if( in_array( 'fe_fi', $mod['parts'] ) ){
		
		//funcclass Datei vorhanden?
		if( is_file( __DIR__.'/'.$mod['todo'].'/fe_fi.php' ) ){
			
			//Datei laden
			require_once( __DIR__.'/'.$mod['todo'].'/fe_fi.php' );
		}
	}
}
?>
