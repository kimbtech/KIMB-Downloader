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

//Quelltexte, Bilder, PDF Vorschau anzeigen
//Binaries => Download Button

$sitecontent->add_site_content( make_breadcrumb( false, false, basename( $urlfrag ) ) );

//CSS
$sitecontent->add_html_header( ' <link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/view.css" media="all">' );

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimetype = finfo_file($finfo, $folder);
finfo_close($finfo);

if( substr( $mimetype, 0, 5 ) == 'text/' ){
	$art = 'code';
}
elseif( substr( $mimetype, 0, 6 ) == 'image/' ){
	$art = 'image';
}
elseif( $mimetype == 'application/pdf' ){
	$art = 'pdf';
}
else{
	$art = 'other';
}

if( $art == 'code'){
	$sitecontent->add_html_header( '<link rel="stylesheet" type="text/css" href="'.$allgsysconf['siteurl'].'/load/prism/prism.css" media="all">' );
	$sitecontent->add_site_content( '<pre data-src="'.$allgsysconf['siteurl'].'/getfile.php?inline&amp;file='.urlencode($urlfrag).'"></pre>');
	$sitecontent->add_site_content( '<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/prism/prism.js"></script>');
}
elseif( $art == 'image' ){
	$sitecontent->add_site_content( '<center><img class="viewimgs" src="'.$allgsysconf['siteurl'].'/getfile.php?file='.urlencode($urlfrag).'&amp;inline" title="Bild" alt="Bild"><center>');
}
elseif( $art == 'pdf' ){
	$sitecontent->add_site_content( '<iframe style="width:100%; height:100%; min-height:500px;"  frameborder="0" scrolling="yes" src="'.$allgsysconf['siteurl'].'/getfile.php?file='.urlencode($urlfrag).'&amp;inline"></iframe>');
}
else{
	if( $allgsysconf['urlrewrite'] == 'on' ){
		$downurl = $allgsysconf['siteurl'].'/download'.$urlfrag;
	}
	else{
		$downurl = $allgsysconf['siteurl'].'/?pfad=download'.urlencode( $urlfrag );
	}
	
	$sitecontent->add_site_content( '<br /><center><i><b>Für diese Datei ist keine Vorschau möglich!</b></i></center><br />');
	$sitecontent->add_site_content( '<a class="downbutt" href="'.$downurl.'">');
	$sitecontent->add_site_content( '<center><span class="download_icon" title="Download"></span><br />Download</center>');
	$sitecontent->add_site_content( '</a>');
}

?>
