<?php

class getSubscriberJsonAction extends sfAction {

    public function execute($request) {

        $this->setLayout(false);
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
        }

        $subscriberId = $request->getParameter('id');

        $service = new EmailNotificationService();
        $subscriber = $service->getSubscriberById($subscriberId);

        return $this->renderText(json_encode($subscriber->toArray()));
    }

}

