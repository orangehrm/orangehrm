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
 * Description of viewPostPreviewComponent
 *
 * @author aruna
 */
class viewPostPreviewComponent extends sfComponent {

    protected $buzzService;
    protected $buzzConfigService;
    protected $buzzUserService;

    /**
     * 
     * @return BuzzUserService
     */
    protected function getBuzzUserService() {
        if (!$this->buzzUserService instanceof BuzzUserService) {
            $this->buzzUserService = new BuzzUserService();
        }
        return $this->buzzUserService;
    }

    /**
     * 
     * @param AddTaskForm $form
     */
    private function setCommentForm($form) {
        $this->commentForm = $form;
    }

    /**
     * function to get comment form
     * @return Comment form
     */
    private function getEditForm() {
        if (!$this->editForm) {
            $this->editForm = new CommentForm();
        }
        return $this->editForm;
    }

    /**
     * get comment form 
     * @return CommentForm
     */
    private function getCommentForm() {
        if (!$this->commentForm) {
            $this->setCommentForm(new CommentForm());
        }
        return $this->commentForm;
    }

    public function execute($request) {

        $this->setShare($this->post);
        $this->commentForm = $this->getCommentForm();
        $this->editForm = $this->getEditForm();
        $this->setBuzzService(new BuzzService());

        $this->loggedInUser = $this->getLogedInEmployeeNumber();
    }

    /**
     * set post valuves to show
     * @param type $post
     */
    private function setShare($post) {
        $this->postId = $post->getId();
        $this->postDate = $post->getDate();
        $this->postTime = $post->getTime();
        $this->postContent = $post->getText();
        $this->postNoOfLikes = $post->getLike()->count();
        $this->postType = $post->getType();
        $this->employeeID = $post->getEmployeeNumber();
        $this->commentList = $post->getComment();
        $this->postEmployeeName = $post->getEmployeeFirstLastName();
        if ($this->postEmployeeName == ' ') {
            $this->postEmployeeName = '(' . __(BaseBuzzAction::LABEL_EMPLOYEE_DELETED) . ')';
            $this->postSharerDeleted = true;
        }
        $this->isLike = $post->isLike($this->loggedInUser);
        $this->originalPost = $post->getPostShared();
        $this->originalPostId = $this->originalPost->getId();
        $this->originalPostEmpNumber = $this->originalPost->getEmployeeNumber();
        $this->originalPostSharerName = $this->originalPost->getEmployeeFirstLastName();
        if ($this->originalPostSharerName == ' ') {
            $this->originalPostSharerName = '(' . __(BaseBuzzAction::LABEL_EMPLOYEE_DELETED) . ')';
            $this->originalPostSharerDeleted = true;
        }
        $this->originalPostDate = $this->originalPost->getDate();
        $this->originalPostTime = $this->originalPost->getTime();
        $this->originalPostContent = $this->originalPost->getText();
        $this->likeEmployeList = $post->getLikedEmployeeList();
    }

    /**
     * 
     * @param type $buzzService
     */
    protected function setBuzzService($buzzService) {
        $this->buzzService = $buzzService;
    }

    /**
     * 
     * @return BuzzService
     */
    private function getBuzzService() {
        if (!$this->buzzService) {
            $this->setBuzzService(new BuzzService());
        }
        return $this->buzzService;
    }

    /**
     * 
     * @return BuzzConfigService
     */
    private function getBuzzConfigService() {
        if (!$this->buzzConfigService) {
            $this->buzzConfigService = new BuzzConfigService();
        }
        return $this->buzzConfigService;
    }

    /**
     * get loged in employee number from cookie service 
     * @return Int
     */
    public function getLogedInEmployeeNumber() {
        return $this->getBuzzUserService()->getEmployeeNumber();
    }

}
