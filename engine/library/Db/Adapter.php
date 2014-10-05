<?php
namespace Db;

use Exceptions\DbException;

class Adapter
{
    protected $_adapter;
    protected $lastStatement;
    protected $isDev;

    public function __construct($credentials, $isDev)
    {
        //We use PDO for security reasons and greater efficiency
        $this->isDev = $isDev;
        try {
            $this->_adapter = new \PDO("mysql:host=" . $credentials["dbhost"] . ";dbname=" . $credentials["dbname"], $credentials["dbuser"], $credentials["dbpassword"]);
            $this->_adapter->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->_adapter->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        } catch (\PDOException $e) {
            if ($isDev)
                die($e->getMessage());
        }

    }

    public function evaluate($sql, $params = null)
    {

            try {

                $this->lastStatement = $this->_adapter->prepare($sql);

                return $this->lastStatement->execute($params);
            } catch (\PDOException $e) {
                if ($this->isDev)
                    die($e->getMessage());
            }


    }

    public function fetch($type)
    {
       //die($this->lastStatement->queryString);
        if($this->lastStatement){

            return $this->lastStatement->fetch($type);

        }else{
            return false;
        }


    }

    public function fetchAll($type)
    {
        if($this->lastStatement){

             return $this->lastStatement->fetchAll($type);

        }else{
             return false;
        }


    }
}