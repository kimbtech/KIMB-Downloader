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

$sitecontent->add_site_content( '<h1>User</h1>' );

//Login prüfen
check_backend_login( true, true );

//Auf der Seite Konfiguration wird oben eine Liste mit den verschiedenen Möglichkeiten anzeigt
//	laden der Buttons
$sitecontent->add_html_header('<script> $(function() { $( "a#dohinw" ).button(); }); </script>');
//	HTML Code
$sitecontent->add_site_content('<center><br />
<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=user&amp;beuser" id="dohinw" title="Den Backend User bearbeiten">Backend User</a>
<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=user&amp;api" id="dohinw" title="Einstellungen für das externe Login von einem CMS verändern">API Login</a>
</center><br /><hr /><br />');

if( !is_object( $beuser ) ){
	$beuser = new KIMBdbf('beuser.kimb');
}

if( isset( $_GET['beuser'] ) ){
	$todos = array(
		'waydow' => 'Login direkt über den Downloader aktivieren? (Achtung, stellen Sie sicher, dass das externe Login funktioniert bevor Sie diesen Wert ändern!!)',
		'name' => 'Name des Users, der sich direkt im Downloader anmelden kann.',
		'username' => 'Username des Users, der sich direkt im Dwnloader anmelden kann.',
		'passhash' => 'Passwort des Users, der sich direkt im Downloader anmelden kann.',
		 'systemsalt' => 'Passwortsalt des Users, der sich direkt im Downloaders anmelden kann.'
	);
	
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/backend.php?todo=user&amp;beuser" method="post">');
}
elseif( isset( $_GET['api'] ) ){
	$todos = array(
		'api_auth' => 'Auth Code des CMS für externes Login',
		'api_sysurl' => 'URL zum externen Login',
		'api_gruppe' => 'Gruppe der externen User (&lt;api_gruppe&gt;&apos;xxadmin&apos; für Backend-Administratoren und &lt;api_gruppe&gt;&apos;xxuser&apos; für Backend-User; Gruppe muss in Felogin im CMS definiert sein!!) (Achtung, nach einer Änderung müssen Sie sich unter Umständen neu einloggen!!)',
		'api_jsons' => 'Daten der Sessions vom CMS übergeben (wird automatisch verwaltet!)'
	);
	
	$sitecontent->add_site_content('<br />');
	$message = 'Die Daten für die folgenden Felder erfahren Sie in Backend des KIMB-CMS, welches zum externen Login verwendet werden soll!<br />';
	$message .= '<i>(Sie müssen die Add-ons Felogin und API Login im CMS installiert haben!)</i><br />';
	$message .= '<ul>';
	$message .= '<li><b>api_auth:</b> Bitte fügen Sie ein externes System im CMS-Backend unter Add-ons &rarr; Konfiguration &rarr; api_login hinzu. Geben Sie das externe Ziel "<code>'.$allgsysconf['siteurl'].'/backend.php</code>" an. Laden Sie anschließend die Datei herunter und öffnen Sie diese. Oben finden Sie unter Daten <code>$auth = &apos;...</code>. Kopieren Sie diesen Wert ohne &apos; in das erste Textfeld auf dieser Seite.</li>';
	$message .= '<li><b>api_sysurl:</b> Geben Sie hier die URL einer Seite des CMS an, auf welcher das Loginformular vorhanden ist.</li>';
	$message .= '<li><b>api_gruppe:</b> Die Gruppen der Feloginuser müssen einer bestimmten Syntax folgen. (&lt;api_gruppe&gt;&apos;xxadmin&apos; für Backend-Administratoren und &lt;api_gruppe&gt;&apos;xxuser&apos; für Backend-User)</li>';
	$message .= '</ul><br />';
	$message .= 'Lassen Sie <code>api_auth</code> leer um das externe Login zu deaktivieren!';
	$sitecontent->echo_message( $message , 'Verbindungsdaten' );
	$sitecontent->add_site_content('<br />');
	
	$sitecontent->add_site_content('<form action="'.$allgsysconf['siteurl'].'/backend.php?todo=user&amp;api" method="post">');

}
else{
	//nichts gewünscht
	$sitecontent->add_site_content('Bitte wählen Sie oben einen Bereich aus, den Sie angezeigt bekommen wollen.');
}

if( is_array( $todos ) ){
	foreach( $todos as $todo => $info ){
		
		$attributes = '';
		$medlung = '';
		
		$value = $beuser->read_kimb_one( $todo );
		
		if( !empty( $_POST['change'] ) && isset( $_POST[$todo] ) ){
			if( $value != $_POST[$todo] ){
				if( $beuser->write_kimb_one( $todo, $_POST[$todo] ) ){
					$medlung .= ' <span style="color:red">Änderung durchgeführt!</span>';
					$value = $_POST[$todo];
				}
			}	
		}
		
		if( $todo == 'api_jsons' ||  $todo == 'systemsalt' ||  $todo == 'passhash' ){
			$attributes = ' readonly="readonly" ';
		}
		
		if( $todo == 'passhash' ){
			$sitecontent->add_html_header( '	<script>
		function makesalt() {
			var text = "", possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
			for( var i=0; i < 15; i++ ){ text += possible.charAt(Math.floor(Math.random() * possible.length)); }
			return text;
		}
				
		function setnewpass(){
			var salt = makesalt();
			$( "input[name=systemsalt]" ).val( salt );
		
			var one = $( "input#passw_one" ).val();
			
			var newhash = SHA1( one + salt );
			
			$( "input[name=passhash]" ).val( newhash );
			
			$( "div#passwched" ).css( "display", "block" );
			$( "div#passwforms" ).css( "display", "none" );
			
			$( "input#passw_one" ).val( "" );
			$( "input#passw_two" ).val( "" );
		
			return true;
		}
				
		function checkgleich(){
			var one = $( "input#passw_one" ).val();
			var two = $( "input#passw_two" ).val();
			
			if( one == "" || two == "" ){
				$( "span#passindicator_ok" ).css( "display", "none" );
				$( "span#passindicator_nok" ).css( "display", "none" );
				return false;
			}
			
			if( one == two ){
				$( "span#passindicator_ok" ).css( "display", "block" );
				$( "span#passindicator_nok" ).css( "display", "none" );
				return true;
			}
			else{
				$( "span#passindicator_ok" ).css( "display", "none" );
				$( "span#passindicator_nok" ).css( "display", "block" );
				return false;
			}
		}
				
		function changepassw() {
			$( "#passwdialog" ).css( "display", "block" );
			$( "#passwdialog" ).dialog({
				resizable: false,
				height:200,
				modal: true,
				buttons: {
					"Passwort ändern": function() {
						if( checkgleich() ){
							setnewpass();
							$( this ).dialog( "option", "buttons", [ { text: "Schließen", click: function() { $( this ).dialog( "close" ); } } ] );
						}
						
					 },
					"Abbrechen": function() {
						$( this ).dialog( "close" );
					}
				}
			});
			return false;
		}		
	</script>');
			
			$sitecontent->add_site_content('<hr /><center><button onclick="return changepassw();">Passwort ändern</button></center><br /><br />');
			
			$sitecontent->add_site_content('<div id="passwdialog" title="Passwort ändern" style="display:none;">');
			$sitecontent->add_site_content('<div id="passwforms">');
			$sitecontent->add_site_content('<input type="password" id="passw_one" onkeyup="checkgleich();" onchange="checkgleich();" placeholder="Passwort"><br />');
			$sitecontent->add_site_content('<input type="password" id="passw_two" onkeyup="checkgleich();" onchange="checkgleich();" placeholder="Passwort wiederholen"><br />');
			$sitecontent->add_site_content('<span id="passindicator_nok" style="display:none; color:red;">Die Passwörter stimmen nicht überein!</span>');
			$sitecontent->add_site_content('<span id="passindicator_ok" style="display:none; color:green;">Die Passwörter stimmen überein!</span>');
			$sitecontent->add_site_content('</div>');
			$sitecontent->add_site_content('<div id="passwched" style="display:none;">');
			$sitecontent->add_site_content('<b>Das Passwort wurde geändert!</b><br />Bitte schließen Sie diesen Dialog und klicken Sie unten auf der Seite auf "<u>Ändern</u>"!!');
			$sitecontent->add_site_content('</div>');
			$sitecontent->add_site_content('</div>');
		}
		
		if( $todo == 'waydow' ){
			$sitecontent->add_html_header( '<script>$(function () { $("select[name='.$todo.'] option[value='.$value.']").attr("selected",true); });</script>');
			
			$sitecontent->add_site_content('<select name="'.$todo.'">');
			$sitecontent->add_site_content('<option value="on">aktiv</option>');
			$sitecontent->add_site_content('<option value="off">inaktiv</option>');
			$sitecontent->add_site_content('</select>');
		}
		else{
			$sitecontent->add_site_content('<input type="text" style="width:90%;" value="'.$value.'" placeholder="'.$todo.'" name="'.$todo.'" '.$attributes.'>');
		}
		$sitecontent->add_site_content('<span class="ui-icon ui-icon-info" style="display:inline-block;" title="'.$info.'" ></span><br />');
		$sitecontent->add_site_content( '<code>'.$todo.'</code>'.$medlung.'<br />');
		$sitecontent->add_site_content('<br />');
	}
	
	$sitecontent->add_site_content('<input type="hidden" value="yes" name="change">');
	$sitecontent->add_site_content('<input type="submit" value="Ändern"></form>');
}
?>
