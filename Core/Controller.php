<?php

    namespace Core;

    abstract class Controller
    {

        private $_registry;

        abstract public function index();

        public function __construct()
        {
            $this->_registry = Registry::getInstance();
        }

        final protected function model($name)
        {
            if (!isValue($this->_registry->$name)) {
                
            }
            return $this->_registry->$name;
        }

        final protected function library($name)
        {
            
        }

        final protected function serviceLayer($name)
        {
            
        }

    }

    