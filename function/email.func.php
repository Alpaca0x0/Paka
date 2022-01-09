<?php defined('INIT') or die('NO INIT'); ?>

<?php
@include_once(Clas('email'));
?>

<?php
$Email = new Email();
$Email->Init();

$temp = include(Conf('email'));
$Email->Config([
	'Host' => $temp['host'],
	'Port' => $temp['port'],
	'Email' => $temp['email'],
	'Pass' => $temp['pass'],
	'Name' => $temp['name'],
]); unset($temp);

// $result = $Email->Send($useremail, $username, $title, $html_content);
// $result[0] will be true or false that represent send successfully or not
// $result[1] is message 