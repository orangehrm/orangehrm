<?php

/* TODO: Rename this class with a proper name as it hangle both pre & post levels together  */

class orangehrmPostExecutionFilter extends sfFilter {
    const PRE_EXEC = 'pre-exec';
    const POST_EXEC = 'post-exec';
    
    const REDIRECT_ACTION = 'redirect';

    protected static $servicePool = array();

    public function execute(sfFilterChain $filterChain) {

        $module = $this->getContext()->getModuleName();
        $action = $this->getContext()->getActionName();

        $actionsStack = PluginExecutionManager::instance()->getPreExecuteMethodStack($module, $action);

        $this->performServiceOpteration($actionsStack, self::PRE_EXEC);

        $filterChain->execute();

        $actionsStack = PluginExecutionManager::instance()->getPostExecuteMethodStack($module, $action);
        $this->performServiceOpteration($actionsStack, self::POST_EXEC);
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

    protected function performServiceOpteration($actionsStack, $execLevel) {

        foreach ($actionsStack as $methodCall) {
            $service = $methodCall['service'];
            $method = $methodCall['method'];
            $messages = $methodCall['messages'];
            $resultUsageOption = isset($methodCall['useResultTo']) ? $methodCall['useResultTo'] : null;

            $serviceInstance = $this->getServiceClassInstance($service);
            if (method_exists($serviceInstance, $method)) {
                
                if ($execLevel == self::PRE_EXEC) {
                    $request = $this->getContext()->getRequest();
                    $returnValue = $serviceInstance->$method($request);
                } elseif ($execLevel == self::POST_EXEC) {
                    $form = $this->getContext()->getActionStack()->getFirstEntry()->getActionInstance()->getForm();
                    $returnValue = $serviceInstance->$method($form);
                } else {
                    // TODO: Warn
                }
                
                if (!empty($resultUsageOption)) {
                    $this->peformActionOperation($resultUsageOption, $returnValue);
                }

                if (isset($messages[$serviceInstance->getState()])) {
                    MessageRegistry::instance()->addMessage($messages[$serviceInstance->getState()], $module, $action);
                }
            }
        }
    }

    protected function peformActionOperation($operation, $result) {
        switch ($operation) {
            case self::REDIRECT_ACTION:
                if (!empty($result)) {
                    $this->getContext()->getActionStack()->getFirstEntry()->getActionInstance()->redirect($result);
                }
                break;

            default:
                break;
        }
    }

}

