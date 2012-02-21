<?php

class validateCredentialsAction extends sfAction {

    protected $authenticationService;

    public function execute($request) {
        if ($request->isMethod(sfWebRequest::POST)) {
            $username = $request->getParameter('txtUsername');
            $password = $request->getParameter('txtPassword');
            $additionalData = array(
                'timeZoneOffset' => $request->getParameter('hdnUserTimeZoneOffset', 0),
            );

            try {
                $success = $this->getAuthenticationService()->setCredentials($username, $password, $additionalData);
                if ($success) {
                    $this->redirect(public_path('../../index.php'));
                } else {
                    $this->getUser()->setFlash('message', __('Invalid credentials'), true);
                    $this->forward('auth', 'retryLogin');
                }
            } catch (AuthenticationServiceException $e) {
                $this->getUser()->setFlash('message', $e->getMessage(), false);
                $this->forward('auth', 'login');
            }
        }

        return sfView::NONE;
    }

    /**
     *
     * @return AuthenticationService 
     */
    public function getAuthenticationService() {
        if (!isset($this->authenticationService)) {
            $this->authenticationService = new AuthenticationService();
            $this->authenticationService->setAuthenticationDao(new AuthenticationDao());
        }
        return $this->authenticationService;
    }

}
