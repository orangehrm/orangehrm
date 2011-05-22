<?php

class coreActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {        
        $params = array();
        
        $configurationFactory = new NationalityListConfigurationFactory();
        $nationalityService = new NationalityService();

        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($nationalityService->getNationalityList());

        $this->parmetersForListCompoment = $params;
    }

}
