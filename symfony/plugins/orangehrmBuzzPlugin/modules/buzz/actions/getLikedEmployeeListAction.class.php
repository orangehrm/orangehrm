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

/**
 * Description of getLikedEmployeeList
 *
 * @author aruna
 */
class getLikedEmployeeListAction extends BaseBuzzAction {

    /**
     * @param sfForm $form
     * @return
     */
    protected function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    /**
     * return employee list
     * @param sfRequest $request
     */
    public function execute($request) {
        try {

            $this->setForm(new LikedOrSharedEmployeeForm());

            if ($request->isMethod('post')) {
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $formValues = $this->form->getValues();

                    $this->loggedInUser = $this->getLogedInEmployeeNumber();
                    $id = $formValues['id'];
                    $type = $formValues['type'];
                    $this->buzzService = new BuzzService();
                    $this->loggedInEmployeeId = $this->getLogedInEmployeeNumber();
                    if ($type == 'post') {
                        $this->share = $this->buzzService->getShareById($id);
                        $this->likedEmployeeList = $this->share->getLikedEmployees($this->loggedInEmployeeId);
                    } else {
                        $this->comment = $this->buzzService->getCommentById($id);
                        $this->likedEmployeeList = $this->comment->getLikedEmployees($this->loggedInEmployeeId);
                    }
                }
            }
        } catch (Exception $ex) {
            $this->redirect('auth/login');
        }
    }
}
