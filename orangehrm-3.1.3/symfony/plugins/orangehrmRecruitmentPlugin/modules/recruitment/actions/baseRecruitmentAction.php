<?php

abstract class baseRecruitmentAction extends baseAction {

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
