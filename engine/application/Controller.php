<?php
namespace Application;
use Parser\Parser;
use Application\Application as App;

class Controller
{
    public function __construct()
    {

    }
    public function index()
    {


        return true;
    }

    public function getrss()
    {
        session_start();
        $dm=App::getInstance()->getDbManipulation();

        $parser= new Parser();
        /*$pages=$parser->setSearchQuery("новости")
            ->getSearchPages()
            ->grabLinks()
            ->getPages();


        $dm->saveLinks($pages);
        $rssLinks=$parser->parseGrabbedPages();

        $dm->saveRssLinks($rssLinks);
        $parser->setRssLinks($rssLinks);
        $rssContent=$parser->getRssContent();
        $_SESSION['rssContent']=$rssContent;*/
        //var_dump(array_keys($_SESSION['rssContent']));
        //exit;

        $parsedData=$parser->parseRss($_SESSION['rssContent']);

        $dm->saveRssContent($parsedData);


    }
}