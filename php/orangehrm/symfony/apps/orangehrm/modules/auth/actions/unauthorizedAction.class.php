<?php

class unauthorizedAction extends sfAction {

    /**
     * Show not authorized message
     * @return boolean true if successfully deleted, false otherwise
     */
    public function execute($request) {
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        $response = $this->getResponse();
        $response->setStatusCode(401, 'Not authorized');
        return $this->renderText("You do not have the proper credentials to access this page!");
    }

}

