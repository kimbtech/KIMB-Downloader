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

//Fehler, Inhalt, Codierung
error_reporting( 0 );
header('Content-Type: text/html; charset=utf-8');

//Nur mit conf-enable Datei den Konfigurator erlauben, sonst unerlaubte Konfiguration möglich
if( !file_exists ('conf-enable') ){
	//User bitten den Konfigurator zu aktivieren
	echo('<!DOCTYPE html>
<html>
	<head>
		<title>KIMB-Downloader - Installation</title>
		<link rel="shortcut icon" href="load/KIMB.ico" type="image/x-icon; charset=binary">
	</head>
	<body>
		<h1>Error - 403</h1>
		Bitte schalten Sie den Konfigurator frei,
		erstellen Sie eine leere "conf-enable" Datei
		im Downloader-Root-Verzeichnis.
	</body>
</html>');
	die;
}

//sichere Zufallszahlen, neu seit PHP 7
//	$a => Anfang
//	$e => Ende
//	Rückgabe: Nummer
function gen_zufallszahl( $a, $e ){
	
	//neue Funktion von PHP 7 verfügbar?
	if( function_exists( 'random_int') ){
		//nutzen
		return random_int( $a, $e );
	}
	else{
		//alte Zufallszahl nutzen
		return mt_rand( $a, $e );
	}
	
}
//Zufallsstrings erzeugen
//	$laenge => Länge des zu erzeugenden Stings
// 	$chars => Charakter des Stings
function makepassw( $laenge , $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'){	
	//Anzahl der möglichen Charakter bestimmen
	$anzahl = strlen($chars);
	//mit dem ersten Charakter geht es los
	$i = '1';
	//noch keine Ausgabe
	$output = '';
	//solange weniger oder genausoviele Charakter wie gwünscht im Sting weiteren erstellen 
	while($i <= $laenge){
		//Charakter zufällig wählen (Zufallszahl als Stelle für $chars nutzen)
		$stelle = gen_zufallszahl('0', $anzahl);
		//Ausgabe erweitern 
		$output .= $chars{$stelle};
		$i++;
	}
	//Ausgeben
	return $output;
}

//HTML des Konfigurators 
//inkl. Warnung per JS wenn /core/ aufrufbar!
echo('
<!DOCTYPE HTML >
<html>
	<head>
		<title>KIMB-Downloader - Installation</title>
		<link rel="shortcut icon" href="load/KIMB.ico" type="image/x-icon; charset=binary">
		<link rel="icon" href="load/KIMB.ico" type="image/x-icon; charset=binary">
		<style>
			body { 
				background-color:#999999; 
				font-family: Ubuntu, Arial;
				color:#000000;
			}
			#main {
			  	width:800px;
				margin:auto;
				text-align:left;
			  	background-color:#ffffff;
				border: 5px solid #55dd77;
				border-radius:20px;
				padding:20px;
			}
			#wichtig, #wichtig_ex{
				background-color:#ff0000;
				color:#ffffff;
				border-radius:10px;
				padding:30px;
				border:solid 2px orange;

			}
			ul{
				list-style-type:none;
			}
			ul li{
				padding: 5px;
				margin:5px;
				border-radius:15px;
			}
			.err{
				background-color:red;
			}
			.war{
				background-color:orange;
			}
			.okay{
				background-color:lightgreen;
			}
		</style>
		<script language="javascript" src="load/jquery/jquery.min.js"></script>
		<script language="javascript" src="load/hash.js"></script>
		<script>
			$(function() {
				var inhaltfile = "No clean Request";

				$.get( "core/conf/funktionen.php", function( data ) {
					if( data == inhaltfile ){
						$( "#wichtig" ).css( "display" , "block" );
					}
				});

				var examplefile = "Beispieldatei";

				$.get( "files/example.txt", function( data ) {
					if( data.substr(0, 13) == examplefile ){
						$( "#wichtig_ex" ).css( "display" , "block" );
					}
				});
			});
		</script>
	</head>
	<body>

		<div id="main">
			<h1 style="border-bottom:5px solid #55dd77;">KIMB-Downloader - Installation</h1>
			<div style="display:none;" id="wichtig" >
				<b>Achtung:</b>
				<br />
				Das Verzeichnis /core/ und seine Unterverzeichnisse sind nicht gesch&uuml;tzt!
				<br />
				Bitte sperren Sie diese Verzeichnisse f&uuml;r jegliche Browseraufrufe!
			</div>
			<div style="display:none;" id="wichtig_ex" >
				<b>Achtung:</b>
				<br />
				Das Verzeichnis /files/ und seine Unterverzeichnisse sind nicht gesch&uuml;tzt!
				<br />
				Bitte sperren Sie diese Verzeichnisse f&uuml;r jegliche Browseraufrufe!
			</div>
			<br />
