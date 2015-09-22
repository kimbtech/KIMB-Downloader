<?php

/*************************************************/
//KIMB Downloader
//Copyright (c) 2015 by KIMB-technologies
/*************************************************/
//This program is free software: you can redistribute it and/or modify
//it under the terms of the GNU General Public License version 3
//published by the Free Software Foundation.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program.
/*************************************************/
//https://www.KIMB-technologies.eu
//https://www.bitbucket.org/kimbtech
//https://www.github.com/kimbtech
//https://www.gnu.org/licenses/gpl-3.0
//https://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/

defined('KIMB_Downloader') or die('No clean Request');

//Liste aller ToDos
$backend_todos[] = array( 'name' => 'Home', 'todo' => 'login', 'icon' => 'home' );
$backend_todos[] = array( 'name' => 'Explorer', 'todo' => 'explorer', 'icon' => 'folder-open');
$backend_todos[] = array( 'name' => 'Infos', 'todo' => 'infos', 'icon' => 'info' );
$backend_todos[] = array( 'name' => 'Konfiguration', 'todo' => 'konfig', 'icon' => 'gear' );
$backend_todos[] = array( 'name' => 'User', 'todo' => 'user', 'icon' => 'person' );
$backend_todos[] = array( 'name' => 'Module', 'todo' => 'module', 'icon' => 'plusthick' );
$backend_todos[] = array( 'name' => 'Themes', 'todo' => 'themes', 'icon' => 'contact' );

//nur die ToDos in Array
$todos_list = array_column($backend_todos, 'todo');

?>
