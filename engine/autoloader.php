<?php
spl_autoload_register(function($className){

    $parts=explode("\\",$className);

    if($parts[0]=="Application"){
        $parts[0]=strtolower($parts[0]);

        $path=ENGINE_DIR."/".implode("/",$parts);

    }else{
        $path=ENGINE_DIR."/library/".implode("/",$parts);
    }
    $path=$path.".php";
    if(file_exists($path)){
        require_once $path;
    }else{
        die("Can't load ".$className." with path:".$path);
    }
});