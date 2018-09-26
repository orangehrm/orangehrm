<?php

class loginAction extends sfAction {

    /**
     * Login action. Forwards to OrangeHRM login page if not already logged in.
     * @param sfRequest $request A request object
     */
    public function execute($request) {

        if (isset($_SESSION['Installation'])) {
            $this->sendInstallationStatus();
        }
        
        $loginForm = new LoginForm();
        $this->message = $this->getUser()->getFlash('message');
        $this->form = $loginForm;
    }

    /**
     * Send instance installation status to OrangeHRM
     */
    public function sendInstallationStatus() {
        $_SESSION['defUser']['type'] = 3;
        $orangeHrmRegistrationService = new OrangeHrmRegisterService();
        $orangeHrmRegistrationService->sendRegistrationData();
    }

}

