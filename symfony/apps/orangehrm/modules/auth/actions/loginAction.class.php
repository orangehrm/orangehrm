<?php

class loginAction extends sfAction {

    /**
     * Login action. Forwards to OrangeHRM login page if not already logged in.
     * @param sfRequest $request A request object
     */
    public function execute($request) {
        
        $loginForm = new LoginForm();
        $this->message = $this->getUser()->getFlash('message');
        $this->form = $loginForm;
    }

}

