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




$userpath = $allgconfexplorerpath.'/';

$filepath = $userpath.$_GET['down'];

$pathnow=$_GET['down'];

if(strpos($filepath, "..") !== false){
	echo ('Do not hack me!!');
	die;
}

$daten = file_get_contents($filepath);
$type = mime_content_type($filepath);
$datei = $_GET['dat'];

$syntaxshow = array("text/php", "text/x-php", "text/x-csrc", "text/x-c", "application/x-python", "application/x-tex", "application/php", "inode/x-empty", "application/x-php", "text/x-shellscript", "application/x-httpd-php", "application/x-httpd-php-source", "application/xhtml+xml", "application/x-javascript", "application/x-sh", "text/css", "text/plain", "text/javascript", "text/html", "text/xml");

if(is_file($filepath)){
	if($type == "application/zip"){
		header("Content-Type: application/force-download");
		header("Content-type: application/zip");
		header('Content-Disposition: attachment; filename= '.$datei);
		echo ($daten);

		die;
	}
	elseif($datei == 'readme.html'){
	
		$addonbottomcontent .= ('<div style="margin:20px; text-align:left; height:auto; min-height:300px; padding:5px; border-radius:15px;">');
		$addonbottomcontent .= ('<center>');
		$addonbottomcontent .= ('<div style="float:left;"><a style="display:inline-block; background:#999; height:58px; margin-right:5px; border: 2px solid #000; border-radius: 2px;" href="explorer.php?action=hoch&amp;path='.urlencode($pathnow).'"><img src="'.$allgconfsiteurl.'/load/up.png" title="Back" alt="&lt;-Back"></a></div>');
		$addonbottomcontent .= ('<div style="line-height: 58px; padding:2px; text-align:left; margin:2px; background:#cccccc; border-radius:2px;">files://'.$pathnow.'</div>');
		$addonbottomcontent .= ('</center>');
		$addonbottomcontent .= ('<div style="border-style:double; border-width:thin; border-color:black; padding:20px; border-radius:15px;">');

		$addonbottomcontent .= $daten;

		$addonbottomcontent .= ('</div></div>');
				}
	elseif(in_array($type, $syntaxshow)){

		$path_parts = pathinfo($filepath);

		$language['php'] = array("php", "inc");
		$language['bash'] = array("sh");
		$language['markup'] = array("html", "xhtml", "htm", "xml");
		$language['css'] = array("css");
		$language['javascript'] = array("js");
		$language['python'] = array("py");
		$language['latex'] = array("tex");
		$language['apacheconf'] = array("htaccess");
		$language['c'] = array("c");
	
		if(in_array($path_parts['extension'], $language['php'])){ $language['datei'] = 'php'; }
		elseif(in_array($path_parts['extension'], $language['bash'])){ $language['datei'] = 'bash'; }
		elseif(in_array($path_parts['extension'], $language['markup'])){ $language['datei'] = 'markup'; }
		elseif(in_array($path_parts['extension'], $language['css'])){ $language['datei'] = 'css'; }
		elseif(in_array($path_parts['extension'], $language['javascript'])){ $language['datei'] = 'javascript'; }
		elseif(in_array($path_parts['extension'], $language['python'])){ $language['datei'] = 'python'; }
		elseif(in_array($path_parts['extension'], $language['latex'])){ $language['datei'] = 'latex'; }
		elseif(in_array($path_parts['extension'], $language['apacheconf'])){ $language['datei'] = 'apacheconf'; }
		elseif(in_array($path_parts['extension'], $language['c'])){ $language['datei'] = 'c'; }
		else{ $language['datei'] = 'markup'; }

		$addonbottomcontent .= ('<div style="margin:20px; text-align:left; height:auto; min-height:300px; padding:5px; border-radius:15px;">');
		$addonbottomcontent .= ('<center>');
		$addonbottomcontent .= ('<div style="float:left;"><a style="display:inline-block; background:#999; height:58px; margin-right:5px; border: 2px solid #000; border-radius: 2px;" href="explorer.php?action=hoch&amp;path='.urlencode($pathnow).'"><img src="'.$allgconfsiteurl.'/load/up.png" title="Back" alt="&lt;-Back"></a></div>');
		$addonbottomcontent .= ('<div style="line-height: 58px; padding:2px; text-align:left; margin:2px; background:#cccccc; border-radius:2px;">files://'.$pathnow.'</div>');
		$addonbottomcontent .= ('</center>');
		$addonbottomcontent .= ('<div style="border-style:double; border-width:thin; border-color:black; padding:20px; border-radius:15px;">');
		$addonbottomcontent .= ('<pre class="language-'.$language['datei'].'" ><code class="language-'.$language['datei'].'">');

		$addonbottomcontent .= (htmlentities($daten));

		$addonbottomcontent .= ('</code></pre>');
		$addonbottomcontent .= ('</div></div>');

		$header .= ('<link href="'.$allgconfsiteurl.'/load/prism.css" rel="stylesheet" />');
		$header .= ('<script src="'.$allgconfsiteurl.'/load/prism.js"></script>');

	}
	else{

		header("Content-type: ".$type);
		header('Content-Disposition: filename= '.$datei);
		echo ($daten);

		die;
	}
}
else{

	$addonbottomcontent .= ('<div style="border-style:double; border-width:thin; border-color:black; padding:20px; border-radius:15px;">');
	$addonbottomcontent .=  '<b>Die gewünschte Datei ist nicht vorhanden!</b>';
	$addonbottomcontent .=  '</div>';

}
?>
