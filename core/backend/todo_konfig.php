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

$sitecontent->add_site_content( '<h1>Konfiguration</h1>' );

//Login prüfen
check_backend_login( true, true );

//Auf der Seite Konfiguration wird oben eine Liste mit den verschiedenen Möglichkeiten anzeigt
//	laden der Buttons
$sitecontent->add_html_header('<script> $(function() { $( "a#dohinw" ).button(); }); </script>');
//	HTML Code
$sitecontent->add_site_content('<center><br />
<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=konfig&amp;konf" id="dohinw">Systemkonfiguration</a>
<a href="'.$allgsysconf['siteurl'].'/backend.php?todo=konfig&amp;cont" id="dohinw">Inhalte</a>
</center><br /><hr /><br />');


if( isset( $_GET['konf'] ) ){
	
	$info = array(
		'sitename' => 'Name der Seite' ,
		'sitefavi' => 'URL zum Favicon' ,
		'loginokay' => 'Zufälliger Code um Login zu verifizieren' ,
		'siteurl' => 'GrundURL der Seite (ohne / am Ende)' ,
		'urlweitermeth' => 'PHP header (1) oder Meta Refresh (2) für Weiterleitungen' ,
		'adminmail' => 'E-Mail Adresse des Administrators' ,
		'robots' => 'Meta-Robots-Tag für Homepage' ,
		'mailvon' => 'E-Mail Absender des Systems' ,
		'sitespr' => 'aktuell nicht verwendet, später Sprache des Frontends' ,
		'systemversion' => 'Version des CMS' ,
		'build' => 'Genaue Version des Systems, wichtig für Updates,... (Beispiele: V1.0F-p0 -> Version 1.0 Final Patch 0 // V0.7B-p4,5 -> Version 0.7 Beta Patch 4 und 5) (entspricht GIT Tags)' ,
		'urlrewrite' => 'on / off des URL-Rewritings (on ist empfehlenswert)' ,
		'use_request_url' => 'Für URL-Rewriting muss der Request entweder an /index.php?url=xxx gesendert werden oder per $SERVER[REQUEST_URI] verfügbar sein. Letzteres kann hier verboten werden, da es auf manchen Server zu Problemen führen könnte. (ok/ nok)' ,
		'theme' => 'Wählen Sie ein installiertes Thema für Ihre Seite, ohne oder mit falschem Parameter wird das Standardthema verwendet. (Dieser Wert wird automatisch bei einer Themeninstallation geändert.)',
		'coolifetime' => 'Lebenszeit des Session Cookies in Sekunden, 0 -> bis Browser geschlossen wird'
	);
	
	if( !is_object( $conffile ) ){
		$conffile = new KIMBdbf('sonder.kimb');
	}
	
	//Teil löschen?
	if ( isset( $_GET['del'] ) && !empty( $_GET['teil'] ) ){
		
		//Löschen des Teils duchführen, Meldung wenn okay
		if( $conffile->write_kimb_id( '001' , 'del' , $_GET['teil'] ) ){
			$sitecontent->echo_message( 'Der Parameter "'.$_GET['teil'].'" wurde aus der Konfiguration entfernt!' );
		}
	}
		
	//Teile verändern?
	if ( isset( $_POST['1'] ) ){
		//Daten nacheinader durchgehen 
			//	Post Werte beginned ab 1 nummeriert
			//		[Nummer] => Name des Konfigurationswertes
			//		[Nummer-wert] => Inhalt des Konfigurationswertes
			
		//alle durchgehen
		$i = 1;
		while( isset( $_POST[$i] ) ){
			//Wert verändert?
			if( $_POST[$i.'-wert'] != $allgsysconf[$_POST[$i]] ){
				//Wert anpassen
				if( $conffile->write_kimb_id( '001' , 'add' , $_POST[$i] , $_POST[$i.'-wert'] ) ){
					//Meldung, wenn Änderung erfolgreich
					$sitecontent->echo_message( 'Der Parameter "'.$_POST[$i].'" wurde in der Konfiguration geändert!' );
				}
			}
		$i++;
		}
		
		//Konfiguration neu laden
		$allgsysconf = $conffile->read_kimb_id('001');
		
	}
		
	//Dialog zum löschen von Inhalten
	$sitecontent->add_html_header('<script>
	function deldialog( teil ) {
		$( "#deldialog-konf" ).show( "fast" );
		$( "#deldialog-konf" ).dialog({
		resizable: false,
		height: 250,
		modal: true,
		buttons: {
			"Delete": function() {
				$( this ).dialog( "close" );
				window.location = "'.$allgsysconf['siteurl'].'/backend.php?todo=konfig&konf&del&teil=" + teil;
				return true;
			},
			Cancel: function() {
				$( this ).dialog( "close" );
				return false;
			}
		}
		});
	}
	</script>');
		
	//Dialoginhalt
	$sitecontent->add_site_content('<div style="display:none;"><div id="deldialog-konf" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>Möchten Sie den Wert wirklich löschen?<br />Tun Sie dies nur wenn Sie genau wissen was Sie tun!!</p></div></div>');
	
	$sitecontent->add_site_content('<h2>Systemkonfiguration</h2>');
		
	//alle Namen der Konfigurationswerte lesen
	$confteile = $conffile->read_kimb_all_xxxid('001');
		
	//Eingabeformular beginnen
	$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/backend.php?todo=konfig&amp;konf">');
	$sitecontent->add_site_content('<table width="100%" ><tr><th>Name</th><th>Wert</th><th width="20px;">Löschen</th><th width="20px;">Info</th></tr>');
		
	//für Nummerierung zählen
	$i = 1;
	//alle Teile durchgehen
	foreach( $confteile as $confteil ){
		
		//Gibt es eine Erklärung zum Teil?
		if( isset( $info[$confteil] ) ){
			//Erklärung anzeigen
			$infotab = '<span class="ui-icon ui-icon-info" title="'.$info[$confteil].'"></span>';
		}
		else{
			$infotab = '';
		}
		
		//Werte anzeigen
			
		if( $confteil == 'systemversion' || $confteil == 'build' ){
			//Bei Version und Build keine Änderung zulassen
			$sitecontent->add_site_content('<tr><td><input type="text" readonly="readonly" value="'.$confteil.'" name="'.$i.'"></td><td><input type="text" readonly="readonly" value="'.$allgsysconf[$confteil].'" name="'.$i.'-wert"></td><td><span><span class="ui-icon ui-icon-trash" title="Löschen nicht erlaubt!"></span></span></td><td>'.$infotab.'</td></tr>');
		}
		else{
			//alle anderen normal als input
			$sitecontent->add_site_content('<tr><td><input type="text" value="'.$confteil.'" name="'.$i.'"></td><td><input type="text" value="'.$allgsysconf[$confteil].'" name="'.$i.'-wert"></td><td><span onclick="deldialog( \''.$confteil.'\' );" style="display:inline-block;" ><span class="ui-icon ui-icon-trash" title="Diesen Wert löschen."></span></span></td><td>'.$infotab.'</td></tr>');
		}
		$i++;
	
	}
	//neuen Wert hinzufügen input
	$sitecontent->add_site_content('<tr><td><input type="text" placeholder="hinzufügen" name="'.$i.'"></td><td><input type="text" placeholder="hinzufügen" name="'.$i.'-wert"></td><td></td><td><span class="ui-icon ui-icon-info" title="Fügen Sie einen eigenen Wert in die allgemeine Konfiguration ein."></span></td></tr>');
	//Button
	$sitecontent->add_site_content('</table><input type="submit" value="Ändern"></form>');
}
elseif( isset( $_GET['cont'] ) ){
	//Überschrift
	$sitecontent->add_site_content('<h2>Inhalte</h2>');
	
	//Datei mit Inhalten laden
	$sonderfile = new KIMBdbf('sonder.kimb');
	
	//JavaScript für CodeMirror
	$id[] = array( 'id' => 'footer', 'mode' => 'text/html' );
	$id[] = array( 'id' => 'error_404', 'mode' => 'text/html' );
	$id[] = array( 'id' => 'htmlheader', 'mode' => 'text/html' );
	add_codemirror( $id );
	
	//JS Bibilotheken hinzufügen
	$sitecontent->add_html_header('	<script>
	$( function() {
		$( "#libs" ).on( "change", function() {
			var valadd, valold, valnew;
			valadd = $( "#libs" ).val();
			valold = mirrorid_htmlheader.getValue();
			valnew = valold + valadd;
			mirrorid_htmlheader.setValue( valnew );
			return false;
		});
	});
	</script>');

	//Eingabeformular beginnen
	$sitecontent->add_site_content('<form method="post" action="'.$allgsysconf['siteurl'].'/backend.php?todo=konfig&amp;cont">');
	
	//Anpassungen
	if( !empty( $_POST['change'] ) ){
		$dos = array( 'footer','error-404','description','keywords','htmlheader' );
		foreach( $dos as $do ){
			$doval = $sonderfile->read_kimb_one( $do );
			if( $doval != $_POST[$do] ){
				$sonderfile->write_kimb_one( $do, $_POST[$do] );
				
				$message .= 'Der Wert "'.$do.'" wurde angepasst!<br />';
		
			}
		}
		
		if( !empty( $message ) ){
			$sitecontent->echo_message( $message, 'Änderung' );
		}
	}
	
	//Eingabefelder
	$sitecontent->add_site_content('<h3>Footer</h3>');
	$sitecontent->add_site_content('<textarea id="footer" name="footer" style="width:100%;">'.htmlspecialchars( $sonderfile->read_kimb_one('footer') , ENT_COMPAT | ENT_HTML401,'UTF-8' ).'</textarea>');
	
	$sitecontent->add_site_content('<h3>Error 404</h3>');
	$sitecontent->add_site_content('<textarea id="error_404" name="error-404" style="width:100%;">'.htmlspecialchars( $sonderfile->read_kimb_one('error-404') , ENT_COMPAT | ENT_HTML401,'UTF-8' ).'</textarea>');
	
	$sitecontent->add_site_content('<h3>Description</h3>');
	$sitecontent->add_site_content('<textarea name="description" style="width:100%;">'.htmlspecialchars( $sonderfile->read_kimb_one('description') , ENT_COMPAT | ENT_HTML401,'UTF-8' ).'</textarea>');
	
	$sitecontent->add_site_content('<h3>Keywords</h3>');
	$sitecontent->add_site_content('<textarea name="keywords" style="width:100%;">'.htmlspecialchars( $sonderfile->read_kimb_one('keywords') , ENT_COMPAT | ENT_HTML401,'UTF-8' ).'</textarea>');
	
	$sitecontent->add_site_content('<h3>HTML Head</h3>');
	$sitecontent->add_site_content('<textarea id="htmlheader" name="htmlheader" style="width:100%;">'.htmlspecialchars( $sonderfile->read_kimb_one('htmlheader') , ENT_COMPAT | ENT_HTML401,'UTF-8' ).'</textarea>');
	
	//JavaScript Bibilotheken einfach wählen für den Header
	$sitecontent->add_site_content('<div style="float:right;">');
	$sitecontent->add_site_content('<select id="libs">');
	$sitecontent->add_site_content('<option value=""></option>');
	$sitecontent->add_site_content('<option value="&lt;!-- jQuery --&gt;">jQuery</option>');
	$sitecontent->add_site_content('<option value="&lt;!-- jQuery UI --&gt;">jQuery UI</option>');
	$sitecontent->add_site_content('<option value="&lt;!-- Prism --&gt;">Prism</option>');
	$sitecontent->add_site_content('<option value="&lt;!-- Hash --&gt;">Hash</option>');
	$sitecontent->add_site_content('</select>');
	$sitecontent->add_site_content('<span class="ui-icon ui-icon-info" style="display:inline-block;" title="Fügen Sie Ihrer Seite ganz einfach eine JavaScript-Bibilothek hinzu." ></span>');
	$sitecontent->add_site_content('</div><br /><br />');
	
	//Button
	$sitecontent->add_site_content('<input type="hidden" value="yes" name="change">');
	$sitecontent->add_site_content('<input type="submit" value="Ändern"></form>');
}
else{
	//nichts gewünscht
	$sitecontent->add_site_content('Bitte wählen Sie oben einen Bereich aus, den Sie angezeigt bekommen wollen.');
}
?>
