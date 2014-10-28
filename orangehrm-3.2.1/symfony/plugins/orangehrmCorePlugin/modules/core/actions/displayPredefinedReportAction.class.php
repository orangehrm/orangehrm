<?php

class displayPredefinedReportAction extends displayReportAction {

    public function  execute($request) {
        
        /* For highlighting corresponding menu item 
         * TODO: Currently menu item is hard-coded since this action is only used by PIM Reports
         */
        $this->reportPermissions = $this->getDataGroupPermissions('pim_reports');

        if(!$this->reportPermissions->canRead()){
            return $this->renderText("You are not allowed to view this page!");
        }
        
        $this->getRequest()->setAttribute('skipRoundBorder', true);
        return parent::execute($request);

    }

    public function setConfigurationFactory() {

        $confFactory = new PimPredefinedReportConfigurationFactory();
        $confFactory->setRuntimeDefinitions(array(
			    'title' => __('Report Name').' : '. $this->report->getName(),
			));

        $this->setConfFactory($confFactory);
    }

    public function setListHeaderPartial() {
        
    }

    public function setParametersForListComponent() {
        return array();
    }

    public function setValues() {
        
    }
    
    public function setInitialActionDetails( $request ){
        
    }

}

