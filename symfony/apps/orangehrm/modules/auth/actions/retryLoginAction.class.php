<?php

class retryLoginAction extends sfAction {

    public function execute($request) {
        $this->setLayout('oldLayout');        
        $this->setTemplate('login', 'auth');
        $loginForm = new LoginForm();
        $this->message = $this->getUser()->getFlash('message');
        $this->form = $loginForm;
    }

}
