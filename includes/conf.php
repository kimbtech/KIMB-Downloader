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



		session_start();
		error_reporting(0);
		header('X-Robots-Tag: none');

		//Globale Variablen
	
		$allgconfsitefavi = "http://XXXXXX/load/KIMB.ico";
		$allgconfsiteurl = "http://XXXXXX";
		$allgconfserversitepath = "/var/www/downloader";
		$allgconfloginokay = "";

		$allgconfexplorerpath = "/var/www/downloader/files";

		$allgconfsitename = "";
		$allgconfdescription = "";
		$allgconfcopyrightname = '';
		$allgconfimpressumlink = "";

		$allgconfsysversion = "1.50";
		$allgconfurlweitermeth = "1";
		$allgconfmail['onoff'] = "off";
		$allgconfmail['abs'] = "gms@example.com";
		$allgconfmail['maildata'] = "data";
		$allgconfmail['logofdatapath'] = "";
	
		$allgconfadminuser['passw'] = "";
		$allgconfadminuser['username'] = "";
		$allgconfadminmail = "admin@example.com";
	
		//KIMB_Datei Lesefunktionen
		require_once('funktionen.php');
		?>
