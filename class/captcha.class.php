<?php defined('INIT') or die('NO INIT'); ?>

<?php 
include_once(Mod('securimage/securimage','php'));
?>

<?php
class Captcha{
	private $Captcha; // object
	private $charset, $code_length;
	private $Src;
	
	function __construct(){
		$this->Captcha = new Securimage();
		$this->Src = Plug('securimage/securimage_show','php');
	}
	
	function __destruct(){ }

	function Set($config=[]){
		foreach($config as $key => $val){ $this->Captcha->{$key} = $val; }
	}

	function Show(){ return $this->Src; }

	function Check($captcha){ return $this->Captcha->check($captcha); }
}
