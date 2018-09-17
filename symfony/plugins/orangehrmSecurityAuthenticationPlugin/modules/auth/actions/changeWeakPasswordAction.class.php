<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

class changeWeakPasswordAction extends ohrmBaseAction
{
    protected $authenticationService;
    protected $systemUserService;
    /**
     *
     * @return SystemUserService
     */
    public function getSystemUserService() {
        if (!isset($this->systemUserService)) {
            $this->systemUserService = new SystemUserService();
        }
        return $this->systemUserService;
    }
    /**
     *
     * @return AuthenticationService
     */
    public function getAuthenticationService() {
        if (!isset($this->authenticationService)) {
            $this->authenticationService = new AuthenticationService();
        }
        return $this->authenticationService;
    }

    function execute($request)
    {
        $this->form = new ResetPasswordWithEnforceForm();
        $this->username = $_SESSION['username'];
        if(!$this->username){
            $this->redirect('auth/login');
        }

        if ($request->isMethod(sfRequest::POST)) {
            $formData = $request->getParameter('changeWeakPassword');
            $this->form->bind($formData);
            if ($this->form->isValid()) {
                $currentPassword = $formData['currentPassword'];
                $newPassword= $formData['newPassword'];
                $success = $this->getAuthenticationService()->setCredentials($this->username, $currentPassword, null);
                if($success){
                    $this->userId = $this->getUser()->getAttribute('user')->getUserId();
                    $this->getSystemUserService()->updatePassword($this->userId, $newPassword);
                    unset($_SESSION['username']);
                    $authService = new AuthenticationService();
                    $authService->clearCredentials();
                    $this->getUser()->setFlash('success', __('Password changed successfully'));
                    $this->redirect('auth/login');
                }else{
                    $this->getUser()->setFlash('warning', __("Invalid Credentials"));
                }

            }else{
                $this->getUser()->setFlash('warning', __(TopLevelMessages::VALIDATION_FAILED), false);
                $this->handleBadRequest();
            }
        }
    }
}