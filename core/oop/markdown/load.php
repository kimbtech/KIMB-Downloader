<?php

defined('KIMB_Downloader') or die('No clean Request');

//Datei um Markdownparser zu laden

require_once ( __DIR__. '/Markdown.php' );
require_once ( __DIR__. '/MarkdownExtra.php' );

//Benutzung:
//	$html = Markdown::defaultTransform($md);
//	$html = MarkdownExtra::defaultTransform($md);

?>