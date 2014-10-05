<?php
namespace Parser;
use Exceptions\RSSException;

/**
 * Class HttpClient
 * @package Parser
 */
class HttpClient
{
    protected $ch;
    protected $cHandlers = array();
    protected $chMulti = null;
    protected $options = array(
        CURLOPT_HEADER => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_ENCODING => ''
        //CURLOPT_COOKIEFILE     => 'cookie.txt',
        //CURLOPT_COOKIEJAR      => 'cookie.txt'

    );

    protected $urls;
    protected $respCollection;

    /**
     * @param $url
     * @param array $options
     */
    public function __construct($url = null, $options = array())
    {
        $this->ch = curl_init();
        foreach ($options as $option => $value)
            $this->options[$option] = $value;
        if ($url)
            $this->options[CURLOPT_URL] = $url;


    }

    /**
     * @return mixed
     */
    public function request()
    {
        /*Reset response collection with empty array*/
        $this->respCollection=array();

        curl_setopt_array($this->ch, $this->options);
        $content=curl_exec($this->ch);
        $info = curl_getinfo($this->ch);
        $this->respCollection[$info['url']]['requestInfo']=$info;
        $this->respCollection[$info['url']]['content']=$content;
        return $this->respCollection;

    }


    /**
     * @return $this
     */
    public function multiRequest($delay = null)
    {
        /*Reset response collection with empty array*/
        $this->respCollection=array();

        $this->chMulti = curl_multi_init();

        foreach ($this->urls as $k => $v) {
            $this->cHandlers[$k] = curl_init();
            $this->options[CURLOPT_URL] = $v;
            curl_setopt_array($this->cHandlers[$k], $this->options);
            curl_multi_add_handle($this->chMulti, $this->cHandlers[$k]);
        }

        $prev = $current = null;
        do {
            curl_multi_exec($this->chMulti, $current);

            if ($current < $prev) {
                $mInfo = curl_multi_info_read($this->chMulti);

                if ($mInfo) {
                    $executed = $mInfo["handle"];
                    $info = curl_getinfo($executed);
                    $content = curl_multi_getcontent($executed);
                    if($content){
                        $this->respCollection[$info["url"]]['requestInfo'] = $info;
                        $this->respCollection[$info["url"]]['content'] = $content;
                    }

                }
            }

            $prev = $current;
            if ($delay)
                usleep($delay);
        } while ($current > 0);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMultiResponse()
    {
        return $this->respCollection;
    }

    /**
     * @param $urls
     * @return $this
     */

    public function setUrls($urls)
    {
        $this->urls = $urls;
        return $this;
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->options[CURLOPT_URL] = $url;
        return $this;
    }

    /**
     * @param $options
     */
    public function setOptions($options)
    {
        foreach ($options as $option => $value)
            $this->options[$option] = $value;
        return $this;
    }

    /**
     * @param $options
     */
    public function overrideOptions($options)
    {
        $this->options = array();
        foreach ($options as $option => $value)
            $this->options[$option] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     *
     */
    public function __destruct()
    {
        curl_close($this->ch);
        if ($this->chMulti) {
            foreach ($this->urls as $k => $v) {
                curl_multi_remove_handle($this->chMulti, $this->cHandlers[$k]);
                curl_close($this->cHandlers[$k]);
            }

            curl_multi_close($this->chMulti);
        }
    }

}
