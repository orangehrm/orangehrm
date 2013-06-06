<?php

class ohrmPluginPannelComponent extends sfComponent {

    public function execute($request) {
        $module = sfContext::getInstance()->getModuleName();
        $action = sfContext::getInstance()->getActionName();
        $this->subComponents = PluginUIManager::instance()->getUISubComponents($module, $action);
    }

}
