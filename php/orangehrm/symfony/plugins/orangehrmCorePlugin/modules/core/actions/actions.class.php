<?php

class coreActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {        
        $params = array();
        
        $configurationFactory = new AncientNationalityListConfigurationFactory();
        $nationalityService = new NationalityService();

        ohrmListComponent::setConfigurationFactory($configurationFactory);
//        ohrmListComponent::setListData($nationalityService->getNationalityList());
        ohrmListComponent::setListData(array(
            array(1, 'Spartan'),
            array(2, 'Atlantian'),
            array(3, 'Persian'),
        ));

        $this->parmetersForListCompoment = $params;
    }

}
