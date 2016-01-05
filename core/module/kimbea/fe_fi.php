<?php
/*************************************************/
//KIMB Downloader
//Copyright (c) 2016 by KIMB-technologies
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

//Konfiguration laden und Tracking Codes generieren
$eaconffile = new KIMBdbf( 'module/kimbea__conf.kimb' );

//infobanner Daten
$ibinfo = $eaconffile->read_kimb_id( '001' );
//tracker Daten
$trackinfo = $eaconffile->read_kimb_id( '002' );

//Tracking durchführen
//	per Include ??
if( $trackinfo['art'] == 'path' ){
	
	if( is_file( __DIR__ . '/' . $trackinfo['path'] ) ){

		//KIMB_EA Tracking Funktion
		//	wichtig, damit CMS Variablen und EA Variablen unbahängig bleiben!!
		function KIMB_EA_TRACK_DO(){
			//Tracking Infos von Add-on für EA
			global $trackinfo;

			//KIMB-EA Tracker laden
			require_once( __DIR__ . '/' . $trackinfo['path'] );
		
			//Tracken
			KIMB_EA\kimb_ea_track_easy( $trackinfo['siteid'] );
		}
		//Tracking im CMS durchführen
		KIMB_EA_TRACK_DO();
	}
	else{
		$sitecontent->echo_error('Der Tracker für KIMB-EA wurde nicht gefunden!!');
	}
	
}
//	per JS ??
elseif( $trackinfo['art'] == 'url' ){
	
	//JS Code erstellen
	$headercode = '
	<script>
	function KIMB_EA_Track(){
	var tracker = "'.$trackinfo['url'].'";
	var site = '.$trackinfo['siteid'].';
	var path = window.location.pathname+window.location.search;
	var url = tracker + "?site=" + site + "&js&pfad=" + encodeURIComponent( path );
	var request = new XMLHttpRequest();
	request.withCredentials = true;
	if (request) { request.open("GET", url, true); request.send( null ); }
	}
	KIMB_EA_Track();
	</script>
	';
	
	//ausgeben
	$sitecontent->add_html_header( $headercode );
}

//Infobanner?
if( $ibinfo['infobann'] == 'on' ){
		
	//Banner nur anzeigen, wenn Cookie nicht vorhanden, also noch nicht okay
	if( !isset( $_COOKIE['analytics'] ) || ( isset( $_COOKIE['analytics'] ) && $_COOKIE['analytics'] != 'ok' ) ){
		
		//jQuery für OK Button
		$sitecontent->add_html_header( '<!-- jQuery -->' );
		//CSS
		$sitecontent->add_html_header( '<style>'.$ibinfo['ibcss'].'</style>' );
		
		//Banner
		$sitecontent->add_site_content( '<div id="analysehinweis">' );
		//Text
		$sitecontent->add_site_content( $ibinfo['ibtext'] );	
		//Button
		$sitecontent->add_site_content( '<button onclick="$( \'#analysehinweis\' ).css( \'display\' , \'none\' ); document.cookie = \'analytics=ok; path=/;\';">OK</button>' );
		$sitecontent->add_site_content( '</div> ' );
	}
}

?>
