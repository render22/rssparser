<?php
namespace View;
use Exceptions\RSSException;


/**
 * Class View
 * @package View
 */
class View
{
    const LAYOUT="layout.phtml";
    private static $_instance;
    protected  $template;
    protected $methods=array();


    private function  __construct()
    {
        header("Content-type:text/html;charset=utf-8");

    }



    public static function getInstance()
    {

        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
            return self::$_instance;
        } else {
            return self::$_instance;
        }
    }

    /**
     * @param $templateName
     * @throws \Exceptions\ViewException
     */
    public  function loadTemplates($templateName)
    {
        $this->template=$templateName.".phtml";
        if(!file_exists(TEMPLATES_DIR."/layout.phtml"))
            throw new RSSException("Can't load".TEMPLATES_DIR."/layout.phtml");
        include_once TEMPLATES_DIR."/layout.phtml";


    }

    /**
     * @throws \Exceptions\ViewException
     */
    public function getContent()
    {
        if(!file_exists(TEMPLATES_DIR."/".$this->template))
            throw new ViewException(TEMPLATES_DIR."/".$this->template);
        include_once TEMPLATES_DIR."/".$this->template;

    }
}