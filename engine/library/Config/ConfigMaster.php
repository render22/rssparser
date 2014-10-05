<?php
namespace Config;

use Exceptions\RSSException;

/**
 * Class ConfigMaster
 * @package Config
 */
class ConfigMaster
{
    const CONFIG_FILE = "config.ini";
    private static $_instance;
    protected $data = null;

    /**
     * Singleton
     */
    private function __construct()
    {

        if (!$this->data)
            $this->loadConfig();

    }

    /**
     * @return ConfigMaster
     */
    public static function initConfig()
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
            return self::$_instance;
        } else {
            return self::$_instance;
        }
    }

    /**
     *
     */
    private function loadConfig()
    {
        try {

            $this->data = parse_ini_file(CONFIG_DIR . "/" . self::CONFIG_FILE,true);

        } catch (RSSException $e) {


        }

    }

    /**
     * @return config data
     */
    public function getConfig()
    {
        return $this->data ? $this->data : false;
    }

    public function getParam($scope,$k)
    {
        return (isset($this->data[$scope][$k]))?:false;
    }

    /**
     * Singleton
     */
    private function __clone()
    {

    }
}