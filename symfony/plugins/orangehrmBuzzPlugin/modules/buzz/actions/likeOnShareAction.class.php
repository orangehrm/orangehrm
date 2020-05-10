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
class likeOnShareAction extends BaseBuzzAction {

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
     * @return CommentForm
     */
    private function getEditForm() {
        if (!$this->editForm) {
            $this->editForm = new CommentForm();
        }
        return $this->editForm;
    }

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

        $this->loggedInUser = $this->getLogedInEmployeeNumber();
        if ($this->loggedInUser) {
            $loggedInEmployee = $this->getEmployeeService()->getEmployee($this->loggedInUser);
        }
        $this->shareId = $request->getParameter('shareId');
        $this->likeAction = $request->getParameter('likeAction');
        $csrfToken = $request->getParameter('CSRFToken');
        $validateForm = $this->getActionValidateForm();
        $this->error = 'no';

        if ($csrfToken == $validateForm->getCSRFToken()) {
            $this->commentForm = $this->getCommentForm();
            $this->editForm = $this->getEditForm();
            $this->share = $this->getBuzzService()->getShareById($this->shareId);

            if ($this->likeAction == 'unlike') {
                $this->unlikeOnShare($this->loggedInUser, $loggedInEmployee);
            } else {
                $this->likeOnShare($this->loggedInUser, $loggedInEmployee);
            }
        }
        throw new Exception($csrfToken . 'ddddd');
    }

    /**
     * save like on share to database
     * @return LikeOnshare
     */
    public function likeOnShare($loggedInEmployeeNumber, $employee) {
        $like = $this->setLike($loggedInEmployeeNumber, $employee);
        $unlike = $this->setUnLike($loggedInEmployeeNumber, $employee);

        $state = 'Liked';
        $delete = 'no';
        if ($this->share->isUnLikeUser($this->loggedInUser) == 'yes') {
            $this->getBuzzService()->deleteUnLikeForShare($unlike);
            $delete = 'yes';
        }

        if ($this->share->isLike($this->loggedInUser) == 'Like') {
            $this->getBuzzService()->saveLikeForShare($like);
            $this->likeLabel = 'Like';

            $state = 'savedLike';
        }
        $shareSaved = $this->getBuzzService()->getShareById($this->shareId);

        $arr = array('states' => $state, 'deleted' => $delete, 'likeCount' => $shareSaved->getNumberOfLikes(), 'unlikeCount' => $shareSaved->getNumberOfUnlikes());
        echo json_encode($arr);
        die();
    }

    public function unlikeOnShare($loggedInEmployeeNumber, $employee) {
        $like = $this->setLike($loggedInEmployeeNumber, $employee);
        $unlike = $this->setUnLike($loggedInEmployeeNumber, $employee);
        $delete = 'no';
        $state = 'Like';
        if ($this->share->isLike($this->loggedInUser) == 'Unlike') {

            $this->getBuzzService()->deleteLikeForShare($like);
            $delete = 'yes';
        }

        if ($this->share->isUnLike($this->loggedInUser) == 'no') {
            $this->getBuzzService()->saveUnLikeForShare($unlike);
            $this->likeLabel = 'Like';

            $state = 'savedUnLike';
        }

        $shareSaved = $this->getBuzzService()->getShareById($this->shareId);

        $arr = array('states' => $state, 'deleted' => $delete, 'likeCount' => $shareSaved->getNumberOfLikes(), 'unlikeCount' => $shareSaved->getNumberOfUnlikes());

        echo json_encode($arr);
        die();
    }

    /**
     * set like on share Data
     * @return \LikeOnShare
     */
    public function setLike($loggedInEmployeeNumber, $employee) {
        $like = New LikeOnShare();
        $like->setLikeTime(date("Y-m-d H:i:s"));
        $like->setEmployeeNumber($this->getLogedInEmployeeNumber());
        $like->setShareId($this->shareId);
        return $like;
    }

    public function setUnLike($loggedInEmployeeNumber, $employee) {
        $like = New UnLikeOnShare();
        $like->setLikeTime(date("Y-m-d H:i:s"));
        $like->setEmployeeNumber($this->getLogedInEmployeeNumber());
        $like->setShareId($this->shareId);
        return $like;
    }

    /**
     * 
     * @param CommentForm $form
     */
    private function setCommentForm($form) {
        $this->commentForm = $form;
    }
}
