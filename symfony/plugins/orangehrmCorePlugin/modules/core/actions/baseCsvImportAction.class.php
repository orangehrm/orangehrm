<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of baseCsvImportAction
 *
 * @author orangehrm
 */
class baseCsvImportAction extends sfAction {

    public function execute($request) {
        
    }

    public function preExecute() {

        $sessionVariableManager = new DatabaseSessionManager();
        $sessionVariableManager->setSessionVariables(array(
            'orangehrm_user' => Auth::instance()->getLoggedInUserId(),
        ));
        $sessionVariableManager->registerVariables();
        $this->setOperationName(OrangeActionHelper::getActionDescriptor($this->getModuleName(), $this->getActionName()));
    }

    protected function setOperationName($actionName) {
        $sessionVariableManager = new DatabaseSessionManager();
        $sessionVariableManager->setSessionVariables(array(
            'orangehrm_action_name' => $actionName,
        ));
        $sessionVariableManager->registerVariables();
    }

}