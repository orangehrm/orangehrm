<?php

class displayPredefinedReportAction extends displayReportAction {

    public function  execute($request) {
        $adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);

        if(!$adminMode){
            return $this->renderText("You are not allowed to view this page!");
        }
        
        $this->getRequest()->setAttribute('skipRoundBorder', true);
        return parent::execute($request);

    }

    public function setConfigurationFactory() {

        $confFactory = new PimPredefinedReportConfigurationFactory();

        $this->setConfFactory($confFactory);
    }

    public function setListHeaderPartial() {
        
    }

    public function setParametersForListComponent() {
        return array();
    }

    public function setValues() {
        
    }

}

