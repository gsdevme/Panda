<?php

    namespace Core\Exceptions;

    class ExceptionAbstract extends \Exception
    {

        public function __construct($message, $code=null, \Exception $previous=null)
        {
            parent::__construct($message, $code, $previous);
        }

    }