');

//Ganz unten bei else{} gehts los!

if($_GET['step'] == '2'){

	//Zufallsgenerator Passwortsalt
	$output = makepassw( 10 );

	//Formular für die Konfiguration
	echo "\r\n\t\t\t".'<h2>Allgemeine Systemeinstellungen</h2>';
	echo "\r\n\t\t\t".'<form method="post" action="configurator.php?step=3" onsubmit="
				if( document.getElementById(\'passw\').value != \'\' && document.getElementById(\'passw\').value == document.getElementById(\'passw_two\').value ){
					document.getElementById(\'passw\').value = SHA1( document.getElementById(\'passw\').value + \''.$output.'\' );
					document.getElementById(\'passw_two\').value == \'\';
					return true;
				}
				else if( document.getElementById(\'passw\').value != document.getElementById(\'passw_two\').value ){
					alert( \'Die Passwörter stimmen nicht überein!\' );
					return false;
				}
				else{
					alert( \'Bitte geben Sie ein Passwort für den Administrator an!\' );
					return false;
				}
			" >';
	echo "\r\n\t\t\t\t".'<input type="text" name="sitename" value="KIMB Downloader" size="60"><br />(Name der Seite)<br /><br />';
	echo "\r\n\t\t\t\t".'<input type="text" name="adminmail" value="downloaderadmin@example.com" size="60"><br />(E-Mail Adresse des Systemadministrators)<br /><br />';
	echo "\r\n\t\t\t\t".'<input type="radio" name="urlrew" value="off">OFF <input type="radio" name="urlrew" value="on" checked="checked">ON (Aktivieren Sie URL-Rewriting f&uuml;r das System [Dazu muss Ihr Server die .htaccess im Rootverzeichnis verwenden k&ouml;nnen oder die Variable $SERVER[REQUEST_URI] setzen.])<br /><br />';

	echo "\r\n\t\t\t\t".'<h2>Administrator einrichten</h2>';
	echo "\r\n\t\t\t\t".'<input type="text" name="user" value="admin" size="60"><br />(Username des Administrators)<br /><br />';
	echo "\r\n\t\t\t\t".'<input type="text" name="name" value="Max Muster" size="60"><br />(Name des Administrators [Nur Kleinbuchstaben von A-Z])<br /><br />';
	echo "\r\n\t\t\t\t".'<input type="password" name="passhash" placeholder="Passwort" id="passw" size="60"><br />';
	echo "\r\n\t\t\t\t".'<input type="hidden" name="salt" value="'.$output.'">';
	echo "\r\n\t\t\t\t".'<input type="password" name="passhash_two" placeholder="Passwort wiederholen" id="passw_two" size="60"><br />(Passwort des Administrators)<br /><hr /><hr />';

	echo "\r\n\t\t\t\t".'<input type="submit" value="Weiter"> <b>Alle Felder m&uuml;ssen gef&uuml;llt sein !!</b><br />';
	echo "\r\n\t\t\t".'</form>';
}

elseif($_GET['step'] == '3'){

	//Alle Felder richtig gefüllt
	if( empty( $_POST['sitename'] ) || empty( $_POST['adminmail'] ) || empty( $_POST['urlrew'] ) || empty( $_POST['user'] ) || empty( $_POST['name'] ) || empty( $_POST['passhash'] ) || empty( $_POST['salt'] ) ){

		echo( "\r\n\t\t\t".'<h1 style="color:red;">Alle Felder m&uuml;ssen gef&uuml;llt sein !!</h1>
			<br />
			<br />' );
		echo( "\r\n\t\t\t".'<a href="configurator.php?step=2" >Zur&uuml;ck</a>' );
		echo( "\r\n\t\t".'</div>' );
		echo( "\r\n\t".'</body>' );
		echo( "\r\n".'</html>');
		die;
	}


	//Request URL
	if(isset($_SERVER['HTTPS'])){
		$urlg = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	else{
		$urlg = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	$url = substr($urlg, '0', '-'.strlen(strrchr($urlg, '/')));

	//Zufallsgenerator Loginokay
	$output = makepassw( 50 );

	//Konfigurationsteile
	//erster
	$addconf = '<[001-sitename]>'.$_POST['sitename'].'<[001-sitename]>
<[001-sitefavi]>'.$url.'/load/KIMB.ico<[001-sitefavi]>
<[001-loginokay]>'.$output.'<[001-loginokay]>
<[001-siteurl]>'.$url.'<[001-siteurl]>
<[001-adminmail]>'.$_POST['adminmail'].'<[001-adminmail]>
<[001-mailvon]>downloader@'.$_SERVER['HTTP_HOST'].'<[001-mailvon]>
<[001-urlrewrite]>'.$_POST['urlrew'].'<[001-urlrewrite]>';

	//Schreibe in Konfigurationsdatei
	$handle = fopen(__DIR__.'/core/oop/kimb-data/config.kimb', 'a+');
	fwrite($handle, $addconf);
	fclose($handle);

	//.htaccess für URL-Rewriting umbenennen
	if( $_POST['urlrew'] == 'on' ){
		rename( __DIR__.'/_.htaccess', __DIR__.'/.htaccess' );
	}

	//zweiter
	$adduser = '<[name]>'.$_POST['name'].'<[name]>
<[username]>'.$_POST['user'].'<[username]>
<[passhash]>'.$_POST['passhash'].'<[passhash]>
<[systemsalt]>'.$_POST['salt'].'<[systemsalt]>';

	//schreiben in Userdatei
	$handle = fopen(__DIR__.'/core/oop/kimb-data/beuser.kimb', 'a+');
	fwrite($handle, $adduser);
	fclose($handle);

	//fertig anzeigen
	echo( "\r\n\t\t\t".'Installation erfolgreich!
			<br />
			<br />
			<a href="'.$url.'/" target="_blank"><button>Zur Seite</button></a>
			<br />');
	echo( "\r\n\t\t\t".'<a href="'.$url.'/backend.php" target="_blank"><button>Zum Backend</button></a>
			<br />');
	
	echo( "\r\n\t\t\t".'<hr />' );
	echo( "\r\n\t\t\t".'<h2>KIMB-technologies Register</h2>' );
	echo( "\r\n\t\t\t".'Registrieren Sie sich im KIMB-technologies Register und bleiben Sie auf dem Laufenden.<br />' );
	echo( "\r\n\t\t\t".'<a href="https://register.kimb-technologies.eu/" target="_blank">Zum Register</a>' );
	echo( "\r\n\t\t\t".'<hr />' );

	//Konfigurator sperren
	unlink('conf-enable');

}
else{
	
	echo "\r\n\t\t\t".'<h2>Serverprüfung</h2>';
	echo "\r\n\t\t\t".'<ul>';
	
	//PHP - Version OK?
	if (version_compare(PHP_VERSION, '7.0.0' ) >= 0 ) {
    		echo "\r\n\t\t\t\t".'<li class="okay">Sie verwenden PHP 7</li>';
		$okay[] = 'okay';
	}
	elseif (version_compare(PHP_VERSION, '5.5.0' ) >= 0 ) {
    		echo "\r\n\t\t\t\t".'<li class="war">Sie verwenden PHP 5.5.0, aber noch nicht das neue PHP 7</li>';
		$okay[] = 'war';
	}
	else{
		echo "\r\n\t\t\t\t".'<li class="err">Dieses System wurde f&uuml;r PHP 5.5.0 und h&ouml;her entwickelt, bitte f&uuml;hren Sie ein PHP-Update durch!</li>';
		$okay[] = 'err';
	}
	
	//url fopen okay?
	if( ini_get( 'allow_url_fopen' ) ){
		$okay[] = 'okay';
		echo "\r\n\t\t\t\t".'<li class="okay">Ihr Server erlaubt PHP Requests per HTTP zu anderen Servern!</li>';
	}
	else{
		$okay[] = 'war';
		echo "\r\n\t\t\t\t".'<li class="war">Ihr Server erlaubt PHP keine Requests per HTTP zu anderen Servern!</li>';
	}
	
	//PHP GD
	if (defined('GD_VERSION')) {   
		$okay[] = 'okay';
		echo "\r\n\t\t\t\t".'<li class="okay">Ihr Server hat PHP_GD!</li>';
	}
	else{
		$okay[] = 'war';
		echo "\r\n\t\t\t\t".'<li class="war">Ihrem Server fehlt PHP_GD!</li>';
	}
	//nötige schreibbare Verzeichnisse und Dateien
	$checkfolders = array(
		'core/oop/kimb-data',
		'core/oop/kimb-data/beuser.kimb',
		'core/oop/kimb-data/config.kimb',
		'core/oop/kimb-data/index.kimb',
		'core/oop/kimb-data/sonder.kimb',
		'core/oop/kimb-data/readme',
		'core/oop/kimb-data/readme/folderlist.kimb',
		'core/oop/kimb-data/readme/folder_1.kimb',
		'core/oop/kimb-data/title',
		'core/oop/kimb-data/title/folderlist.kimb',
		'core/oop/kimb-data/title/folder_1.kimb',
		'core/module',
		'core/module/modules_list.json',
		'core/theme',
		'core/theme/output_site_norm.php',
		'core/theme/output_menue_norm.php',
		'load/system/theme',
		'load/system/theme/design.css',
		'conf-enable'
	 );

	//alle Verzeichnisse testen und Fehler bzw. $count++
	$count = 0;
	foreach( $checkfolders as $folder ){

		if( is_writable( __DIR__.'/'.$folder ) ){
			$count++;
		}
		else{
			echo "\r\n\t\t\t\t".'<li class="err">"'.$folder.'" ist nicht schreibbar!</li>';
		}
	}
	
	//Hat count den richtigen Wert, dann alles okay
	if($count == count( $checkfolders ) ){
		echo "\r\n\t\t\t\t".'<li class="okay">Alle benötigten Verzeichnisse sind schreibbar!</li>';
		$okay[] = 'okay';
	}
	else{
		$okay[] = 'err';
	}

	echo "\r\n\t\t\t".'</ul>';

	//okay auswerten
	//wiederholen oder weiter zu Schritt 2
	if( array_search ('err' , $okay ) === false && array_search ('war' , $okay ) === false ){
		echo( "\r\n\t\t\t".'<ul>
				<li class="okay">
					Alle Bedingungen für den KIMB-Downloader sind erfüllt!
					<br />
					<br />');
		
		echo( "\r\n\t\t\t\t\t".'<a href="configurator.php?step=2">
						<button>Weiter</button>
					</a>
				</li>
			</ul>');
	}
	elseif( array_search ('err' , $okay ) === false ){
		echo( "\r\n\t\t\t".'<ul>
					<li class="war">
						Die grundlegenden Bedingungen für den KIMB-Downloader sind erfüllt, es könnte aber zu Problemen kommen!
					<br />
					<br />');
		
		echo( "\r\n\t\t\t\t\t".'<a href="configurator.php?step=2">
						<button>Weiter</button>
					</a>
					</br />');
		echo( "\r\n\t\t\t\t\t".'<a href="configurator.php">
						<button>Neue Systemprüfung</button>
					</a>
				</li>
			</ul>');
	}
	else{
		echo( "\r\n\t\t\t".'<ul>
				<li class="err">
					Die grundlegenden Bedingungen für den KIMB-Downloader sind nicht erfüllt!
					<br />
					<br />');
		
		echo( "\r\n\t\t\t\t\t".'<a href="configurator.php">
						<button>Neue Systemprüfung</button>
					</a>
				</li>
			</ul>');
	}

}
echo( "\r\n\t\t".'</div>
	</body>
</html>');
?>
