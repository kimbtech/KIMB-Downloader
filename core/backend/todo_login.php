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

$sitecontent->add_site_content( '<h1>Login</h1>' );

$beuserfile = new KIMBdbf( 'beuser.kimb' );
//Username, Name des Users
//Passwort sha1( PW."--".SALT) und Systemsalt (wird immer mit im HTML ausgegeben)
//direkt über Downloader on/off [waydow] 
//Daten für API Login

//*************
//*************
//API Login eines CMS Anbindung einbauen!!!!!
//*************
//*************

$htmlcode_drin = '<h2>Home</h2>';
$htmlcode_drin .= 'Sie haben sich erfolgreich im KIMB-Downloader Backend angemeldet!';
$htmlcode_drin .= '<br /><br />';
$htmlcode_drin .= '<a id="startbox-x" href="'.$allgsysconf['siteurl'].'/backend.php?todo=explorer"><span id="startbox"><b>Explorer</b><br /><span class="ui-icon ui-icon-folder-open"></span><br /><i>Dateien hochladen und verwalten</i></span></a>';
$htmlcode_drin .= '<a id="startbox-x" href="'.$allgsysconf['siteurl'].'/backend.php?todo=infos"><span id="startbox"><b>Infos</b><br /><span class="ui-icon ui-icon-info"></span><br /><i>Infotexte erstellen und verändern</i></span></a>';

//Will sich der User ausloggen?
if( isset( $_GET['logout'] ) ){
	
	if( $_SESSION['way'] == 'api' ){
		$loginwasextern = true;
	}
	else{
		$loginwasextern = false;
	}

	//Die falschen Loginversuche sollen bleiben
	$loginfehler = $_SESSION["loginfehler"];
	//Session leeren
	session_unset();
	//Session zerstören
	session_destroy();
	//Session neu aufesetzen
	//	jetzt ist alles, was mit dem User zu tun hatte weg
	session_start();
	//Wie gesagt, die falschen Loginversuche bleiben 
	$_SESSION["loginfehler"] = $loginfehler;	

	//Hinweis, dass User ausgeloggt
	$sitecontent->echo_message('Sie wurden ausgeloggt!', 'Auf Wiedersehen');
	
	//API Login?
	if( $loginwasextern ){
		//Zum externen Login weiterleiten
		open_url( $beuserfile->read_kimb_one( 'api_sysurl' ), 'outsystem' );
		die;
	}
}

//Login direkt am Downloader
if( check_backend_login( false ) ){
	$sitecontent->add_site_content( $htmlcode_drin );
}
elseif( $beuserfile->read_kimb_one( 'waydow' ) == 'on' ){
	if( isset( $_GET['make_session'] ) ){
		$_SESSION['loginokay'] = $allgsysconf['loginokay'];
		$_SESSION["ip"] = $_SERVER['REMOTE_ADDR'];
		$_SESSION["useragent"] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION["name"] = 'Herr Mustermann';
		$_SESSION["user"] = 'hm';
		$_SESSION["way"] = 'dow';
		
		$sitecontent->add_site_content( $htmlcode_drin );
	}
	else{
		$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=login&make_session">Einloggen</a>');
	}
}
else{
	$sitecontent->add_site_content( '<br />');
	$sitecontent->add_site_content( '<br />');
	$sitecontent->echo_message( '<br /><b>Sie können sich hier nicht direkt einloggen!</b><br />Bitte verwenden Sie dieses Login: <br /><center><a href="'.$beuserfile->read_kimb_one( 'api_sysurl' ).'"><button>Zum Login</button></a></center><br />', 'Externes Login aktiviert' );
}

?>
