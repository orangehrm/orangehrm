<?php

class coreActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        $nationalityService = new NationalityService();

        $defs = sfYaml::load(sfConfig::get('sf_root_dir') . '/plugins/orangehrmCorePlugin/config/list_component.yml');
        $params = $defs['Sample'];
        $params['data'] = $nationalityService->getNationalityList();
        $params['hasSelectableRows'] = false;
        $params['columns'] = array();

        $this->listParams = $params;
    }

}

