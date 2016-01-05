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

//Add-on URL, damit kann man später einfacher abrbeiten
$addonurl = $allgsysconf['siteurl'].'/backend.php?todo=module&amp;module=kimbea';

//Form beginnen
$sitecontent->add_site_content('<form action="'.$addonurl.'" method="post" >');

//Inhalte Vorgaben
$examples['001'] = array( 
	'infobann' => 'on' ,
	'ibcss' => 'div#analysehinweis{position:fixed; left:0; top:0; width:100%; background-color:orange; text-align:center; z-index:2;}' ,
	'ibtext' => '<p>Diese Seite nutzt Cookies und einen Webanalysedienst. Mit der Nutzung dieser Seite erklären Sie sich damit einverstanden.</p><p><i>Weitere Informationen: <a href="http://www.example.com/" target="_blank" style="color:#ffffff; text-decoration:underline;">Impressum &amp; Datenschutz</a>!</i></p>'
);
$examples['002'] = array( 
	'art' => 'url' ,
	'path' => '../../../../--EA--Folder--/tracker/track.php' ,
	'url' => 'http://ea.example.com/tracker/track.php' ,
	'siteid' => '1' ,
);

//Neue Daten??
if( !empty( $_POST['send'] ) ){
	//alle IDs (001 und 002) durchgehen
	foreach( $_POST as $id => $mixed ){
		//alle Werte für die IDs durchgehen
		foreach( $mixed as $xxxid => $val ){
			//Änderung ??
			//außerdem sicherstellen, dass ID und xxxid gegeben (muss Beispiel geben!!)
			if( $val != $eaconffile->read_kimb_id( $id, $xxxid ) && isset( $examples[$id][$xxxid] ) ){
				//Wert in dbf ändern
				$eaconffile->write_kimb_id( $id, 'add', $xxxid, $val );
				//Meldung
				$message .= $xxxid.' wurde angepasst!<br />';
			}
			
		}
	}
}
//Medlung für Änderunge
if( !empty( $message ) ){
	$sitecontent->echo_message( $message, 'Änderungen übernommen' );
}

//infobanner Daten
$ibinfo = $eaconffile->read_kimb_id( '001' );
//tracker Daten
$trackinfo = $eaconffile->read_kimb_id( '002' );

//Leer => Beispiel einfügen!!
//	alle für Infobanner durchgehen
foreach( $examples['001'] as $key => $val ){
	//leer
	if( empty( $ibinfo[$key] ) ){
		//Beispiel setzen
		$ibinfo[$key] = $val;
	}
}
//	alle für Tracking durchgehen
foreach( $examples['002'] as $key => $val ){
	//leer
	if( empty( $trackinfo[$key] ) ){
		//Beispiel setzen
		$trackinfo[$key] = $val;
	}
}


//Formularausgaben
$sitecontent->add_site_content('<h3>Tracking</h3>');
//Art checked richtig setzen
$sys = array( 'url' => '', 'path' => '' );
if( $trackinfo['art'] == 'url'){
	$sys['url'] = 'checked="checked"';
}
else{
	$sys['path'] = 'checked="checked"';
}
$sitecontent->add_site_content('<input name="002[art]" value="url" type="radio" '.$sys['url'].'> JavaScript Tracking<span style="display:inline-block;" title="Es wird ein TrackingCode in die Seite eingebaut und die User werden mit JavaScript getrackt." class="ui-icon ui-icon-info"></span>' );
$sitecontent->add_site_content('<input name="002[art]" value="path" type="radio" '.$sys['path'].'> PHP Tracking<span style="display:inline-block;" title="Das Tracking findet per PHP statt, es wir die Tracking Klasse des KIMB-EA geladen und der Besuch aufgenommen." class="ui-icon ui-icon-info"></span><br /><br />' );

//Werte
$sitecontent->add_site_content('<input name="002[path]" type="text" value="'.$trackinfo['path'].'"> Pfad zur KIMB-EA Tracking Klasse<span style="display:inline-block;" title="Geben Sie hier den Dateisystempfad zur Tracking Klasse von KIMB-EA ein. (&apos;../../../../KIMB_EA/tracker/track.php&apos; geht ein Verzeichnis unter das CMS Root Verzeichnis und dann zu KIMB-EA im Ordner &apos;KIMB_EA&apos;." class="ui-icon ui-icon-info"></span><br />' );
$sitecontent->add_site_content('<input name="002[url]" type="text" value="'.$trackinfo['url'].'"> URL zur track.php<span style="display:inline-block;" title="Geben Sie hier die URL zum Tracker von KIMB-EA ein, z.B: http://ea.example.com/tracker/track.php" class="ui-icon ui-icon-info"></span><br />' );
$sitecontent->add_site_content('<input name="002[siteid]" type="text" value="'.$trackinfo['siteid'].'"> Seiten ID in KIMB-EA<span style="display:inline-block;" title="Geben Sie hier die SiteID von KIMB-EA an, welche für die Seite dieses CMS erstellt wurde." class="ui-icon ui-icon-info"></span><br />' );

//Infobanner on/off checked richtig setzen
$sys = array( 'on' => '', 'off' => '' );
if( $ibinfo['infobann'] == 'on'){
	$sys['on'] = 'checked="checked"';
}
else{
	$sys['off'] = 'checked="checked"';
}

//Infobanner on/off und Werte
$sitecontent->add_site_content('<h3>Infobanner</h3>');
$sitecontent->add_site_content('<input name="001[infobann]" value="on" type="radio" '.$sys['on'].'><span style="display:inline-block;" title="Infobanner bei erstem Seitenaufruf anzeigen (rechtlich teilweise nötig um über Cookies und Webanalyse zu infomieren)" class="ui-icon ui-icon-check"></span>' );
$sitecontent->add_site_content('<input name="001[infobann]" value="off" type="radio" '.$sys['off'].'><span style="display:inline-block;" title="Keinen Infobanner anzeigen" class="ui-icon ui-icon-closethick"></span><br />' );
$sitecontent->add_site_content('<textarea name="001[ibcss]" style="width:75%; height:70px;" >'.$ibinfo['ibcss'].'</textarea> CSS<b title="CSS Code für den Infobanner (leer => Voreinstellung)">*</b><br />');
$sitecontent->add_site_content('<textarea name="001[ibtext]" style="width:75%; height:70px;" >'.$ibinfo['ibtext'].'</textarea> Text<b title="Text des Infobanners (leer => Voreinstellung, Achtung: Link zum Impressum einfügen)">*</b><br />');

$sitecontent->add_site_content('<br /><br /><input type="hidden" value="send" name="send"><input type="submit" value="Speichern"> </form>');


?>