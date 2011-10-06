<?php

/* TODO: Rename this class with a proper name as it hangle both pre & post levels together  */

class orangehrmPostExecutionFilter extends sfFilter {

    protected static $servicePool = array();

    public function execute(sfFilterChain $filterChain) {

        $module = $this->getContext()->getModuleName();
        $action = $this->getContext()->getActionName();

        $actionsStack = PluginExecutionManager::instance()->getPreExecuteMethodStack($module, $action);
        foreach ($actionsStack as $methodCall) {
            $service = $methodCall['service'];
            $method = $methodCall['method'];
            $messages = $methodCall['messages'];

            $serviceInstance = $this->getServiceClassInstance($service);
            if (method_exists($serviceInstance, $method)) {
                $serviceInstance->$method($this->getContext()->getRequest());
                if (isset($messages[$serviceInstance->getState()])) {
                    MessageRegistry::instance()->addMessage($messages[$serviceInstance->getState()], $module, $action);
                }
            }
        }

        $filterChain->execute();

        $actionsStack = PluginExecutionManager::instance()->getPostExecuteMethodStack($module, $action);
        foreach ($actionsStack as $methodCall) {
            $service = $methodCall['service'];
            $method = $methodCall['method'];
            $messages = $methodCall['messages'];

            $serviceInstance = $this->getServiceClassInstance($service);
            if (method_exists($serviceInstance, $method)) {
                $form = $this->getContext()->getActionStack()->getFirstEntry()->getActionInstance()->getForm();
                $serviceInstance->$method($form);
                if (isset($messages[$serviceInstance->getState()])) {
                    MessageRegistry::instance()->addMessage($messages[$serviceInstance->getState()], $module, $action);
                }
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

