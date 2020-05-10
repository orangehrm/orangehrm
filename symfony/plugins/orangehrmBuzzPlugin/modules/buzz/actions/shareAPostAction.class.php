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
class shareAPostAction extends BaseBuzzAction {

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
     * this is reurn form to edit comment
     * @return CommentEditForm
     */
    private function getEditForm() {
        if (!($this->editForm instanceof CommentForm)) {
            $this->editForm = new CommentForm();
        }
        return $this->editForm;
    }

    /**
     * function to set comment form
     * @param CommentForm
     */
    private function setCommentForm($form) {
        $this->commentForm = $form;
    }

    /**
     * this is to get comment form
     * @return CommentForm
     */
    private function getCommentForm() {
        if (!($this->commentForm instanceof CommentForm)) {
            $this->setCommentForm(new CommentForm());
        }
        return $this->commentForm;
    }

    public function execute($request) {
        try {
            $this->loggedInUser = $this->getLogedInEmployeeNumber();
        } catch (Exception $ex) {
            $this->redirect('auth/login');
        }

        try {

            $this->setForm(new DeleteOrEditShareForm());

            if ($request->isMethod('post')) {
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $formValues = $this->form->getValues();

                    if ($this->loggedInUser) {
                        $loggedInEmployee = $this->getEmployeeService()->getEmployee($this->loggedInUser);
                    }
                    $this->error = 'no';
                    $this->postId = $formValues['shareId'];
                    $this->shareText = $formValues['textShare'];
                    
                    $this->post = $this->getBuzzService()->getPostById($this->postId);
                    $this->share = $this->sharePost($loggedInEmployee);
                    $this->logeInUser = $this->getLogedInEmployeeNumber();
                    $this->commentForm = $this->getCommentForm();
                    $this->editForm = $this->getEditForm();
                }
            }
        } catch (Exception $ex) {
            $this->error = 'yes';
            $this->getUser()->setFlash('error', __("This post has been deleted or you do not have permission to perform this action"));
        }
    }

    /**
     * save share in database
     * @return Share
     */
    public function sharePost($loggedInEmployee) {
        $share = $this->setShare($this->postId, $loggedInEmployee);
        return $this->getBuzzService()->saveShare($share);
    }

    /**
     * set Share details
     * @param int $postId
     * @return \Share
     */
    public function setShare($postId, $employee) {
        $share = new Share();
        $share->setPostId($postId);
        $share->setEmployeeNumber($this->getLogedInEmployeeNumber());
        $share->setNumberOfComments(0);
        $share->setNumberOfLikes(0);
        $share->setNumberOfUnlikes(0);
        $share->setText($this->shareText);
        $share->setShareTime(date("Y-m-d H:i:s"));
        $share->setUpdatedAt(date("Y-m-d H:i:s"));
        $share->setType('1');
        return $share;
    }

}
