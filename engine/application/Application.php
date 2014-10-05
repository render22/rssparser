<?php
namespace Application;

use Config\ConfigMaster as Config;
use Exceptions\RSSException;
use Router\Router;
use Application\Controller;
use Db\DbManipulation;

final class Application
{
    private $config;
    private $dbmanipulation;
    private $controller;
    private $instances=array();
    private static $_instance=null;
    private function __construct()
    {
        $this->_initConfig();

    }

    public static function getInstance()
    {
        return self::$_instance;
    }

    public static function run()
    {
        if(!self::$_instance){
            self::$_instance=new self();
            self::$_instance->init();
        }

    }

    public function init()
    {
        $this->dbmanipulation= new DbManipulation($this->config["dbsettings"],$this->config["main"]["isDevelopment"]);
        $this->controller=new Controller();
        Router::dispatch($this->controller);
    }

    private function _initConfig()
    {

        $this->config = Config::initConfig()->getConfig();

    }

    public function __call($param,$args)
    {
        $parts=preg_split("/(?=[A-Z])/",$param,2);
        $property=strtolower($parts[1]);

        if(property_exists($this,$property)){
            if($parts[0] == 'get')
                return $this->{$property};
            elseif($parts[0] == 'set'){
                $this->{$property}=$args[0];
            }

        }else{
            throw new RSSException("Requested property or class does not exist");
        }


    }


}