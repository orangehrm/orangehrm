<?php

class getNationalityJsonAction extends sfAction {

    public function execute($request) {

        $this->setLayout(false);
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
        }

        $nationalityId = $request->getParameter('id');

        $service = new NationalityService();
        $nationality = $service->getNationalityById($nationalityId);

        return $this->renderText(json_encode($nationality->toArray()));
    }

}

