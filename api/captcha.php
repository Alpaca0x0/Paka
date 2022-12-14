<?php
header('Content-Type: application/json; charset=utf-8');
if(!DEV){ die(json_encode("")); }
Inc::clas('captcha');
die(json_encode(Captcha::answer()));