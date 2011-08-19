<?php

class orangehrmPostExecutionFilter extends sfFilter {

    protected static $servicePool = array();
    
    public function execute(sfFilterChain $filterChain) {

        $filterChain->execute();

        $module = $this->getContext()->getModuleName();
        $action = $this->getContext()->getActionName();
        
        $actionsStack = PluginExecutionManager::instance()->getPostExecuteMethodStack($module, $action);
        foreach ($actionsStack as $methodCall) {
            list($service, $method) = explode('.', $methodCall);
            $serviceInstance = $this->getServiceClassInstance($service);
            if (method_exists($serviceInstance, $method)) {
                $form = $this->getContext()->getActionStack()->getFirstEntry()->getActionInstance()->getForm();

                $serviceInstance->$method($form);
            }
        }

    }

    /**
     *
     * @param string $className 
     * @return BaseService
     */
    protected function getServiceClassInstance($className) {
        if (!array_key_exists($className, self::$servicePool)) {
            self::$servicePool[$className] = new $className();
        }
        return self::$servicePool[$className];
    }
}

