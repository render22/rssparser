<?php
namespace Exceptions;
use Exception;
class RSSException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->printMessage($message);

    }

    public function getRSSMessage()
    {
        die($this->getMessage());
    }

    private function printMessage($message)
    {
        echo "<pre>";
        echo "<p>".$message."</p>";
        echo "<p>Error occurred in: ".$this->getFile().":".$this->getLine()."</p>";
        echo "</pre>";
        die();
    }
}