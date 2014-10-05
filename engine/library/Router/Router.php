<?php
namespace Router;
use View\View;

class Router
{
    protected static $view;

    public static function dispatch($controller)
    {
        $url=array();
        $urlPath=parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);
        $urlParts=explode("/",$urlPath);
        $action=$urlParts[1];
        if(empty($action)){
           $action="index";
        }

        if(method_exists($controller,$action)){
            self::$view=View::getInstance();
            $enableView=call_user_func_array(array($controller,$action),array());
            if($enableView)
                self::$view->loadTemplates($action);
        }else{
            header("HTTP/1.1 404 Not Found");
            die("Wrong action");
        }

    }


}