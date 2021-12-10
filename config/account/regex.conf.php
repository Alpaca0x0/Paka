<?php defined('INIT') or die('NO INIT'); ?>

<?php
$regex = [
    'email' => '/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z]+$/', // email format
    'username' => '/^[a-z]{1}[a-z0-9]{5,12}$/', // 只能是小寫英文, 英文開頭, 可含數字, 長度 6~13
    'password' => '/^(?=.*\d)(?=.*[a-z]).{8,32}$/', // 需有 數字、小寫英文, 長度 8~32
];

return $regex;