<?php
/****************************************************************/
# Read Me
# - The project requires php8 version (or higher)
#
# - Do NOT output any thing on this page
#
/****************************************************************/
# Initialize
date_default_timezone_set('Asia/Taipei');
define('INIT', true);
require('config.php');

/****************************************************************/
# Init
if(!INIT){ die('The website is under maintenance. Please come back later.<br>網站正在維修中，請稍後再回來看看吧。'); }
# Debug mode will display all errors
if(DEBUG){ ini_set('display_errors',1); error_reporting(E_ALL); }
else{ ini_set('display_errors',0); error_reporting(0); }
# Check libs
$libraries = ['gd', 'pdo'];
foreach ($libraries as $library) 
    if(!get_extension_funcs($library))
        if(DEV) die("Library {$library} not even be installed.");

/****************************************************************/
# Basic web info

# Domain
define('Domain', explode(':', isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''))[0]);

# Port
define('Port', isset($_SERVER['HTTP_X_FORWARDED_PORT']) ? $_SERVER['HTTP_X_FORWARDED_PORT'] : $_SERVER['SERVER_PORT']);

# Protocol
define('Protocol', isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http'));

# Client IP
define('IP', isset($_SERVER['HTTP_X_REMOTE_ADDR']) ? $_SERVER['HTTP_X_REMOTE_ADDR'] : $_SERVER["REMOTE_ADDR"]);

# Paths
class Path{
    const 
        init = 'init/',
        clas = 'classes/',
        func = 'functions/',
        page = 'pages/',
        config = 'configs/',
        component = 'components/',
        router = 'routers/',
        asset = 'assets/',
        lib = 'libraries/'
    ;
    const
        js = 'js/',
        css = 'css/',
        img = 'img/',
        auth = 'auth/',
        api = 'api/',
        plugin = 'plugin/'
    ;
}

# Init classes, functions...
$filenames = glob(Local.Path::clas.Path::init."*.php");
array_push($filenames, ...glob(Local.Path::func.Path::init."*.php"));
foreach($filenames as $filename){ require($filename); }

/****************************************************************/

