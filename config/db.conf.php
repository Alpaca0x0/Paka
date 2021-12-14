<?php defined('INIT') or die('NO INIT'); ?>

<?php
define('DB', [
	'type'=>'mysql',
	'host'=>'localhost',
	'user'=>'root',
	'pass'=>'a1pacapassw0rd',
	'name'=>'AlpacaTech',
	'tables'=>[
		'account','post','post_event'
	],
	'columns'=>[
		'account'=>[
			'id',
			'identity',
			'username',
			'password',
			'email',
		],
	],
]);


