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

$thissitename = "Home"; // Seitenname

$header .= '';

$sitecontent .= '<h2>Willkommen</h2>';

if($_GET['error'] != ''){
	if($_GET['error']=='404' || $_GET['error']=='403' || $_GET['error']== $syslang['err030']){$error = $_GET['error'];}
	else{$error = $syslang['err029'];}
	$sitecontent .='<div style="background:#cccccc; margin:auto; margin-bottom:10px; padding:20px; border-radius: 15px;">';
	$sitecontent .='<h1>Error - '.$error.'</h1>';
	$sitecontent .='</div>';
}

$sitecontent .= '<br />'.read_kimb_one('home.kimb', 'textm').'<br />';

$siteapps .= '<br />'.read_kimb_one('home.kimb', 'textl').'<br />';

$footer .='';
//aktuelle Seite -- Ende
//aktuelle Seite -- Ende

require_once('includes/all.php');
//$sitecontent -> Seiteninhalt
//$header -> zusaetzlicher Header
//$footer -> Zusatz im Footer
?>
