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

//Die KIMBdbf ist die zentrale Klasse für alle Datenbankaufgaben.
//Alle Daten werden hiermit gespeichert.
//==> Weitere Infos zu KIMBdbf
//	https://download.kimb-technologies.eu/explorer.php?action=rein&path=%2FKIMBdbf

//Die Möglichkeit der Verschlüsselung der Dateien wird vom KIMB-Downloader nicht verwendet!

//objektorientiert
//objektorientiert
//objektorientiert

class KIMBdbf {

	//Allgemeines zur Klasse
	protected $path;
	protected $datei;
	protected $encryptkey;
	protected $dateicont;
	protected $dateicontanfang;
	protected $dateidel = 'no';
	
	const DATEIVERSION = '3.50';
	
	public function __construct($datei, $encryptkey = 'off', $path = __DIR__){
		$datei = preg_replace('/[\r\n]+/', '', $datei);
		$datei = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', ' ', '..'),array('ae','oe','ue','ss','Ae','Oe','Ue', '', '.'), $datei);
		$datei = preg_replace( '/[^A-Za-z0-9]_.-/' , '' , $datei );
		if(strpos($datei, "..") !== false){
			echo ('Do not hack me!!');
			die;
		}
		$this->path = $path;
		$this->datei = $datei;
		$this->encryptkey = $encryptkey;
		if(file_exists($this->path.'/kimb-data/'.$this->datei)){
			$this->dateicont = file_get_contents($this->path.'/kimb-data/'.$this->datei);
			if($this->encryptkey != 'off'){
				$this->dateicont = mcrypt_decrypt (MCRYPT_BLOWFISH , $this->encryptkey , $this->dateicont , MCRYPT_MODE_CBC, mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_BLOWFISH , MCRYPT_MODE_CBC ), MCRYPT_RAND ));
			}
		}
		else{
			$this->dateicont = 'none';
		}
		$this->dateicontanfang = $this->dateicont;
	}
	
	protected function umbruch_weg($teil, $art) {
		if( $art == 'inhalt' ){
			$teil = preg_replace('/[\r\n]+/', '<!--UMBRUCH-->', $teil);
			$teil = str_replace(array('==','--entfernt--','<[',']>'),array('=','-entfernt-','<','>'), $teil);
			return $teil;
		}
		elseif( $art == 'tag' ){
			$teil = preg_replace('/[\r\n]+/', '', $teil);
			$teil = str_replace(array('<[',']>','about:doc'),array('=','<','>','aboutdoc'), $teil);
			return $teil;
		}
		return false;
		
	}

	protected function for_inhalt_return( $inhalt ){
		$inhalt = str_replace( '<!--UMBRUCH-->' , "\r\n" , $inhalt);
		return $inhalt;
	}

	protected function inhalt_return( $inhalt ){
		if( is_array( $inhalt ) ){
			$inhalt = array_map( array($this, 'for_inhalt_return'), $inhalt);
		}
		else{
			$inhalt = $this->for_inhalt_return( $inhalt );
		}
		return $inhalt;
	}
	
	protected function file_write($inhalt, $art) {
		if( $this->dateicont == 'none' ){
			$this->dateicont = '';
		}

		$this->dateidel = 'no';

		if($this->encryptkey != 'off'){
			if($art == 'w+'){
				$inhaltwr = mcrypt_encrypt (MCRYPT_BLOWFISH , $this->encryptkey , $inhalt , MCRYPT_MODE_CBC, mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_BLOWFISH , MCRYPT_MODE_CBC ), MCRYPT_RAND ));
			}
			elseif($art == 'a+'){
				$inhaltwr = $this->dateicont.$inhalt;
				$inhaltwr = mcrypt_encrypt (MCRYPT_BLOWFISH , $this->encryptkey , $inhaltwr , MCRYPT_MODE_CBC, mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_BLOWFISH , MCRYPT_MODE_CBC ), MCRYPT_RAND ));
			}
			else{
				echo "Error on encrypting KIMBdbf!";
				die;
			}
			$artwr = 'w+';
		}
		else{
			$inhaltwr = $inhalt;
			$artwr = $art;
		}

		if($art == 'w+'){
			$this->dateicont = $inhalt;
		}
		elseif($art == 'a+'){
			$this->dateicont .= $inhalt;
		}
		else{
			echo "Error on writing KIMBdbf!";
			die;
		}
		return true;
	}

	public function __destruct() {

		if( $this->dateicontanfang != $this->dateicont && $this->dateidel == 'no' ){
			$this->dateicont = preg_replace( "/[\r\n]+[\s\t]*[\r\n]+/", "\r\n", $this->dateicont );
			$handle = fopen($this->path.'/kimb-data/'.$this->datei , 'w+');
			$ok = fwrite($handle, $this->dateicont);
			fclose($handle);
		}
		return true;
	}
	
	//KIMB-Dateien lesen
	public function read_kimb_one($teil){  //bei mehreren teilen, oberster treffer
		$teiltext = '<['.$this->umbruch_weg($teil, 'tag').']>';
		$teile = explode($teiltext, $this->dateicont);
		if( isset( $teile[1] ) ){
			return $this->inhalt_return( $teile[1] );	
		}
		else{
			return $this->inhalt_return( '' );
		}
	}
	
	public function read_kimb_all($teil){
		$teiltext = '<['.$this->umbruch_weg($teil, 'tag').']>';
		$teile = explode($teiltext, $this->dateicont);
		$count = '0';
		$i = '0';
		foreach ($teile as $teil) {
			if ($count % 2 != 0){
				$return[$i] = $teil;
				$i++;
			}
			$count++;
		}
		return $this->inhalt_return( $return );
	}
		
	public function read_kimb_search($teil, $search){
		$search = $this->umbruch_weg($search, 'inhalt');
		$teiltext = '<['.$this->umbruch_weg($teil, 'tag').']>';
		$teile = explode($teiltext, $this->dateicont);
		foreach ($teile as $teil) {
			if ($count % 2 != 0){
				if($teil == $search){return true;}
			}
			$count++;
		}
		return false;
	}
	
	//KIMB-Dateien lesen $teil++
	public function read_kimb_search_teilpl($teil, $search){
		$search = $this->umbruch_weg($search, 'inhalt');
		$count = '1';
		$gelesen = 'start';
		while($gelesen != ''){
			$teilread = $teil.$count;
			$gelesen = $this->read_kimb_one($teilread);
			if($gelesen == $search){
				return true;
			}
			$count++;
		}
		return false;
	}
	
	public function read_kimb_all_teilpl($teil){
		$teil = $this->umbruch_weg($teil, 'tag');
		$count = '1';
		$gelesen = 'start';
		$i = '0';
		while($gelesen != ''){
			$teilread = $teil.$count;
			$gelesen = $this->read_kimb_one($teilread);
			if($gelesen != '--entfernt--'){
				$return[$i] = $gelesen;
				$i++;
			}
			$count++;
		}
		array_splice($return, $i-1);
		return $this->inhalt_return( $return );
	}
	
	//KIMB-Dateien schreiben public, weiter an protected
	public function write_kimb_new($teil, $inhalt){
		$inhalt = $this->umbruch_weg($inhalt, 'inhalt');
		$teil = $this->umbruch_weg($teil, 'tag');
		return $this->write_kimb_new_pr($teil, $inhalt);
	}
	
	public function write_kimb_replace($teil, $inhalt){  //teil darf nur einmal vorhanden sein!!
		$inhalt = $this->umbruch_weg($inhalt, 'inhalt');
		$teil =  $this->umbruch_weg($teil, 'tag');
		return $this->write_kimb_replace_pr($teil, $inhalt);
	}
	
	public function write_kimb_delete($teil){  //teil darf nur einmal vorhanden sein !!
		$teil = $this->umbruch_weg($teil, 'tag');
		return $this->write_kimb_delete_pr($teil);
	}

	public function write_kimb_one($teil, $inhalt){  //teil darf nur einmal vorhanden sein !!
		$inhalt = $this->umbruch_weg($inhalt, 'inhalt');
		$teil =  $this->umbruch_weg($teil, 'tag');
		return $this->write_kimb_one_pr($teil, $inhalt);
	}

	//KIMB-Dateien schreiben protected
	protected function write_kimb_new_pr($teil, $inhalt){
		if( $this->dateicont == 'none' ){
			$writetext .= '<[about:doc]>KIMB dbf V'.self::DATEIVERSION.' - KIMB-technologies<[about:doc]>';
		}
		$writetext .= "\r\n".'<['.$teil.']>'.$inhalt.'<['.$teil.']>';
		return $this->file_write($writetext, 'a+');
	}
	
	protected function write_kimb_replace_pr($teil, $inhalt){  //teil darf nur einmal vorhanden sein!!
		$teiltext = '<['.$teil.']>';
		$teile = explode($teiltext, $this->dateicont);
		$writetext .= $teile[0];
		$writetext .= '<['.$teil.']>'.$inhalt.'<['.$teil.']>';
		$writetext .= $teile[2];
		$ok = $this->file_write($writetext, 'w+');
		if($ok == false || $this->dateicont == ''){
			return false;
		}	
		else{
			return true;
		}
	}
	
	protected function write_kimb_delete_pr($teil){  //teil darf nur einmal vorhanden sein !!
		$teiltext = '<['.$teil.']>';
		$teile = explode($teiltext, $this->dateicont);
		$writetext .= $teile[0];
		$writetext .= $teile[2];
		$ok = $this->file_write($writetext, 'w+');
		if($ok == false || $this->dateicont == ''){
			return false;
		}
		else{
			return true;
		}
	}

	protected function write_kimb_one_pr($teil, $inhalt){
		$this->write_kimb_delete_pr($teil);
		return $this->write_kimb_new_pr($teil, $inhalt);
	}
	
	//KIMB-Datei $teil++ schreiben
	protected function for_write_kimb_teilpl_add($teil){
		$count = 1;
		$gelesen = 'start';
		while($gelesen != ''){
			$teilread = $teil.$count;
			$gelesen = $this->read_kimb_one($teilread);
			$count++;
		}
		return $count-1;
	}

	protected function for_write_kimb_teilpl_del($teil){
		$count = '1';
		$gelesen = 'start';
		$i = '0';
		while($gelesen != ''){
			$teilread = $teil.$count;
			$gelesen = $this->read_kimb_one($teilread);
			$return[$i] = $gelesen;
			$i++;
			$count++;
		}
		array_splice($return, $i-1);
		return $return;
	}
	
	public function write_kimb_teilpl($teil, $inhalt, $todo){
		$inhalt = $this->umbruch_weg($inhalt, 'inhalt');
		$teil =  $this->umbruch_weg($teil, 'tag');
		if($todo == 'add'){
			$anzahl = $this->for_write_kimb_teilpl_add($teil);
			$teilneu = $teil.$anzahl;
			return $this->write_kimb_new_pr($teilneu, $inhalt);
		}
		elseif($todo == 'del'){
			$all = $this->for_write_kimb_teilpl_del($teil);

			$i = 1;
			foreach( $all as $a ){
				$this->write_kimb_delete_pr($teil.$i);
				$i++;
			}

			$i = 1;
			foreach( $all as $a ){
				if( $a == $inhalt || $a == '--entfernt--'){
					//nichts
				}
				else{
					$this->write_kimb_new_pr($teil.$i, $a); 
					$i++;
				}
			}
			return true;
		}
		else{
			return false;
		}
	}
	
	public function write_kimb_teilpl_del_all( $teil ){
		$teil = $this->umbruch_weg($teil, 'tag');
		$count = '1';
		$i = '0';
		while( true ){
			$teilread = $teil.$count;
			$gelesen = $this->read_kimb_one($teilread);
			if( !empty( $gelesen ) ){
				$this->write_kimb_delete_pr($teilread);
			}
			else{
				return true;
			}
			$count++;
		}
	}
	
	//kimb datei loeschen
	public function delete_kimb_file(){
		if(unlink($this->path.'/kimb-data/'.$this->datei)){
			$this->dateicont = 'none';
			$this->dateidel = 'yes';
			return true;
		}
		else{
			return false;
		}
	}
	
	//gesamte kimb datei ausgaben
	public function show_kimb_file() {
		return $this->dateicont;
	}
	
	//zuordnungen id
	public function read_kimb_id($id, $xxxid = '---all---') {
		$id = preg_replace( '/[^0-9]/' , '' , $id );
		$xxxid = $this->umbruch_weg($xxxid, 'tag');

		$idinfo = $this->read_kimb_one($id);
		if ( empty( $idinfo ) ){
			return false;
		}
		$idinfos = explode('==', $idinfo);
		if($xxxid == '---all---'){
			foreach ($idinfos as $info) {
				$return[$info] = $this->read_kimb_one($id.'-'.$info);
			}
			return $this->inhalt_return( $return );
		}	
		else{
			foreach ($idinfos as $info) {

				if($xxxid == $info){
					return $this->inhalt_return( $this->read_kimb_one($id.'-'.$info) );
				}
			}
			return false;
		}
	}

	public function read_kimb_all_xxxid($id) {
		$id = preg_replace( '/[^0-9]/' , '' , $id );
		$idinfo = $this->read_kimb_one($id);
		$idteile = explode('==', $idinfo);
		return $this->inhalt_return( $idteile );
	}
	
	public function search_kimb_id($search, $id) {
		$search = $this->umbruch_weg($search, 'inhalt');
		$id = preg_replace( '/[^0-9]/' , '' , $id );

		$idinfo = $this->read_kimb_one($id);
		if ( empty( $idinfo ) ){
			return false;
		}
		$idinfos = explode('==', $idinfo);
		foreach ($idinfos as $info) {
			if($this->read_kimb_one($id.'-'.$info) == $search){
				return $info;
			}
		}
		return false;
	}
	
	public function search_kimb_xxxid($search, $xxxid, $ende = 1000) {
		$search = $this->umbruch_weg($search, 'inhalt');
		$xxxid = $this->umbruch_weg($xxxid, 'tag');

		$id = 1;
		while ($id <= $ende) {
			$idinhalt = $this->read_kimb_id($id, $xxxid);
			if($idinhalt == $search){
				return $id;
			}
			$id++;
		}
		return false;		

	}

	public function next_kimb_id($ende = '1000'){
		$id = 1;
		while ($id <= $ende) {
			$idinhalt = $this->read_kimb_one($id);
			if($idinhalt == ''){
				return $id;
			}
			$id++;
		}
		return false;
	}
	
	public function write_kimb_id($id, $todo, $xxxid = '---none---', $inhalt = '---none---') {
		$id = preg_replace( '/[^0-9]/' , '' , $id );
		$xxxid = $this->umbruch_weg($xxxid, 'tag');
		$inhalt = $this->umbruch_weg($inhalt, 'inhalt');
		
		$idinfo = $this->read_kimb_one($id);
		if( empty( $idinfo ) && $todo != 'add'){
			return false;
		}
		if( empty( $idinfo ) ){
			$new = 'yes';
			$this->write_kimb_teilpl('allidslist', $id, 'add');
		}
		$idinfos = explode('==', $idinfo);
	
		if($todo == 'add' && $inhalt != '---none---' && $xxxid != '---none---'){
			if( $inhalt == '---empty---' ){
				$inhalt = '';
			}
			if( !in_array( $xxxid , $idinfos ) ){
				$this->write_kimb_new_pr($id.'-'.$xxxid, $inhalt);
				$newxxx = 'yes';
			}
			else{
				$this->write_kimb_replace_pr($id.'-'.$xxxid, $inhalt);
			}
			
			if($newxxx == 'yes' && $new != 'yes'){
				$infotag = $idinfo.'=='.$xxxid;
			}
			elseif($newxxx == 'yes'){
				$infotag = $xxxid;
			}
			else{
				foreach ($idinfos as $info) {
					$infotag .= $info.'==';
				}
				$laenge = strlen($infotag)-2;
				$infotag = substr($infotag, 0, $laenge);
			}
			
			if($new == 'yes'){
				$this->write_kimb_new_pr($id, $infotag);
			}
			else{
				$this->write_kimb_replace_pr($id, $infotag);
			}
			return true;
		}
		elseif($todo == 'del' && $xxxid == '---none---' && $inhalt == '---none---'){
			foreach ($idinfos as $info) {
				$this->write_kimb_delete_pr($id.'-'.$info);
			}
			$this->write_kimb_delete_pr($id);
			$this->write_kimb_teilpl('allidslist', $id, 'del');
			return true;
		}
		elseif($todo == 'del' && $xxxid != '---none---' && $inhalt == '---none---'){
			$gut = 0;
			foreach ($idinfos as $info) {
				if($xxxid == $info){
					$this->write_kimb_delete_pr($id.'-'.$info);
					$gut++;
				}
				if($info != $xxxid){
					$infotag .= $info.'==';
				}
			}
			$laenge = strlen($infotag)-2;
			$infotag = substr($infotag, 0, $laenge);
			if($this->write_kimb_replace_pr($id, $infotag)){
				$gut++;
			}
			if($gut == 2){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
}


//funktioneller Zugriff
//funktioneller Zugriff
//funktioneller Zugriff

//KIMB-Dateien lesen

function read_kimb_one($datei, $teil){  //bei mehreren teilen, oberster treffer
	$obj = new KIMBdbf( $datei );
	return $obj->read_kimb_one( $teil );
}
	
function read_kimb_search($datei, $teil, $search){
	$obj = new KIMBdbf( $datei );
	return $obj->read_kimb_search( $teil, $search );
}

//KIMB-Dateien schreiben
	
function write_kimb_new($datei, $teil, $inhalt){
	$obj = new KIMBdbf( $datei );
	return $obj->write_kimb_new( $teil, $inhalt );
}

function write_kimb_replace($datei, $teil, $inhalt){  //teil darf nur einmal vorhanden sein!!
	$obj = new KIMBdbf( $datei );
	return $obj->write_kimb_replace( $teil, $inhalt );
}

function write_kimb_delete($datei, $teil){  //teil darf nur einmal vorhanden sein !!
	$obj = new KIMBdbf( $datei );
	return $obj->write_kimb_delete( $teil );
}

//KIMB-Datei loeschen
function delete_kimb_datei($datei){
	$obj = new KIMBdbf( $datei );
	return $obj->delete_kimb_file();
}


?>
