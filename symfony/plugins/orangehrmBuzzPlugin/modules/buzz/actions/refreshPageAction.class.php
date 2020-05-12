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
 * Description of refreshPageAction
 *
 * @author aruna
 */
class refreshPageAction extends BaseBuzzAction {

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
     * this is set post form
     * @param set Post form $form
     */
    private function setPostForm($form) {
        $this->postForm = $form;
    }

    /**
     * function to get post form
     * @return PostForm
     */
    private function getPostForm() {
        if (!($this->postForm instanceof CreatePostForm)) {
            $this->setPostForm(new CreatePostForm());
        }
        return $this->postForm;
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
            $this->lastPostId = $request->getParameter('lastPostId');
            $this->buzzService = $this->getBuzzService();
            
            $this->fullSharesList = $this->buzzService->getSharesUptoId($this->lastPostId);
            $this->commentForm = $this->getCommentForm();
            $this->editForm = $this->getEditForm();
        } catch (Exception $ex) {
            $this->redirect('auth/login');
        }
    }

}
