<?php defined('INIT') or die('NO INIT'); ?>

<?php
define('DB', [
	'type'=>'mysql',
	'host'=>'localhost',
	'user'=>'root',
	'pass'=>'a1pacapassw0rd',
	'name'=>'AlpacaTech',
	'tables'=>[
		'account',
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


