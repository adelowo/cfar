<?php

namespace Adelowo\Cfar;

/**
 * @author Lanre Adelowo
 */
class CfarException extends \Exception
{

    protected $message;

    const INVALID_DECLARATION = "Invalid route declaration";

    public function __construct($message)
    {
        $this->message = $message;
    }
<<<<<<< HEAD
=======

    public function __toString()
    {
        return self::INVALID_DECLARATION.". The ".$this->getMessage();
    }
>>>>>>> 6618892fbc932ea00c4467efcebd63aad6eee54b
}
