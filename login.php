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



require_once('includes/conf.php');
//globale Variablen, Funktionen

//aktuelle Seite -- Anfang
//aktuelle Seite -- Anfang

$thissitename = "Login";

if($_GET['todo'] == 'logout'){

	$loginfehler = $_SESSION["loginfehler"];
	session_destroy();
	session_start();
	$_SESSION["loginfehler"] = $loginfehler;	

	$sitecontent .='<div style="background:#cccccc; margin:auto; margin-bottom:10px; padding:20px; border-radius: 15px;">';
	$sitecontent .='<h1>Sie wurden ausgeloggt!</h1>';
	$sitecontent .='</div>';
}

//Loginteil

if($_GET['todo'] == 'login'){

	if($_SESSION["loginfehler"]==''){ $_SESSION["loginfehler"] = '0'; }

	$user = $_POST['user'];
	$passhash = $_POST['pass'];
	$passpruef = md5(md5($allgconfadminuser['passw']).$_SESSION["loginsalt"]);
	
	if( $allgconfadminuser['username'] != $user || $passhash != $passpruef || $_SESSION["loginfehler"] >= '6'){
		$_SESSION["loginfehler"]++;
		$sitecontent .='<div style="background:#cccccc; margin:auto; margin-bottom:10px; padding:20px; border-radius: 15px;">';
		$sitecontent .='<h1>Fehler!</h1>';
		$sitecontent .='<h1>Das ist schon Ihr '.$_SESSION["loginfehler"].'. Versuch, von 6 Versuchen!</h1>';
		$sitecontent .= '<center><a href="login.php"><button>Nächster Versuch</button></a></center>';
		$sitecontent .='</div>';
		
	}
	else{
		$_SESSION["backendlogin"] = $allgconfloginokay;
		$_SESSION["user"] = $allgconfadminuser['username'];
		$_SESSION["ip"] = getenv ("REMOTE_ADDR");
		$_SESSION["useragent"] = $_SERVER['HTTP_USER_AGENT'];
		unset($_SESSION["loginsalt"]);

		$sitecontent .='<div style="background:#cccccc; margin:auto; margin-bottom:10px; padding:20px; border-radius: 15px;">';
		$sitecontent .='<h1>Willkommen, Sie sind eingeloggt!</h1>';
		$sitecontent .='</div>';

		require_once('includes/editor.php');
	}
}
elseif($_SESSION["backendlogin"] == $allgconfloginokay){
	$sitecontent .='<div style="background:#cccccc; margin:auto; margin-bottom:10px; padding:20px; border-radius: 15px;">';
	$sitecontent .='<h1>Willkommen, Sie sind eingeloggt!</h1>';
	$sitecontent .='</div>';

	require_once('includes/editor.php');
}
else{
	$loginsalt = sha1(mt_rand());
	$_SESSION["loginsalt"] = $loginsalt;
	$sitecontent .= '<h2>Bitte loggen Sie sich ein!</h2>';
	$sitecontent .= '<form action="login.php?todo=login" method="post" onsubmit="document.getElementById(\'pass\').value = MD5(MD5(document.getElementById(\'pass\').value)+\''.$loginsalt.'\');">
		<table><tr><td>Username:</td><td><input type="text" name="user"><br /></td></tr>
		<tr><td>Paswort:</td><td><input type="password" name="pass" id="pass" maxlength="32"><br /></td></tr></table>
		<input type="submit" value="Anmelden">
		</form><br /><br />';

	$sitecontent .='<noscript>';
	$sitecontent .='<h3>Sie benötigen Java Script</h3>';
	$sitecontent .='</noscript>';


}

$header .= '<script language="javascript" src="'.$allgconfsiteurl.'/load/md5.js"></script>';

//aktuelle Seite -- Ende
//aktuelle Seite -- Ende

require_once('includes/all.php');
//$sitecontent -> Seiteninhalt
//$header -> zusaetzlicher Header
//$footer -> Zusatz im Footer
?>
