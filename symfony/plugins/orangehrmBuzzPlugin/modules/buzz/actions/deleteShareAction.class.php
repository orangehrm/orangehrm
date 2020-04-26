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
 * Description of likeOnShareAction
 *
 * @author aruna
 */
class deleteShareAction extends BaseBuzzAction {

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
     * get share by Id and return it
     * @param type $shareId
     * @return Share
     */
    public function getShare($shareId) {
        return $this->getBuzzService()->getShareById($shareId);
    }

    /**
     * delete Share if it bis post then delete post
     */
    public function deleteShare($share) {
        try {
            if ($share->getEmployeeNumber() == $this->getLogedInEmployeeNumber() || $this->getLoggedInEmployeeUserRole() == 'Admin') {
                $this->getBuzzService()->deleteShare($share->getId());
            }
        } catch (Exception $ex) {
            
        }
    }

    /**
     * delete post by Id 
     * @param type $share
     */
    private function deletePost($share) {
        if (($share->getPostShared()->getEmployeeNumber() == $this->getLogedInEmployeeNumber()) || $this->getLoggedInEmployeeUserRole() == 'Admin') {
            $this->getBuzzService()->deletePost($share->getPostId());
        }
    }

    public function execute($request) {
        try {
            $this->setForm(new DeleteOrEditShareForm());

            if ($request->isMethod('post')) {
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $formValues = $this->form->getValues();

                    $this->loggedInUser = $this->getLogedInEmployeeNumber();
                    $this->shareId = $formValues['shareId'];
                    
                    $share = $this->getShare($this->shareId);
                    if ($share->getType() == 0) {
                        $this->deletePost($share);
                    } else {
                        $this->deleteShare($share);
                    }
                }
            }
        } catch (Exception $ex) {
            $this->redirect('auth/login');
        }

        return sfView::NONE;
    }

}
