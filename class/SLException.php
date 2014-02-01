<?php
class SLException extends Exception
{
    private $httpResponseCode;

    public function __construct($httpResponseCode, $errorMessage)
    {
        parent::__construct($errorMessage);
        $this->httpResponseCode = $httpResponseCode;
    }

    public function getHttpResponseCode()
    {
        return $this->httpResponseCode;
    }
}