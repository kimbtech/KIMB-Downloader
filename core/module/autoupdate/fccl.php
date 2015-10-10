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

//Funcclass

//nach Updates mit der KIMB API suchen
//	Updates für Downloader
//		$time => wenn true, nur alle 3 Tage aktualisieren, bei false immer
//		Rückgabe: true wenn API Abfrage durchgeführt, false wenn nicht 
function api_check_for_updates( $time = true ){
	global $module_autoupdate_infofile,$allgsysconf; 
	
	if( $time){
		$lastcheck = $module_autoupdate_infofile->read_kimb_one(  'lastcheck' );
		
		if( $lastcheck + 259200 < time() ){
			$donow = true;
		}
		else{
			$donow = false;
		}
	}
	else{
		$donow = true;
	}
	
	if( $donow ){
		$alladdsjson = file_get_contents( 'https://api.kimb-technologies.eu/downloader/module/getall.php' );
		$module_autoupdate_infofile->write_kimb_one( 'module_json', $alladdsjson );
		
		$currvers = file_get_contents( 'https://api.kimb-technologies.eu/downloader/getcurrentversion.php' );
		$currvers = json_decode( $currvers, true);
		$currvers = $currvers['currvers'];
		
		if( compare_cms_vers( $currvers , $allgsysconf['build'] ) == 'newer' ){
			$module_autoupdate_infofile->write_kimb_one( 'sys_update', 'yes' );
			$module_autoupdate_infofile->write_kimb_one( 'sys_newversion', $currvers );
			
			send_mail( $allgsysconf['adminmail'] ,'Hallo Administrator,'."\r\n".'es gibt eine neue Version für den KIMB-Downloader.'."\r\n".'Gehen Sie gleich zu '.$allgsysconf['siteurl'].'/backend.php'."\r\n".$allgsysconf['sitename'] );			
		}
		else{
			$module_autoupdate_infofile->write_kimb_one( 'sys_update', 'no' );
			$module_autoupdate_infofile->write_kimb_one( 'sys_newversion', $currvers );
		}
		
		
		$module_autoupdate_infofile->write_kimb_one( 'lastcheck', time() );
		
		return true;
	}
	else{
		return false;
	}
}

//immer wenn User im Backend nach Updates gucken (Mail wird dann an Admin gesendet!)
if( get_req_url() == '/backend.php' ){
	$module_autoupdate_infofile = new KIMBdbf( 'module/module_autoupdate_infofile.kimb' );
	api_check_for_updates();
}

//Modul herunterladen und installieren
//	$modtodo = ToDo des Moduls (muss in der KIMB-API verfügbar sein)
//	Rückgabe => true/ false (+Ausgaben in $sitecontent)
function get_and_install_module( $modtodo ){
	global $sitecontent, $allgsysconf;
	
	//Link zum Pack bekommen
	//	KIMB-API
	$addwert = json_decode( file_get_contents( 'https://api.kimb-technologies.eu/downloader/module/getcurrentversion.php?module='.$modtodo ) , true );
	
	//Fehler?
	if( $addwert[0]['err'] == 'no' ){
		//URL aus Array
		 $url = $addwert[0]['link'];
		 
		 //Installdatei herunterladen
		//	Name der Installdatei
		$file = __DIR__.'/temp/'.time().'.kimbadd';
		 //	die Update-Datei auf dem Server öffnen
		$src = fopen( $url , 'r');
		//	den Ort für die Update-Datei im Downloader öffnen
		$dest = fopen( $file , 'w+');
		//	Datei herunterladen
		if( !stream_copy_to_stream( $src, $dest ) ){
			//wenn Fehler -> Fehlermeldung
			$sitecontent->echo_error( 'Download des Add-ons nicht möglich!' );
			return false;
		}
		else{ 
			 //Add-on Installieren
			 return install_module( $file );
		}
	}
	else{
		//Fehlermedung, wenn API Fehler ausgibt
		$sitecontent->echo_error( 'Fehler beim Holen der Infos über das Modul!' );
		return false;
	}
}

?>