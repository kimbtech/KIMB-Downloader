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




error_reporting('0');

if(file_exists ('conf-enable') != 'true'){
	echo('<title>KIMB LCNV - Installation</title><link rel="shortcut icon" href="load/KIMB.ico" type="image/x-icon; charset=binary"><h1>Error - 404</h1>Bitte schalten Sie den Configurator frei, erstellen Sie eine leere "conf-enable" Datei im LCNV-Root-Verzeichnis.'); die;
}

echo('
<html><head>
<title>KIMB LCNV - Installation</title>
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
</style></head><body>
');
echo('<div id="main"><h1 style="border-bottom:5px solid #55dd77;" >KIMB LCNV - Installation</h1>');



if($_GET['step'] == '2'){

	$sitecontent .= '<form method="post" action="configurator.php?step=3">';

	$sitecontent .= '<input type="text" name="a101a" size="60"><br />(Name der Seite)<br /><br />';
	$sitecontent .= '<input type="text" name="a102a" size="60"><br />(Meta Beschreibung der Seite)<br /><br />';
	$sitecontent .= '<input type="text" name="a103a" size="60"><br />(Footer der Seite, z.B. Lizenz)<br /><br />';
	$sitecontent .= '<input type="text" name="a104a" size="60"><br />(URL zum Impressum)<br /><br />';
	$sitecontent .= '<input type="text" name="a106a" size="60"><br />(Username f&uuml;r Login)<br /><br />';
	$sitecontent .= '<input type="text" name="a105a" size="60"><br />(Passwort f&uuml;r Login)<br /><br />';

	$sitecontent .= '<input type="submit" value="Weiter"><br />';
	$sitecontent .= '</form>';
	echo($sitecontent);
}

elseif($_GET['step'] == '3'){

	if(isset($_SERVER['HTTPS'])){
		$urlg = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	else{
		$urlg = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	$url = substr($urlg, '0', '-'.strlen(strrchr($urlg, '/')));


	$newconf = '<?php

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



		session_start();
		error_reporting(0);
		header(\'X-Robots-Tag: none\');

		//Globale Variablen
	
		$allgconfsitefavi = "'.$url.'/load/KIMB.ico";
		$allgconfsiteurl = "'.$url.'";
		$allgconfserversitepath = "'.__DIR__.'";
		$allgconfloginokay = "'.md5(mt_rand()).'";

		$allgconfexplorerpath = "'.__DIR__.'/files";

		$allgconfsitename = "'.$_POST['a101a'].'";
		$allgconfdescription = "'.$_POST['a102a'].'";
		$allgconfcopyrightname = \''.$_POST['a103a'].'\';
		$allgconfimpressumlink = "'.$_POST['a104a'].'";

		$allgconfsysversion = "1.50";
		$allgconfurlweitermeth = "1";
		$allgconfmail[\'onoff\'] = "off";
		$allgconfmail[\'abs\'] = "gms@example.com";
		$allgconfmail[\'maildata\'] = "data";
		$allgconfmail[\'logofdatapath\'] = "";
	
		$allgconfadminuser[\'passw\'] = "'.$_POST['a105a'].'";
		$allgconfadminuser[\'username\'] = "'.$_POST['a106a'].'";
		$allgconfadminmail = "admin@example.com";
	
		//KIMB_Datei Lesefunktionen
		require_once(\'funktionen.php\');
		?>';
	
	$handle = fopen(__DIR__.'/includes/conf.php', 'w+');
	fwrite($handle, $newconf);
	fclose($handle);

	
	
	echo('Installation erfolgreich! <a href="login.php"><button>Zum Login</button></a>');

	unlink('conf-enable');

}
else{

	if (version_compare(PHP_VERSION, '5.3.0', '<' )) {
    		echo '<b>Dieses System wurde f&uuml;r PHP 5.3.0 und h&ouml;her entwickelt, das System funktioniert wahrscheinlich fehlerfrei, bei Problemen sollten Sie aber ein PHP-Update durchf&uuml;hren!</b><br />';
	}	

	$count = '0';

	if(is_writable('kimb-data/')){echo 'kimb-data/ ist schreibbar -> OK<br /><br />'; $count++;}
	else{echo '<b style="color:#dd4444">kimb-data/ ist nicht schreibbar -> Fehler!!</b><br /><br />';}

	if($count == '1'){
		echo('<a href="configurator.php?step=2"><button>Weiter - Installation</button></a></br />');
	}
	else{
		echo('<b style="color:#dd4444">Die Fehler m&uuml;ssen entfernt werden.<br />Dateirechte eingestellt?</b><br /> <a href="configurator.php"><button>N&auml;chster Versuch</button></a></br />');
	}

}

echo('</div></body></html>');
?>
