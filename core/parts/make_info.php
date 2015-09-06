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

//Readme des Verzeichnisses anzeigen

$md = '
HALLO Franz
================================================

##About
>Das KIMB-CMS (*KIMB ContentManagementSystem*) ist ein einfaches CMS mit vielen wichtigen Features. Bei der Entwicklung wurde auf Relevanz und Einfachheit geachtet.  
>Das CMS unterstützt Add-ons und Themes zur verbesserten Personalisierung. Auch auf URL-Rewriting wurde nicht verzichtet. Features wie ein Cache und ein Backend mit verschiedenen Userlevelen sind integriert.
>####Anforderungen
>Das System benötigt keine Datenbank, nur einen Webserver (Apache) mit PHP 5.5 oder neuer.

##Install

1. Manuell
 1. [Download Installpack](https://raw.githubusercontent.com/kimbtech/kimb-cms/files/Install/KIMB-CMS_V2.0F.zip)
 2. [Anleitung im Wiki](https://cmswiki.kimb-technologies.eu/tutorials/installation#man)
2. Automatisch
 1. [Download Installer](https://raw.githubusercontent.com/kimbtech/kimb-cms/files/Install/easy-installer.php)
 2. [Anleitung im Wiki](https://cmswiki.kimb-technologies.eu/tutorials/installation#aut)

###Lizenz
|Lizenz|Version|
|-----------------|--------------------|
| [![GPLv3](https://www.kimb-technologies.eu/load/userdata/softallg/gpl-v3-logo_smaller.png "GPLv3")](https://github.com/kimbtech/KIMB-CMS/blob/files/LICENSE) | Version 2
[![CC BY-ND](https://www.kimb-technologies.eu/load/userdata/softallg/88x31.png "CC BY-ND")](http://creativecommons.org/licenses/by-nd/4.0/legalcode) | Version 1

###Links
[Homepage](https://www.kimb-technologies.eu/software/cms/)  
[Downloads KIMB-technologies](https://download.kimb-technologies.eu/explorer.php?action=rein&path=%2FCMS%2FVersion-2)  
[GIT Bitbucket](https://bitbucket.org/kimbtech/kimb-cms/)  
[GIT & Downloads **GitHub - Spiegel**](https://github.com/kimbtech/kimb-cms/)  

[Demo](http://demo.kimb-technologies.eu/cms/)  
[Wiki & Dokumentation](https://cmswiki.kimb-technologies.eu/)  

###Links
[Homepage](https://www.kimb-technologies.eu/)  
[Downloads KIMB-technologies](https://download.kimb-technologies.eu/)  
[GIT Bitbucket](https://bitbucket.org/kimbtech/)  
[Spiegel](https://github.com/kimbtech/)  

';

$sitecontent->add_site_content( MarkdownExtra::defaultTransform($md) );

?>
