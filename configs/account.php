<?php
return [
    'email' => '/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+[\.]+[a-zA-Z]+$/', // email format
    'username' => '/^[a-z][a-z0-9]{5,13}$/', // 只能是小寫英文, 英文開頭, 可含數字, 長度 6~13
    'password' => '/^(?=.*?[0-9])(?=.*?[a-z]).{8,32}$/', // 需有 數字、小寫英文, 長度 8~32
    'nickname' => '/^.{0,16}$/', // 任意字元，長度 0~16
    'captcha' => '/^[a-zA-Z0-9]{6}$/',
    'gender' => ['male','female','transgender','secret'],
    'birthday' => [18, 122], // age range
    'avatar' => [
        'width' => 320,
        'height' => 320,
        'size' => 1024*1024*5, // 5mb
        'formats' => ['image/jpeg', 'image/png'],
    ],
    'timeout' => [
        'verify' => 60*15, // 15 mins
        'login' => 60*60*24*7*2, // login session keeping 2 weeks
    ],
    'description' => [
        'email' => '僅允許二級網域 (如 gmail.com)',
        'username' => '小寫英文及數字, 首字英文, 長度6~13',
        'password' => '需有數字及小寫英文, 長度 8~32',
        'captcha' => '英文大小寫不區分',
    ],
];