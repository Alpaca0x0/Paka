<?php defined('INIT') or die('NO INIT'); ?>

<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require Clas('PHPMailer-6.5.3/src/Exception','php');
require Clas('PHPMailer-6.5.3/src/PHPMailer','php');
require Clas('PHPMailer-6.5.3/src/SMTP','php');
?>

<?php
class Email{
	private $Mailer; // object
	private $Host, $Port, $Email, $Pass, $Name; // primary config
	
	function __construct(){
		$this->Mailer = new PHPMailer();
	}
	
	function __destruct(){ }

	function Init(){
		$this->Name = 'Service';
		$this->Port = 25;
		//
		$this->Mailer->SMTPDebug = false; // 1,2,3,4 or false 
		$this->Mailer->IsSMTP(); // Using SMTP method
		$this->Mailer->SMTPAuth = true;
		$this->Mailer->SMTPSecure = 'tls'; // Using TLS
		$this->Mailer->IsHTML(true); // the content is html format
		$this->Mailer->CharSet = "utf8";
		$this->Mailer->Timeout = 30;
	}

	function Config($conf=[]){
		// $conf = [
		// 	'Host' => '',
		// 	'Port' => '',
		// 	'Email' => '',
		// 	'Pass' => '',
		//	'Name' => '',
		// ];

		// must give
		if(isset($conf['Host'])){ $this->Host = trim($conf['Host']); };
		if(isset($conf['Email'])){ $this->Email = trim($conf['Email']); }
		if(isset($conf['Pass'])){ $this->Pass = $conf['Pass']; }
		// options
		if(isset($conf['Port'])){ $this->Port = (int)$conf['Port']; }
		if(isset($conf['Name'])){ $this->Name = $conf['Name']; }
		if(isset($conf['IsHTML'])){ $this->Mailer->IsHTML((bool)$conf['IsHTML']); }
		if(isset($conf['Timeout'])){ $this->Mailer->Timeout = (int)$conf['Timeout']; }

		$this->Mailer->Host = $this->Host;
		$this->Mailer->Port = $this->Port;
		$this->Mailer->Username = $this->Email;
		$this->Mailer->Password = $this->Pass;
		$this->Mailer->From = $this->Email;
		$this->Mailer->FromName = $this->Name;
	}

	function Send($recipient, $recipient_name, $subject, $message){
		$this->Mailer->AddAddress($recipient, $recipient_name); // can be multiple, just call AddAddress again before Send()
		$this->Mailer->Subject = $subject;
		$this->Mailer->Body = $message;
		// $this->Mailer->AddAttachment('path/filename'); // add file into mail
		// Send 
		// error
		if(!$this->Mailer->Send()){ return [false, $this->Mailer->ErrorInfo]; }
		// success
		else{ return [true, 'Send successfuly']; }
	}
}
