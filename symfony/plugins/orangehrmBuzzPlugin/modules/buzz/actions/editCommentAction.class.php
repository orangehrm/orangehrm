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
class editCommentAction extends BaseBuzzAction {

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
     * main function  
     * @param sfRequest $request
     */
    public function execute($request) {
        try {

            $this->setForm(new DeleteOrEditCommentForm());

            if ($request->isMethod('post')) {
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $formValues = $this->form->getValues();

                    $this->loggedInUser = $this->getLogedInEmployeeNumber();
                    $this->commentId = $formValues['commentId'];
                    $this->editedContend = $formValues['textComment'];
                    $this->error = 'no';

                    $comment = $this->getBuzzService()->getCommentById($this->commentId);
                    If ($comment != null) {
                        $this->comment = $this->editComment($comment);
                    } else {
                        $this->error = 'yes';
                        $this->getUser()->setFlash('error', __("This comment has been deleted or you do not have permission to perform this action"));
                    }
                }
            }
        } catch (Exception $ex) {
            $this->redirect('auth/login');
        }
    }

    /**
     * edit the comment conntent
     * @return Comment
     */
    private function editComment($comment) {

        if ($comment->getEmployeeNumber() == $this->getLogedInEmployeeNumber()) {
            $comment = $this->saveComment($comment);
        }
        return $comment;
    }

    /**
     * save the edited comment to database
     * @param comment $comment
     * @return Comment
     */
    private function saveComment($comment) {
        $comment->setCommentText($this->editedContend);
        return $this->getBuzzService()->saveCommentShare($comment);
    }

}
