<?php

class indexAction extends sfAction {

    /**
     * Index action. Forwards to OrangeHRM login page
     * @param sfRequest $request A request object
     */
    public function execute($request) {
        $this->forward('auth', 'login');
    }

}

