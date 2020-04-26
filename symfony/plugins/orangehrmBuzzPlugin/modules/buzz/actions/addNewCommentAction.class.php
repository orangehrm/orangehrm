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
 * Description of addNewCommentAction
 *
 * @author aruna
 */
class addNewCommentAction extends BaseBuzzAction {

    /**
     * function to get edit form
     * @return CommentForm
     */
    private function getEditForm() {
        if (!($this->editForm instanceof CommentForm)) {
            $this->editForm = new CommentForm();
        }
        return $this->editForm;
    }

    /**
     * function to get edit form
     * @return CommentForm
     */
    private function getCommentForm() {
        if (!($this->commentForm instanceof CommentForm)) {
            $this->commentForm = new CommentForm();
        }
        return $this->commentForm;
    }

    public function execute($request) {
        try {
            $this->loggedInUser = $this->getLogedInEmployeeNumber();
            if ($this->loggedInUser) {
                $loggedInEmployee = $this->getEmployeeService()->getEmployee($this->loggedInUser);
            }
            $this->loggedInEmployeeUserRole = $this->getLoggedInEmployeeUserRole();
            $this->commentForm = $this->getCommentForm();
            $this->editForm = $this->getEditForm();
            $this->isSuccessfullyAddedComment = false;
            if ($request->isMethod('post')) {
                $this->commentForm->bind($request->getParameter($this->commentForm->getName()));
                if ($this->commentForm->isValid()) {
                    if ($this->getBuzzService()->getShareById($this->commentForm->getValue('shareId')) != null) {
                        $commentSaved = $this->commentForm->saveComment($this->loggedInUser, $loggedInEmployee);
                        $this->setCommentVariablesForView($commentSaved);
                        $this->isSuccessfullyAddedComment = true;
                    } else {
                        $this->getUser()->setFlash('error', __("This share has been deleted or you do not have permission to perform this action"));
                    }
                } else {
                    $this->getUser()->setFlash('error', __("This share has been deleted or you do not have permission to perform this action"));
                }
            }
        } catch (Exception $ex) {
            $this->error = 'redirect';
        }
    }

    /**
     * save comment to the database
     * @return Comment
     */
    public function setCommentVariablesForView($comment) {
        $this->comment = $comment;
        $this->commentPostId = $this->comment->getShareId();
        $this->commentEmployeeName = $this->comment->getEmployeeFirstLastName();
        $this->commentEmployeeJobTitle = $this->comment->getEmployeeComment()->getJobTitleName();
        $this->commentContent = $this->comment->getCommentText();
        $this->commentDate = $this->comment->getDate();
        $this->commentTime = $this->comment->getTime();
        $this->commentId = $this->comment->getId();
        $this->commentNoOfLikes = $this->comment->getNumberOfLikes();
        $this->isLikeComment = $this->comment->isLike($this->getLogedInEmployeeNumber());
        $this->commentEmployeeId = $this->getLogedInEmployeeNumber();
        $this->commentNoOfLikes = $this->comment->getNumberOfLikes();
        $this->commentNoOfUnLikes = $this->comment->getNumberOfUnlikes();
        if ($this->comment->isUnLike($this->getLogedInEmployeeNumber())) {
            $this->isUnlike = 'yes';
        }
    }

}
