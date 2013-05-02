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
class changeUserPasswordAction extends sfAction {

    public function execute($request) {

        $this->form = new ChangeUserPasswordForm();

        $this->userId = $this->getUser()->getAttribute('user')->getUserId();

        $systemUserService = new SystemUserService();

        $systemUser = $systemUserService->getSystemUser($this->userId);
        $this->username = $systemUser->getName();

        if ($this->getUser()->hasFlash('templateMessage')) {
            $this->templateMessage = $this->getUser()->getFlash('templateMessage');
        }

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {

                if (!$systemUserService->isCurrentPassword($this->userId, $this->form->getValue('currentPassword'))) {

                    $this->getUser()->setFlash('warning', __('Current Password Is Wrong'));
                    $this->redirect('admin/changeUserPassword');
                } else {
                    $this->form->save();
                    $this->getUser()->setFlash('success', __('Successfully Changed'));
                    $this->redirect('admin/changeUserPassword');
                }
            }
        }
    }

}