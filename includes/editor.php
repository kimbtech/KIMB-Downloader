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




$header .= '<script src="'.$allgconfsiteurl.'/load/nicEdit.js" type="text/javascript"></script>';
$header .= '<script>bkLib.onDomLoaded(function() { nicEditors.allTextAreas({fullPanel : true, iconsPath : \''.$allgconfsiteurl.'/load/nicEditorIcons.gif\'}) });</script>';


if($_GET['todo']=='change'){

		write_kimb_replace('home.kimb', 'textl', $_POST['htl']);
		write_kimb_replace('home.kimb', 'textm', $_POST['htm']);
		write_kimb_replace('lizenz.kimb', 'textl', $_POST['ltl']);
		write_kimb_replace('lizenz.kimb', 'textm', $_POST['ltm']);

		open_url('login.php');
		die;
	
}
else{
		$sitecontent .= '<h2>Seiteninhalte verändern:</h2>';
		$sitecontent .= '<form action="login.php?todo=change" method="post">';

		$sitecontent .= '<h3>Text Home Links:</h3>';
		$sitecontent .= '<textarea name="htl" cols="100" rows="20">'.read_kimb_one('home.kimb', 'textl').'</textarea>';

		$sitecontent .= '<h3>Text Home Mitte:</h3>';
		$sitecontent .= '<textarea name="htm" cols="100" rows="20">'.read_kimb_one('home.kimb', 'textm').'</textarea>';

		$sitecontent .= '<h3>Text Information Links:</h3>';
		$sitecontent .= '<textarea name="ltl" cols="100" rows="20">'.read_kimb_one('lizenz.kimb', 'textl').'</textarea>';

		$sitecontent .= '<h3>Text Information Mitte:</h3>';
		$sitecontent .= '<textarea name="ltm" cols="100" rows="20">'.read_kimb_one('lizenz.kimb', 'textm').'</textarea>';

		$sitecontent .= '<br /><input type="submit" value="Ändern">';
		$sitecontent .= '</center></form>';

		$sitecontent .= 'Packen Sie alle Ihre Dateien in das Verzeichnis "/files/", für eine Infoseite erstellen Sie bitte eine Datei "readme.html" im jeweiligen Verzeichnis.<br />';
		$sitecontent .= 'Ein Ordner mit dem Namen "hidden-XXX" wird für jeden verborgen, nur über Login oder mit dem entsprechenden Link wird er sichtbar!<br />';
}



?>
