<?php
namespace Parser;

use Exceptions\RSSException;
use Parser\HttpClient;


class Parser
{
    const SEARCH_URL = "https://www.google.by/search?q=";
    protected $_client;
    protected $query;
    protected $response;
    protected $links = array();
    protected $pages = array();
    protected $rsslinks = array();
    protected $rss = array();

    public function __construct()
    {
        $this->_client = new HttpClient();

    }

    public function setSearchQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    public function getHttpClient()
    {
        return $this->_client;
    }

    public function getSearchPages()
    {
        $this->_client->setUrl(self::SEARCH_URL . $this->query);
        $this->response = $this->_client->request();

        if (!$this->response[self::SEARCH_URL . $this->query]['content'])
            throw new RSSException("Failed with retrieving search results");
        return $this;

    }

    public function grabLinks()
    {

        preg_match_all('/<h3 class\="r"><a href\="\/url\?q\=(.*)\&/U', $this->response[self::SEARCH_URL . $this->query]['content'], $matches);
        $this->links = $matches[1];


        return $this;

    }

    public function getLinks()
    {
        return $this->links;
    }

    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    public function getPages()
    {

        $this->pages = $this->_client->setUrls($this->links)->multiRequest()->getMultiResponse();

        return $this->pages;
    }

    public function parseGrabbedPages()
    {

        foreach ($this->pages as $url => $info) {

            preg_match_all('/<link(.*)>/U', $info['content'], $matches);
            $concatLinks = array();
            if (is_array($matches[1])) {
                foreach ($matches[1] as $k => $v) {
                    if (stristr($v, "application/rss")) {
                        preg_match('/href\=(\"|\')(.*)(\"|\')/U', $v, $href);
                        if (!strstr($href[2], $url))
                            $concatLinks[] = rtrim($url, "/") . $href[2];
                        else
                            $concatLinks[] = $href[2];

                    }
                }
            }
            if (count($concatLinks))
                $this->rss[$url] = $concatLinks;
        }

        return $this->rss;

    }

    public function setRssLinks(array $data)
    {
        foreach ($data as $k => $v) {

            foreach ($v as $kk => $vv) {

                $this->rsslinks[] = $vv;
            }
        }

    }

    public function getRssContent()
    {


        $this->rss = $this->_client->setUrls($this->rsslinks)->setOptions(
            array(
                CURLOPT_FOLLOWLOCATION => false
            )
        )->multiRequest(500)->getMultiResponse();

        return $this->rss;

    }

    public function parseRss($rss)
    {
        $data = array();
        libxml_use_internal_errors(true);
        foreach ($rss as $url => $info) {
            $i = 0;
            //if ($this->is_valid_xml($info['content'])) {
            try{
                $se = new \SimpleXMLElement($info['content']);
            }catch(\Exception $e){

            }

                foreach ($se->channel->item as $item) {

                    $data[$url][$i]['link'] = (string)$item->link;
                    $data[$url][$i]['title'] = (string)$item->title;
                    $data[$url][$i]['description'] = (string)$item->description;

                    ++$i;
                }
            //} else {
               // echo $url . "</br>";
            //}


        }

        return $data;
    }


    public function getresponse()
    {
        return $this->response;
    }

    protected function is_valid_xml($xml)
    {
        $prev = libxml_use_internal_errors(true);
        $ret = true;
        try {
            new \SimpleXMLElement($xml, 0, true);
        } catch(\Exception $e) {
            $ret = false;
        }
        if(count(libxml_get_errors()) > 0) {
            // There has been XML errors
            $ret = false;
        }
        // Tidy up.
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        return $ret;
    }
}