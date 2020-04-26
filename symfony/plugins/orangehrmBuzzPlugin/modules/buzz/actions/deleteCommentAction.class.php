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
class deleteCommentAction extends BaseBuzzAction {

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
     * get Comment By It Id
     * @param type $commentId
     * @return type
     */
    private function getComment($commentId) {
        return $this->getBuzzService()->getCommentById($commentId);
    }

    public function execute($request) {
        try {

            $this->setForm(new DeleteOrEditCommentForm());

            if ($request->isMethod('post')) {
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $formValues = $this->form->getValues();

                    $this->loggedInUser = $this->getLogedInEmployeeNumber();
                    $this->commentId = $formValues['commentId'];
                    $comment = $this->getComment($this->commentId);
                    $commentedEmployeeNumber = $comment->getEmployeeNumber();
                    if ($commentedEmployeeNumber == $this->loggedInUser || $this->getLoggedInEmployeeUserRole() == 'Admin') {
                        $this->deleteComment($comment);
                    }
                }
            }
        } catch (Exception $ex) {
            $this->redirect('auth/login');
        }

        return sfView::NONE;
    }

    /**
     * delete comment 
     */
    private function deleteComment($comment) {
        $this->getBuzzService()->deleteCommentForShare($comment);
    }

}
