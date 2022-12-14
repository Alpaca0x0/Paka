<?php
return [
    'email' => '/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+[\.]+[a-zA-Z]+$/', // email format
    'username' => '/^[a-z]{1}[a-z0-9]{5,12}$/', // 只能是小寫英文, 英文開頭, 可含數字, 長度 6~13
    'password' => '/^(?=.*?[0-9])(?=.*?[a-z]).{8,32}$/', // 需有 數字、小寫英文, 長度 8~32
    'nickname' => '/^.{0,16}$/', // 任意字元，長度 0~16
    'captcha' => '/^[a-zA-Z0-9]{6}$/',
    'gender' => ['male','female','transgender','secret'],
    'birthday' => [18, 122], // age range
    'avatar' => [
        'width' => 320,
        'height' => 320,
        'formats' => ['image/jpeg', 'image/png'],
    ],
    'timeout' => [
        'verify' => 60*15, // 15 mins
        'login' => 60*60*6, // login session keeping 6 hours
    ],
    'description' => [
        'username' => '小寫英文及數字,首字英文,長度8~32',
        'password' => '需有數字及小寫英文,長度 8~32',
    ],
];