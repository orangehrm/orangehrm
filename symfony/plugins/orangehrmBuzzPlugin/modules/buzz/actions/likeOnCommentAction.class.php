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
class likeOnCommentAction extends BaseBuzzAction {

    /**
     * return action validate form for validate actions
     * @return ActionValidateForm
     */
    private function getActionValidateForm() {
        if (!$this->actionValidateForm instanceof ActionValidatingForm) {
            $this->actionValidateForm = new ActionValidatingForm();
        }
        return $this->actionValidateForm;
    }

    public function execute($request) {
        try {
            $this->loggedInUser = $this->getLogedInEmployeeNumber();
            if ($this->loggedInUser) {
                $loggedInEmployee = $this->getEmployeeService()->getEmployee($this->loggedInUser);
            }
            $this->commentId = $request->getParameter('commentId');
            $this->likeAction = $request->getParameter('likeAction');
            $this->comment = $this->getBuzzService()->getCommentById($this->commentId);
            $csrfToken = $request->getParameter('CSRFToken');
            $validateForm = $this->getActionValidateForm();

            if ($csrfToken == $validateForm->getCSRFToken()) {
                if ($this->likeAction == 'unlike') {
                    $this->unlikeOnComment($this->loggedInUser, $loggedInEmployee);
                } else {
                    $this->likeOnComment($this->loggedInUser, $loggedInEmployee);
                }
            }
        } catch (Exception $ex) {
            $this->redirect('auth/login');
        }
    }

    /**
     * save like on comment
     * @return LikeOnComment
     */
    public function likeOnComment($loggedInEmployeeNumber, $employee) {
        $like = $this->setLike($loggedInEmployeeNumber, $employee);
        $unlike = $this->setUnLike($loggedInEmployeeNumber, $employee);
        $delete = 'no';
        $state = 'Like';
        if ($this->comment->isUnLike($this->loggedInUser) == 'yes') {
            $this->getBuzzService()->deleteUnLikeForComment($unlike);
            $delete = 'yes';
        }
        if ($this->comment->isLike($this->loggedInUser) == 'Like') {
            $this->getBuzzService()->saveLikeForComment($like);
            $state = 'savedLike';
        }
        $commentSaved = $this->getBuzzService()->getCommentById($this->commentId);

        $arr = array('states' => $state, 'deleted' => $delete, 'likeCount' => $commentSaved->getNumberOfLikes(), 'unlikeCount' => $commentSaved->getNumberOfUnlikes());

        echo json_encode($arr);
        die();
    }

    private function unlikeOnComment($loggedInEmployeeNumber, $employee) {
        $like = $this->setLike($loggedInEmployeeNumber, $employee);
        $unlike = $this->setUnLike($loggedInEmployeeNumber, $employee);
        $delete = 'no';
        $state = 'Like';
        if ($this->comment->isLike($this->loggedInUser) == 'Unlike') {
            $this->getBuzzService()->deleteLikeForComment($like);
            $delete = 'yes';
        }
        if ($this->comment->isUnLike($this->loggedInUser) == 'no') {
            $this->getBuzzService()->saveUnLikeForComment($unlike);
            $this->likeLabel = 'Like';
            $state = 'savedUnLike';
        }

        $commentSaved = $this->getBuzzService()->getCommentById($this->commentId);

        $arr = array('states' => $state, 'deleted' => $delete, 'likeCount' => $commentSaved->getNumberOfLikes(), 'unlikeCount' => $commentSaved->getNumberOfUnlikes());

        echo json_encode($arr);
        die();
    }

    /**
     * set like on comment data
     * @return \LikeOnComment
     */
    public function setLike($loggedInEmployeeNumber, $employee) {
        $like = New LikeOnComment();
        $like->setLikeTime(date("Y-m-d H:i:s"));
        $like->setEmployeeNumber($loggedInEmployeeNumber);
        $like->setCommentId($this->commentId);
        return $like;
    }

    public function setUnLike($loggedInEmployeeNumber, $employee) {
        $like = New UnLikeOnComment();
        $like->setLikeTime(date("Y-m-d H:i:s"));
        $like->setEmployeeNumber($loggedInEmployeeNumber);
        $like->setCommentId($this->commentId);
        return $like;
    }
}
