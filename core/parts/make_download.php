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

//Datei download

$sitecontent->add_site_content( make_breadcrumb( false, false, basename( $urlfrag ) ) );

//CSS
$sitecontent->add_html_header( ' <link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/view.css" media="all">' );

$downloadurl = $allgsysconf['siteurl'].'/getfile.php?file='.urlencode($urlfrag);

$sitecontent->add_site_content( '<iframe style="width:0; height:0;"  frameborder="0" scrolling="no" src="'.$downloadurl.'"></iframe>');

$sitecontent->add_site_content( '<div class="download"><center><i><b>Der Download startet in Kürze!!</b></i><br /><br />');
$sitecontent->add_site_content( '<small>Sollte der Download nicht starten klicken Sie <a href="'.$downloadurl.'">hier</a>.</small></center></div>');

?>
