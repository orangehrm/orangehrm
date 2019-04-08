<?php

class confirmPasswordResetAction extends sfAction {

	public function execute($request) {
		$this->getContext()->getConfiguration()->loadHelpers(array('I18N'));
        $username =  $_SESSION["name"];
        $systemUserService = new SystemUserService();
        $systemUser=$systemUserService->searchSystemUsers(array('userName' => $username))->get(0);

        if ($request->isMethod(sfRequest::POST)) {

            if ($systemUser instanceof SystemUser) {
                $user = $systemUser->getEmployee();
                $this->getContext()->getConfiguration()->loadHelpers('Url');
                $securityAuthService = new SecurityAuthenticationService();

                try {
                    $securityAuthService->logPasswordResetRequest($systemUser);
                    session_destroy();
                    $this->redirect('auth/sendPasswordReset');
                } catch (ServiceException $e) {
                    $this->getUser()->setFlash('warning', __($e->getMessage()));

                }
            }
        }

	}

}

