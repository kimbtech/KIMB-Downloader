<?php

/*************************************************/
//KIMB-technologies
//KIMB Downloader
//KIMB-technologies.blogspot.com
/*************************************************/
//CC BY-ND 4.0
//http://creativecommons.org/licenses/by-nd/4.0/
//http://creativecommons.org/licenses/by-nd/4.0/legalcode
/*************************************************/
//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
//BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
//IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
//WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
//IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
/*************************************************/



//alle Inhalte gleich

$allcontent = $addontopcontent.$sitecontent.$addonbottomcontent;


//hier werden $sitecontent -> Seiteninhalt ; $header -> zusaetzlicher Header ; $footer -> Zusatz im Footer ; $siteapps -> Text linke Spalte verarbeitet
//außerdem werden $thissitename -> Seitentitelzusatz ;   verarbeitet
echo('<!DOCTYPE html> <html> <head> <title>'.$allgconfsitename.': '.$thissitename.'</title>');
echo("\n\r");
echo ('<link rel="shortcut icon" href="'.$allgconfsitefavi.'" type="image/x-icon; charset=binary">'."\n\r");
echo ('<link rel="icon" href="'.$allgconfsitefavi.'" type="image/x-icon; charset=binary">'."\n\r");
echo ('<meta name="generator" content="V. '.$allgconfsysversion.'" >'."\n\r");
echo ('<meta name="robots" content="none">'."\n\r");
echo ('<meta charset="utf-8">'."\n\r");
echo ('<link rel="stylesheet" type="text/css" href="'.$allgconfsiteurl.'/load/fonts.css" media="all">'."\n\r");
echo ('<link rel="stylesheet" type="text/css" href="'.$allgconfsiteurl.'/load/design.css" media="screen">'."\n\r");
echo ('<link rel="stylesheet" type="text/css" href="'.$allgconfsiteurl.'/load/print.css" media="print">');
echo ('<meta name="description" content="'.$allgconfdescription.'">'."\n\r");
echo("\n\r");
echo($header);
echo("\n\r");
echo('</head><body><div id="page">');
echo('<div id="header">');
echo('<div style="float:left;"><a href="'.$allgconfsiteurl.'/index.php"><img src="'.$allgconfsiteurl.'/load/logo.png" style="border:none;"></a></div><div style="padding-left:146px; line-height: 100px;">'.$allgconfsitename.'</div>');
echo('</div>');
echo('<div id="menu">');
echo('<a href="'.$allgconfsiteurl.'/index.php">Home</a>
&nbsp;|&nbsp;&lt;-->&nbsp;|&nbsp;<a href="'.$allgconfsiteurl.'/explorer.php">Explorer</a>
&nbsp;|&nbsp;&lt;-->&nbsp;|&nbsp;<a href="'.$allgconfsiteurl.'/info.php">Infomationen</a>');
if($_SESSION["backendlogin"]== $allgconfloginokay){
			echo('&nbsp;|&nbsp;&lt;-->&nbsp;|&nbsp;<i><a href="'.$allgconfsiteurl.'/login.php">Seiteninhalte ändern</a>
			&nbsp;--&nbsp;<u><b><a href="'.$allgconfsiteurl.'/login.php?todo=logout">Logout</a></b></u></i>');
		}
echo('</div>');
echo("\n\r");
echo('<div id="site">'."\n\r");
if($siteapps != ''){
	echo('<div id="menu-apps">'."\n\r");
	echo($siteapps);
	echo('</div>'."\n\r");
	echo('<div id="content-apps">');
	echo("\n\r\n\r");
	echo($allcontent);
	echo("\n\r\n\r");
	echo('</div>'."\n\r");
	}
else{
	echo('<div id="content">');
	echo("\n\r\n\r");
	echo($allcontent);
	echo("\n\r\n\r");
	echo('</div>'."\n\r");
	}
echo('</div><div id="footer">'."\n\r");
echo(''.$allgconfcopyrightname.'<br />');
echo('<a href="'.$allgconfimpressumlink.'" target=blank>Impressum</a><br />');
echo("\n\r");
echo('</div> <a style="float:right;" href="login.php">Login</a> </body> </html>');
?>
