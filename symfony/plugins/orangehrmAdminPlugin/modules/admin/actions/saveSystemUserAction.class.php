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


class saveSystemUserAction extends baseAdminAction {

    private $systemUserService;
    private $configService;
    private $securityAuthenticationConfigService;
    private $employeeService;

    public function getSystemUserService() {        
        if (is_null($this->systemUserService)) {
            $this->systemUserService = new SystemUserService();
        }
        return $this->systemUserService;
    }    
    
    public function setSystemUserService($systemUserService) {
        $this->systemUserService = $systemUserService;
    }

    /**
     * @return EmployeeService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }
    
    /**
     * 
     * @return SecurityAuthenticationConfigService
     */
    public function getConfigService() {
        if (is_null($this->configService)) {
            $this->configService = new ConfigService();
        }
        return $this->configService;
    }

    /**
     *
     * @return \SecurityAuthenticationConfigService
     */
    public function getSecurityAuthenticationConfigService() {
        if (is_null($this->securityAuthenticationConfigService)) {
            $this->securityAuthenticationConfigService = new SecurityAuthenticationConfigService();
        }
        return $this->securityAuthenticationConfigService;
    }

    /**
     *
     * @return sfForm 
     */
    public function getForm() {
        return $this->form;
    }

    public function execute($request) {

        $employeeCount = $this->getEmployeeService()->getEmployeeCount();
        $this->employeeCount = $employeeCount;

        if ($this->employeeCount == 0) {
            $this->getUser()->setFlash('warning.nofade', __('Add Employee in PIM to add user accounts'), false);
        }

        /* For highlighting corresponding menu item */
        $request->setParameter('initialActionName', 'viewSystemUsers');
        $this->openIdEnabled = $openIdConfig = $this->getConfigService()->getOpenIdProviderAdded();
        
        $this->userId = $request->getParameter('userId');
        $values = array('userId' => $this->userId, 'sessionUser' => $this->getUser()->getAttribute('user'));
        $this->setForm(new SystemUserForm(array(), $values));

        if ($request->getParameter('userId')) {
            $userRoleManager = $this->getContext()->getUserRoleManager();
            $accessible = $userRoleManager->isEntityAccessible('SystemUser', $request->getParameter('userId'));

            if (!$accessible) {
                $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
            }
        }

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                
                $userId = $this->form->getValue('userId');
                $userRoleModified = false;
                if (!empty($userId)) {
                    $user = $this->getSystemUserService()->getSystemUser($userId);
                    if ($user instanceof SystemUser) {
                        $newRoleId = $this->form->getValue('userType');
                        $userRoleModified = $newRoleId != $user->getUserRoleId();
                    }
                }
            
                $savedUser = $this->form->save();
                if ($savedUser instanceof SystemUser) { // sets flash values for admin/viewSystemUsers pre filter for further actions if needed
                    $this->getUser()->setFlash("new.user.id", $savedUser->getId()); //
                    $this->getUser()->setFlash("new.user.role.id", $savedUser->getUserRoleId());
                    $this->getUser()->setFlash("new.user.edited", $this->form->edited);
                    $this->getUser()->setFlash("new.user.role.modified", $userRoleModified);
                }                

                if ($this->form->edited) {
                    $this->getUser()->setFlash('success', __(TopLevelMessages::UPDATE_SUCCESS));
                } else {

                    $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                }
                $this->redirect('admin/viewSystemUsers');
            } else {
                $this->handleBadRequest();
                $this->getUser()->setFlash('warning', __(TopLevelMessages::VALIDATION_FAILED), false);
            }
        }
    }

}