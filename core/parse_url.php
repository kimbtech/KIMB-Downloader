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

//URL Parsen und für /parts/make_XXXX.php vorbereiten
//	$parsed enthält part zu machen

//URL-Schema
//	example.com/downloader/info/folder(unter files) => Inhalt der readme des Ordners
//	example.com/downloader/explorer/folder(unter files) => Elemente des Ordners
//	example.com/downloader/view/folder(unter files)/Datei => eine Datei anschauen (Vorschau)
//	example.com/downloader/download/folder(unter files)/Datei => eine Datei herunterladen


//alte URLs verarbeiten
//	dazu Datei explorer.php die auf neue URL weiterleitet!

?>
