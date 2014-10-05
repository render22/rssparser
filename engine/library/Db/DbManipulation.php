<?php
namespace Db;

class DbManipulation extends Adapter
{

    public function __construct($credentials, $isDev)
    {
        parent::__construct($credentials, $isDev);

    }

    public function getDbAdapter()
    {
        return $this->_adapter;
    }

    public function saveLinks($data)
    {
        foreach ($data as $k => $info) {

            if (!$this->fetchLinkId($k)) {

                $this->evaluate('INSERT INTO resources (link) VALUES(:link)',
                    array(

                        ":link" => $k
                    )
                );
            }
        }
    }

    public function saveRssLinks($data)
    {

        foreach ($data as $url => $links) {
            $id = $this->fetchLinkId($url)['id'];

            foreach ($links as $k => $v) {

                if (!$this->getRssId($v)) {

                    $this->evaluate('INSERT INTO rss (resourceId,rsslink) VALUES(:resourceId,:rsslink)',
                        array(
                            ":resourceId" => $id,
                            ":rsslink" => $v
                        )
                    );

                }
            }


        }

    }

    public function saveRssContent($rss)
    {

        foreach ($rss as $link => $info) {

            foreach ($info as $k) {

                if (!$this->checkRssContentExists($k['link'])) {

                    $rssId = $this->getRssId($link);

                    $this->evaluate('INSERT INTO rsscontent (rssId,link,title,description)
                        VALUES(:rssId,:link,:title,:description)',
                        array(
                            ":rssId" => $rssId['id'],
                            ":link" => $k['link'],
                            ":title" => $k['title'],
                            ":description" => $k['description']
                        )
                    );
                }
            }


        }


    }

    /**
     * @param $link
     * @return bool
     */
    public function checkRssContentExists($link)
    {
       $this->evaluate('
            SELECT id FROM rsscontent WHERE link = :rsslink LIMIT 1',
            array(
                ":rsslink" =>$link
            )
        );

        return $this->fetch(\PDO::FETCH_ASSOC);
    }

    public function getRssId($link)
    {

        $this->evaluate('
            SELECT id FROM rss WHERE rsslink=:rsslink LIMIT 1',
            array(
                ":rsslink" => $link
            )
        );

        return $this->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchLinkId($link)
    {
        $this->evaluate('SELECT id FROM resources WHERE link LIKE :link LIMIT 1',
            array(
                ":link" => "%" . $link . "%"
            )
        );
        return $this->fetch(\PDO::FETCH_ASSOC);
    }

    public function getRssList()
    {
        $this->evaluate('
            SELECT resources.link AS reslink, rsscontent.* FROM resources
            RIGHT JOIN rss ON rss.resourceId=resources.id
            RIGHT JOIN rsscontent ON rsscontent.rssId=rss.id
            '
        );
        $result=$this->fetchAll(\PDO::FETCH_ASSOC);
        $resultArray=array();
        $i=0;
        foreach($result as $k=>$v){
            $resultArray[$v['reslink']][$i]['link']=$v['link'];
            $resultArray[$v['reslink']][$i]['title']=$v['title'];
            ++$i;

        }
        return $resultArray;
    }


}