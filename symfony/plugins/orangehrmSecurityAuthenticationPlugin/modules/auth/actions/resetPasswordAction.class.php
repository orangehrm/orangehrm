<?php

class resetPasswordAction extends baseSecurityAuthenticationAction {
    
    /**
     * 
     * @return SecurityAuthenticationConfigService
     */
    protected $configService = null;
    protected $resetCode ='';
    public function getConfigService() {
        if (is_null($this->configService)) {
            $this->configService = new SecurityAuthenticationConfigService();
        }
        return $this->configService;
    }

    public function execute($request) {
        $this->showForm = true;
        $this->form = new ResetPasswordForm();
        $this->resetCode = $request->getParameter('resetCode');
        $this->getContext()->getConfiguration()->loadHelpers(array('I18N'));

        if (empty($this->resetCode)) {
            $this->getUser()->setFlash('warning', __('Reset code is not specified'));
            $this->showForm = false;

        } else {
            $this->passwordStrengthEnforced = $this->getConfigService()->isPasswordStengthEnforced();
            $this->requiredPasswordStrength = $this->getConfigService()->getRequiredPasswordStength();

            if ($request->isMethod(sfRequest::POST)) {
                $formData = $request->getParameter('securityAuthentication');
                $this->form->bind($formData);

                try {
                    $success = $this->getSecurityAuthenticationService()->saveNewPassword($formData, $this->resetCode);

                    if ($success) {
                        $this->redirect('auth/passwordReset');
                    } else {
                        $this->getUser()->setFlash('warning', __('Password resetting failed'));
                    }
                } catch (Exception $e) {
                    $this->getUser()->setFlash('warning', __($e->getMessage()));
                }
            }
        }
    }

}
