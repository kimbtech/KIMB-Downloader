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

//zu oeffnenden Pfad generieren
if ($_GET['action']=='rein'){
	$openpath=$userpath.$_GET['path']."/";
	$pathnow=$_GET['path'];
	}
elseif ($_GET['action']=='hoch'){
	$pfad = $_GET['path'];
	$openpath = $userpath.substr($pfad, '0', strlen($pfad) - strlen(strrchr($pfad, '/'))).'/';
	$pathnow = substr($pfad, '0', strlen($pfad) - strlen(strrchr($pfad, '/')));
	}
else {$openpath=$userpath;}


if(strpos($openpath, "..") !== false){
	echo ('Do not hack me!!');
	die;
}


$addonbottomcontent .= ('<div style="margin:20px; text-align:left; background: url('.$allgconfsiteurl.'/load/cloud.png) repeat-y scroll top #ffffff; height:auto; min-height:300px; padding:5px; border-radius:15px;">');

$syntaxshow = array("text/php", "text/x-php", "text/x-csrc", "text/x-c", "application/x-python", "application/x-tex", "application/php", "inode/x-empty", "application/x-php", "text/x-shellscript", "application/x-httpd-php", "application/x-httpd-php-source", "application/xhtml+xml", "application/x-javascript", "application/x-sh", "text/css", "text/plain", "text/javascript", "text/html", "text/xml");

if(is_dir($openpath)){

	$addonbottomcontent .= ('<div style="padding-bottom:5px; font-size:1em;">');
	$addonbottomcontent .= ('<center>');
	$addonbottomcontent .= ('<div style="float:left;"><a style="display:inline-block; background:#999; height:58px; margin-right:5px; border: 2px solid #000; border-radius: 2px;" href="explorer.php?action=hoch&amp;path='.urlencode($pathnow).'"><img src="'.$allgconfsiteurl.'/load/up.png" title="Back" alt="&lt;-Back"></a></div>');
	$addonbottomcontent .= ('<div style="line-height: 58px; padding:2px; text-align:left; margin:2px; background:#cccccc; border-radius:2px;">files://'.$pathnow.'</div>');
	$addonbottomcontent .= ('</center>');
	$addonbottomcontent .= ('</div>');
	$addonbottomcontent .= ('<div style="border-style:double; border-width:thin; border-color:black; padding:20px; border-radius:15px;">');

	if ($handle = opendir($openpath)) {
	    while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
		if (substr($file, 0, 7) != "hidden-" || $_SESSION["backendlogin"] == $allgconfloginokay){
				$type = mime_content_type($openpath.$file);
				if(is_dir($openpath.$file)){
					$addonbottomcontent .=  '<div style=" background-image:url('.$allgconfsiteurl.'/load/ordner.png); padding-left:35px; display: table-cell; vertical-align:middle; height:32px; background-repeat:no-repeat; border-radius:5px;">
						<a href="explorer.php?action=rein&amp;path='.urlencode($pathnow.'/'.$file).'">'.$file.'</a>';
					$addonbottomcontent .= '</div><br />';
				}
				elseif($file == 'readme.html'){
					$addonbottomcontent .=  '<div style="background-image:url('.$allgconfsiteurl.'/load/info.png); padding-left:35px; display: table-cell; vertical-align:middle; height:32px; background-repeat:no-repeat; border-radius:5px;">
						<a href="explorer.php?todo=down&amp;down='.urlencode($pathnow.'/'.$file).'&amp;dat='.urlencode($file).'" target="blank">Projektinformation</a>';
					$addonbottomcontent .= '</div><br />'; 
				}
				elseif(in_array($type, $syntaxshow)){
					$addonbottomcontent .=  '<div style="background-image:url('.$allgconfsiteurl.'/load/code.png); padding-left:35px; display: table-cell; vertical-align:middle; height:32px; background-repeat:no-repeat; border-radius:5px;">
						<a href="explorer.php?todo=down&amp;down='.urlencode($pathnow.'/'.$file).'&amp;dat='.urlencode($file).'" target="blank">'.$file.'</a>';
					$addonbottomcontent .= '</div><br />'; 
				}
				elseif($type == "application/zip"){
					$addonbottomcontent .=  '<div style="background-image:url('.$allgconfsiteurl.'/load/zip.png); padding-left:35px; display: table-cell; vertical-align:middle; height:32px; background-repeat:no-repeat; border-radius:5px;">
						<a href="explorer.php?todo=down&amp;down='.urlencode($pathnow.'/'.$file).'&amp;dat='.urlencode($file).'" target="blank">'.$file.'</a>';
					$addonbottomcontent .= '</div><br />'; 
				}
				else{  
					$addonbottomcontent .=  '<div style="background-image:url('.$allgconfsiteurl.'/load/other.png); padding-left:35px; display: table-cell; vertical-align:middle; height:32px; background-repeat:no-repeat; border-radius:5px;">
						<a href="explorer.php?todo=down&amp;down='.urlencode($pathnow.'/'.$file).'&amp;dat='.urlencode($file).'" target="blank">'.$file.'</a>';
					$addonbottomcontent .= '</div><br />'; 
				}
		}
		}
	    }
	    closedir($handle);
	}
}
else{

	$addonbottomcontent .= ('<div style="border-style:double; border-width:thin; border-color:black; padding:20px; border-radius:15px;">');
	$addonbottomcontent .=  '<b>Der gewünschte Pfad ist nicht vorhanden!</b>';

}

$addonbottomcontent .= ('</div></div>');


?>
