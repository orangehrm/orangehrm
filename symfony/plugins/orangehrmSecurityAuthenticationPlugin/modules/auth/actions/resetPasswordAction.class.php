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
class resetPasswordAction extends basePasswordResetAction {
    
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
        $this->getContext()->getConfiguration()->loadHelpers(array('OrangeI18N'));

        if (empty($this->resetCode)) {
            $this->getUser()->setFlash('warning', __('Reset code is not specified,'));
            $this->showForm = false;

        } else {
            if ($request->isMethod(sfRequest::POST)) {
                $formData = $request->getParameter('securityAuthentication');
                $this->form->bind($formData);

                if ($this->form->isValid()) {
                    try {
                        $success = $this->getPasswordResetService()->saveNewPassword($formData, $this->resetCode);

                        if ($success) {
                            $this->redirect('auth/passwordReset');
                        } else {
                            $this->getUser()->setFlash('warning', __('Password resetting failed'));
                        }
                    } catch (Exception $e) {
                        $this->getUser()->setFlash('warning', __($e->getMessage()));
                    }

                } else {
                    $this->handleBadRequest();
                    $this->getUser()->setFlash('warning', __(TopLevelMessages::VALIDATION_FAILED), false);
                }

            }
        }
    }

}
