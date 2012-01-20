<?php

class logoutAction extends sfAction {

    /**
     * Logout action
     * @param $request 
     */
    public function execute($request) {
        $authService = new AuthenticationService();
        $authService->clearCredentials();
        $this->redirect('auth/login');
    }

}

