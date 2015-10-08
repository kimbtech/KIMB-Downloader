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

//Array mit Liste der Module (sowas wie Add-ons)
//	wird aus JSON im diesem Ordner gelesen
$jsonfile = file_get_contents( __DIR__.'/modules_list.json' );
$downloader_modules = json_decode( $jsonfile , true);

//wenn keine Module aktiviert, leeres Array
if( $downloader_modules == null ){
	$downloader_modules = array();
}

//nur die ToDos in Array
$modules_todos_list = array_column($downloader_modules, 'todo');
?>
