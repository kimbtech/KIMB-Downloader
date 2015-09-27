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

$sitecontent->add_html_header( '<script> $( function () { $( "div#jsinfo" ).css( "display", "none" ); }); </script>');
$sitecontent->add_site_content( '<div id="jsinfo" style="text-align:center; font-size:1.8em; color:#f00;"><hr />Das Backend benötigt JavaScript!<br />Bitte aktivieren Sie dieses!<hr /></div>');

//Userdaten (Downloader interner Nutzer und Daten für externes Login )
$beuserfile = new KIMBdbf( 'beuser.kimb' );

//externes Login?
if( isset( $_GET['id'] ) || isset( $_POST['auth'] ) ){
	
	//Abänbderung des Codes des KIMB-CMS Add-ons "API_Login" zur Nutzung mit dem Downloader
	//	Code GPLv3 by KIMB-technologies.eu
	
	//CMS überträgt Inhalt der Session?
	if( $_POST['auth'] == $beuserfile->read_kimb_one( 'api_auth' ) ){
	
		$cont = $beuserfile->read_kimb_one( 'api_jsons' );

		$arr = json_decode( $cont, true );
		 $addval = json_decode( $_REQUEST['jsondata'], true );
		 $addval['time'] = time();
		 
		 $arr[] = $addval;
	
		$beuserfile->write_kimb_one( 'api_jsons', json_encode ( $arr ) );
	
		echo 'taken';
		die;
	}
	//Ist es ein User? (hat er eine ID einer Session in der JSON)
	elseif( isset( $_GET['id'] ) ){
	
		$cont = $beuserfile->read_kimb_one( 'api_jsons' );
		//JSON Daten parsen
		$arr = json_decode( $cont, true );
	
		//alle Daten durchgehen
		foreach( $arr as $user ){
			//passt die ID des Users zu der aktuellen Session?
			if( $user['id'] == $_GET['id'] && $user['time'] + 120 > time() ){
				
				$cmsgr = $beuserfile->read_kimb_one( 'api_gruppe' );
				
				if( $cmsgr == $user['gr'] ){
					$_SESSION['loginokay'] = $allgsysconf['loginokay'];
					$_SESSION["ip"] = $_SERVER['REMOTE_ADDR'];
					$_SESSION["useragent"] = $_SERVER['HTTP_USER_AGENT'];
					$_SESSION["name"] = $user['na'];
					$_SESSION["user"] = $user['us'];
					$_SESSION["way"] = 'api';
					
					$sitecontent->echo_message( 'Sie haben sich erfolgreich von einem externen System eingeloggt!!', 'Wilkommen' );
				}
				else{
					$sitecontent->echo_error( 'Sie haben nicht die nötigen Rechte um auf den Downloader zuzugreifen!!' );
				}
			}
			elseif( $user['time'] + 120 > time() ){
				//wenn nicht, Daten für später aufheben
				$newarr[] = $user;
			}
		}
	
		$beuserfile->write_kimb_one( 'api_jsons', json_encode ( $newarr ) );
	}
}

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

if( !isset( $_SESSION['loginfehler']) ){
	$_SESSION['loginfehler'] = 0;
}

