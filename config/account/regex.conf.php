<?php defined('INIT') or die('NO INIT'); ?>

<?php
$regex = [
    'email' => '/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z]+$/', // email format
    'username' => '/^[a-z]{1}[a-z0-9]{5,12}$/', // 只能是小寫英文, 英文開頭, 可含數字, 長度 6~13
    'password' => '/^(?=.*?[0-9])(?=.*?[a-z]).{8,32}$/', // 需有 數字、小寫英文, 長度 8~32
    'nickname' => '/^.{0,16}$/', // 任意字元，長度 0~16
    'gender' => ['male','female','transgender','secret'],
    'avatar' => [
        'width' => 320,
        'height' => 320,
        'formats' => ['image/jpeg', 'image/png'],
    ],
    'verify' => [
        'timeout' => 60*15, // 15 mins
    ],
];

return $regex;