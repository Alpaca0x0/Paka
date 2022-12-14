<?php
class Router{
    static private $uri=null, $preRoot='/'; // /path/path2/, /path/path2/
    static private $root, $local; // /path/, /pages/path/
    static private $args, $path=''; // [path,path2], path2

    # init the router, only using on the main router
    static function init($uri){
        if(is_null(self::$uri)){ self::$uri = $uri; }
        self::new(Path::page);
    }
    # create a new one router
    static function new($local){
        self::$local = $local;
        self::$root = self::$preRoot;
        self::$args = explode('/', rtrim(substr(self::$uri, strlen(self::$root)), '/') );
        self::$path = rtrim(substr(self::$uri, strlen(self::$preRoot)), '/');
    }
    # get info
    static function uri() { return self::$uri; }
    static function root() { return self::$root; }
    static function local() { return self::$local; }
    static function path() { return self::$path; }
    static function arg($idx=null, $replace=false){
        if (is_null($idx)) { return self::$args; }
        return isset(self::$args[$idx]) ? self::$args[$idx] : $replace; 
    }
    # get if match then callback
    static function get($uri, $callback=null){
        $uri = is_array($uri) ? $uri : [$uri];
        foreach($uri as $u){
            if (!str_starts_with(self::$uri, $u)) { continue; };
            self::$preRoot = $u;
            if(is_callable($callback)){ call_user_func($callback); }
            else if (is_string($callback)) { self::route($callback); }
        }
    }
    # same as get(), but needs completed equal
    static function equal($uri, $callback=null){
        $uri = is_array($uri) ? $uri : [$uri];
        foreach($uri as $u){
            if($u !== self::$uri){ continue; }
            self::$preRoot = $u;
            if(is_callable($callback)){ call_user_func($callback); }
            else if (is_string($callback)) { self::route($callback); }
        }
    }

    # jump to another router
    static function route($router){
        Inc::router($router);
        die();
    }

    # redirect page
    static function redirect($uri){
        header('Location: '.Root.ltrim($uri,'/'));
        die();
    }

    // # same as get(), but only difference is the try() will try other possibility of path when file not found.
    static function tryFile($file=null){
        $file = is_null($file) ? self::local().self::path() : $file;
        # find if it's in public space
        $filepath = File::in('/')::exist($file);
        # if file not exist, try append "/index.php" after it
        if($filepath !== false){ $filepath = $file; }
        else{
            foreach(['', '.php', '/index.php'] as $exName){
                $filepath = File::in('/')::exist(rtrim($file, '/').$exName);
                if($filepath){
                    $file = rtrim($file, '/').$exName;
                    break;
                }
            }
        }
        return $filepath === false ? false : '/'.ltrim($file,'/');
    }

    // function getMimeType($file=null){
    //     $filepath = $this->get($file);
    //     if($filepath !== false){ return File::getMimeType($filepath); }
    //     return false;
    // }
    // function tryMimeType($file=null){
    //     $filepath = $this->try($file);
    //     if($filepath !== false){ return File::getMimeType($filepath); }
    //     return false;
    // }

    // function display($file=null){
    //     $filepath = $this->get($file);
    //     if($filepath !== false){ require($filepath); return $filepath; }
    //     return false;
    // }
    # same as display(), but only difference is the view() will try other possibility of path when file not found.
    static function view($file=null){
        if(headers_sent()){ die('Router Error: Headers already been sent.'); }
        $file = is_null($file) ? null : self::local().$file;
        $filepath = self::tryFile($file);
        if($filepath !== false){
            $mimeType = File::getMimeType(Local.$filepath);
            header('Content-Type: '.$mimeType);
            if($mimeType === 'text/html'){ require(Local.$filepath); }
            else{ readfile(Local.$filepath); }
            die();
            // return Local.$filepath;
        }
        return false;
    }
}