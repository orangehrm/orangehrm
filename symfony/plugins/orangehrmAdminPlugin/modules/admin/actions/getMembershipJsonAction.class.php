<?php

class getMembershipJsonAction extends sfAction {

    public function execute($request) {

        $this->setLayout(false);
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
        }

        $membershipId = $request->getParameter('id');

        $service = new MembershipService();
        $membership = $service->getMembershipById($membershipId);

        return $this->renderText(json_encode($membership->toArray()));
    }

}

