<?php class_exists('Securimage') or Inc::asset(Path::plug.'securimage/securimage.php'); ?>

<?php
class Captcha{
	static private $Init = false;
	static private $captcha, $charset, $code_length, $src, $config;

	static function init($force=false){
		if(self::$Init && !$force){ return; } self::$Init = true;
		self::$src = Uri::plug('securimage/securimage_show.php');
		self::$captcha = new Securimage();
		self::$config = Inc::conf('captcha');
		self::set(self::$config);
	}

	static function set($config=[]){
		foreach($config as $key => $val){ self::$captcha->{$key} = $val; }
	}

	static function src(){ return self::$src; }

	static function check($captcha){ return self::$captcha->check($captcha); }

	// not current
	static function answer(){ return self::$captcha->getCode(); } 

	static function html($prop=''){
		return '<img id="Captcha" onload="this.src=\''.Captcha::src().'?'.bin2hex(random_bytes(16)).'\'" onclick="this.src=\''.Captcha::src().'?\' + Math.random();" style="cursor: pointer;" '.$prop.'>';
	}
} 
Captcha::init();

