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

$thissitename = 'Infomationen'; // Seitenname

$header .= '';

$sitecontent .= '<h2>Informationen</h2>';

$sitecontent .= '<br />'.read_kimb_one('lizenz.kimb', 'textm').'<br />';

$siteapps .= '<br />'.read_kimb_one('lizenz.kimb', 'textl').'<br />';

$footer .= '';
//aktuelle Seite -- Ende
//aktuelle Seite -- Ende

require_once('includes/all.php');
//$sitecontent -> Seiteninhalt
//$header -> zusaetzlicher Header
//$footer -> Zusatz im Footer
?>
