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
 * Description of addNewPostAction
 *
 * @author aruna
 */
class addNewPostAction extends BaseBuzzAction {

    /**
     * 
     * @param CommentForm $form
     */
    private function setCommentForm($form) {
        $this->commentForm = $form;
    }

    /**
     * 
     * @return CommentForm
     */
    private function getCommentForm() {
        if (!$this->commentForm) {
            $this->setCommentForm(new CommentForm());
        }
        return $this->commentForm;
    }

    /**
     * 
     * @return PostForm
     */
    private function getPostForm() {
        if (!$this->postForm) {
            $this->postForm = new CreatePostForm();
        }
        return $this->postForm;
    }

    /**
     * 
     * @return CommentForm
     */
    private function getEditForm() {
        if (!$this->editForm) {
            $this->editForm = new CommentForm();
        }
        return $this->editForm;
    }

    public function execute($request) {

        $this->form = $this->getPostForm();
        $this->loggedInUser = $this->getLogedInEmployeeNumber();
        
        if($this->loggedInUser){
            $loggedInEmployee = $this->getEmployeeService()->getEmployee($this->loggedInUser );
        }

        $this->isSuccessfullyAddedPost = false;
        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->postSaved = $this->form->save($this->loggedInUser, $loggedInEmployee);
                $this->isSuccessfullyAddedPost = true;
            } else {
                
            }
        }
        $this->commentForm = $this->getCommentForm();
        $this->editForm = $this->getEditForm();
    }

}
