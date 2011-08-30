<?php

/**
 * Show not authorized message
 */
class unauthorizedAction extends sfAction {

    /**
     * @param sfRequest $request
     */
    public function execute($request) {
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        $response = $this->getResponse();
        $response->setStatusCode(401, 'Not authorized');
        return $this->renderText("You do not have the proper credentials to access this page!");
    }

}
