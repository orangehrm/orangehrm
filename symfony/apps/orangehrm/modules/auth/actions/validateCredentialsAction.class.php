<?php

class validateCredentialsAction extends ohrmBaseAction
{

    protected $authenticationService;
    protected $homePageService;
    protected $beaconCommunicationService;
    protected $loginService;
    private $passwordHelper;

    public function getPasswordHelper()
    {
        if (!($this->passwordHelper instanceof PasswordHelper)) {
            $this->passwordHelper = new PasswordHelper();
        }
        return $this->passwordHelper;
    }

    /**
     *
     * @return BeaconCommunicationsService
     */
    public function getBeaconCommunicationService()
    {
        if (is_null($this->beaconCommunicationService)) {
            $this->beaconCommunicationService = new BeaconCommunicationsService();
        }
        return $this->beaconCommunicationService;
    }

    public function getLoginService()
    {
        if (is_null($this->loginService)) {
            $this->loginService = new LoginService();
        }
        return $this->loginService;
    }

    public function execute($request)
    {

        if ($request->isMethod(sfWebRequest::POST)) {
            $loginForm = new LoginForm();
            $csrfToken = $request->getParameter('_csrf_token');
            if ($csrfToken != $loginForm->getCSRFToken()) {
                $this->getUser()->setFlash('message', __('Csrf token validation failed'), true);
                $this->handleBadRequest();
                $this->forward('auth', 'retryLogin');
            }
            $username = $request->getParameter('txtUsername');
            $password = $request->getParameter('txtPassword');
            $additionalData = array(
                'timeZoneOffset' => $request->getParameter('hdnUserTimeZoneOffset', 0),
            );

            try {
                $this->sendInstallationStatus();
                $success = $this->getAuthenticationService()->setCredentials($username, $password, $additionalData);

                if ($success) {
                    if (!$this->getPasswordHelper()->isPasswordStrongWithEnforcement($password)) {
                        $_SESSION['username'] = $username;
                        $this->redirect('auth/changeWeakPassword');
                    }

                    $this->getBeaconCommunicationService()->setBeaconActivation();
                    $this->getLoginService()->addLogin();
                    $this->redirect($this->getHomePageService()->getPathAfterLoggingIn($this->getContext()));

                } else {
                    $this->getUser()->setFlash('message', __('Invalid credentials'), true);
                    $this->forward('auth', 'retryLogin');
                }
//                }
            } catch (AuthenticationServiceException $e) {

                $this->getUser()->setFlash('message', $e->getMessage(), false);
                $this->forward('auth', 'login');
            }
        }

        $this->forward('auth', 'login');
    }

    /**
     *
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        if (!isset($this->authenticationService)) {
            $this->authenticationService = new AuthenticationService();
        }
        return $this->authenticationService;
    }

    public function getHomePageService()
    {

        if (!$this->homePageService instanceof HomePageService) {
            $this->homePageService = new HomePageService($this->getUser());
        }

        return $this->homePageService;
    }

    public function setHomePageService($homePageService)
    {
        $this->homePageService = $homePageService;
    }

    public function getForm()
    {
        return null;
    }

    /**
     * Send instance installation status to OrangeHRM
     */
    public function sendInstallationStatus() {
        try{
            $registrationProcessorFactory = new RegistrationEventProcessorFactory();
            $installStartRegistrationEventProcessor = $registrationProcessorFactory->getRegistrationEventProcessor(RegistrationEventQueue::INSTALLATION_START);
            $installStartRegistrationEventProcessor->saveRegistrationEvent(date("Y-m-d H:i:s"));
            $installSuccessRegistrationEventProcessor = $registrationProcessorFactory->getRegistrationEventProcessor(RegistrationEventQueue::INSTALLATION_SUCCESS);
            $installSuccessRegistrationEventProcessor->saveRegistrationEvent(date("Y-m-d H:i:s"));
            $installSuccessRegistrationEventProcessor->publishRegistrationEvents();
            if (isset($_SESSION['Installation'])) {
                if($_SESSION['Installation'] == "You have successfully upgraded OrangeHRM") {
                    $registrationProcessorFactory = new RegistrationEventProcessorFactory();
                    $upgradeRegistrationEventProcessor = $registrationProcessorFactory->getRegistrationEventProcessor(RegistrationEventQueue::UPGRADE_START);
                    $upgradeRegistrationEventProcessor->saveRegistrationEvent(date("Y-m-d H:i:s"));
                }
            }
        }catch (Exception $exception){
            Logger::getLogger('orangehrm')->error('Registration Dat Sync Failed');
        }
    }

}