//Login direkt am Downloader
if( check_backend_login( false ) ){
	$sitecontent->add_site_content( $htmlcode_drin );
}
elseif( $beuserfile->read_kimb_one( 'waydow' ) == 'on' ){
	if( !empty( $_POST['login_user'] ) && !empty( $_POST['login_pass'] ) ){
		
		$loginerr = false;
		
		$username = preg_replace( "/[^a-z]/" , "" , strtolower( $_POST['login_user'] ) );
		$password = preg_replace( "/[^A-Z0-9a-z]/" , "" , strtolower( $_POST['login_pass'] ) );
		
		if( $username == $beuserfile->read_kimb_one( 'username' ) && $_SESSION['loginfehler'] <= 6 ){
			
			$dbfpass = $beuserfile->read_kimb_one( 'passhash' );
			$passhash = sha1( $dbfpass.$_SESSION['randcode'] );
			
			unset( $_SESSION['randcode'] );
			
			if( $password == $passhash ){
				$_SESSION['loginokay'] = $allgsysconf['loginokay'];
				$_SESSION["ip"] = $_SERVER['REMOTE_ADDR'];
				$_SESSION["useragent"] = $_SERVER['HTTP_USER_AGENT'];
				$_SESSION["name"] = $beuserfile->read_kimb_one( 'name' );
				$_SESSION["user"] = $beuserfile->read_kimb_one( 'username' );
				$_SESSION["way"] = 'dow';
				
				$sitecontent->add_site_content( $htmlcode_drin );
			}
			else{
				$loginerr = true;
			}
		}
		else{
			$loginerr = true;
		}
		
		if( $loginerr ){
		
			$_SESSION['loginfehler']++;
		
			$sitecontent->echo_error( 'Ihre Logindaten stimmen nicht, bitte verusuchen Sie es erneut!!', 'unknown', 'Fehlerhafte Logindaten' );
			$sitecontent->add_site_content( '<br />');
			$sitecontent->add_site_content( 'Sie haben nur 6 Versuche, dies ist schon der '.$_SESSION['loginfehler'].'. Versuch!!');
			$sitecontent->add_site_content( '<br />');
			$sitecontent->add_site_content( '<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=login">&larr; Erneut versuchen</a>');
			
		}
		
		
	}
	else{		
		$sitecontent->add_site_content( '<br />');
		$sitecontent->echo_message( 'Bitte loggen Sie sich ein!' );
		$sitecontent->add_site_content( '<br />');
		if( !empty( $beuserfile->read_kimb_one( 'api_auth') ) ){
			$sitecontent->echo_message( 'Sie können auch <a href="'.$beuserfile->read_kimb_one( 'api_sysurl' ).'">dieses Login</a> verwenden.' );
			$sitecontent->add_site_content( '<br />');
		}
		
		$_SESSION['randcode'] = makepassw( 75, '', 'numaz' );
		
		$formhtml = '	<input type="text" name="login_user" id="login_user" placeholder="Username" autofocus="autofocus"><br />' ;
		$formhtml .='	<input type="password" name="login_pass"  id="login_pass" placeholder="Passwort"><br />' ;
		$formhtml .='	<input type="submit" value="Login"><br />' ;
		
		$sitecontent->add_html_header( '<script>' );
		$sitecontent->add_html_header( '	var randcode = "'.$_SESSION['randcode'].'";
	$( function () { $( "form#loginform_down_be" ).html( '.json_encode( $formhtml ).' ); });
	var salt = "'.$beuserfile->read_kimb_one( 'systemsalt' ).'";
	function makeloginhash () {
		var login_pass = $( "input#login_pass" ).val(), passhash;
		passhash = SHA1( login_pass + salt );
		passhash = SHA1( passhash + randcode );
		$( "input#login_pass" ).val( passhash );
		return true;
	}');
		$sitecontent->add_html_header( '</script>' );
		
		$sitecontent->add_site_content( '<h2>Formular</h2>' );
		$sitecontent->add_site_content( '<form action="'.$allgsysconf['siteurl'].'/backend.php?todo=login" method="post" id="loginform_down_be" onsubmit="return makeloginhash();">' );
		$sitecontent->add_site_content( '	<h3>Sie benötigen JavaScript für das Login!</h3>' );
		$sitecontent->add_site_content( '</form>' );
	}
}
else{
	$sitecontent->add_site_content( '<br />');
	$sitecontent->add_site_content( '<br />');
	$sitecontent->echo_message( '<br /><b>Sie können sich hier nicht direkt einloggen!</b><br />Bitte verwenden Sie dieses Login: <br /><center><a href="'.$beuserfile->read_kimb_one( 'api_sysurl' ).'"><button>Zum Login</button></a></center><br />', 'Externes Login aktiviert' );
}

?>