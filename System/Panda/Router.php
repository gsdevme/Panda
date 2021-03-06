<?php

    namespace System\Panda;

    use \System\Panda\Exceptions\RouterException;
    use \ReflectionException;
    use \Exception;
    use \ReflectionClass;
    use \ReflectionMethod;

    class Router
    {

        private $_panda, $_request, $_controller = null, $_method, $_args;

        /**
         * @param Request $request 
         */
        public function __construct(Request $request)
        {
            $this->_panda = Panda::getInstance();

            // Get App Name, i.e. Index
            $this->_panda->appName = $this->_getApp($request->getApp());
            $this->_panda->appRoot = $this->_panda->root . $this->_panda->appName . '/';

            if ((isset($this->_panda->rewrites)) && (($rewrite = preg_replace($this->_panda->rewrites['pattern'], $this->_panda->rewrites['replacement'], $request->getRequest())))) {
                $this->_request = \SplFixedArray::fromArray(explode('/', $rewrite));
            } else {
                $this->_request = \SplFixedArray::fromArray(($request->getRequest() !== null) ? explode('/', $request->getRequest()) : array($this->_panda->defaultController, $this->_panda->defaultMethod));
            }

            $controllerName = $this->_getController();
            $methodName = $this->_getMethod();

            $this->_panda->controller = $controllerName;
            $this->_panda->method = $methodName;

            $this->_request->next();

            if ($this->_request->valid()) {
                return new ControllerFactory($this->_controller, $this->_method, array_slice($this->_request->toArray(), $this->_request->key()));
            }

            return new ControllerFactory($this->_controller, $this->_method);
        }

        /**
         * Gets the app name or loads default
         * @param type $app 
         */
        private function _getApp($app, $recursive=true)
        {
            if (is_readable($this->_panda->root . $app . '/Config.php')) {
                require_once $this->_panda->root . $app . '/Config.php';

                if (isValue($config)) {
                    $this->_panda->import($config);
                }

                return $app;
            }

            if ($recursive === true) {
                return $this->_getApp($this->_panda->defaultApp, false);
            }

            throw new RouterException('Panda failed to find the application with the name: ' . $app . ', Please check you have a folder with a Config.php', 500, null);
        }

        /**
         * Gets the controller and returns the name
         * @return string 
         */
        private function _getController($folder=null)
        {
            try {
                if ($this->_request->valid()) {
                    $this->_controller = new ReflectionClass('Controllers\\' . $folder . ucfirst($this->_request->current()));
                } else {
                    $this->_controller = new ReflectionClass('Controllers\\' . $folder . ucfirst($this->_panda->defaultController));
                }

                if ($this->_controller->isInstantiable()) {
                    if (($this->_panda->mode == 'HTTP') && ($this->_controller->getParentClass()->name == 'Controllers\CLIController')) {
                        throw new RouterException('This controller is for CLI use only, Controller: ' . ucfirst($this->_request->current()), 404);
                    }

                    return ucfirst($this->_controller->name);
                }
            } catch (Exception $e) {
                if (is_dir($this->_panda->appRoot . 'Controllers/' . str_replace('\\', '/', $folder) . ucfirst($this->_request->current()))) {
                    $folder .= ucfirst($this->_request->current()) . '\\';

                    $this->_request->next();
                    return $this->_getController($folder);
                }
            }

            throw new RouterException('Panda failed the find the controller, with the name ' . ucfirst($this->_request->current()), 404, ifsetor($e, null));
        }

        /**
         * Gets the method and returns the name
         * @param string
         */
        private function _getMethod()
        {
            $this->_request->next();

            if ($this->_request->valid()) {
                $method = $this->_request->current();
            } else {
                $method = $this->_panda->defaultMethod;
            }

            try {
                $this->_method = new ReflectionMethod($this->_controller->name, $method);

                if (($this->_method->isPublic()) && (!$this->_method->isConstructor())) {
                    return $method;
                }
            } catch (ReflectionException $e) {
                
            }

            throw new RouterException('Panda failed to find the method with the name: ' . $method . ' within ' . $this->_controller->name, 404, ifsetor($e, null));
        }

    }

    