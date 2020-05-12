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
 * Description of viewPostComponent
 *
 * @author dewmal
 */
class viewPostComponent extends sfComponent {

    protected $buzzService;
    protected $buzzConfigService;
    protected $buzzUserService;
    protected $ohrmCookieManager;

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
     * @return CookieManager
     */
    protected function getOhrmCookieManager() {
        if (!$this->ohrmCookieManager instanceof CookieManager) {
            $this->ohrmCookieManager = new CookieManager();
        }
        return $this->ohrmCookieManager;
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
     * 
     * @param AddTaskForm $form
     */
    private function setPostForm($form) {
        $this->postForm = $form;
    }

    /**
     * 
     * @return AddTaskForm
     */
    private function getPostForm() {
        if (!$this->postForm) {
            $this->setPostForm(new CreatePostForm());
        }
        return $this->postForm;
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

        $this->setBuzzService(new BuzzService());
        $this->setShare($this->post);
        //$this->postForm = $this->getPostForm();
        $this->commentForm = $this->getCommentForm();
        $this->editForm = $this->getEditForm();
        $this->intializeConfigValuves();
    }

    /**
     * set the share parameters to view
     * @param type $post
     */
    private function setShare($post) {
        $this->postId = $post->getId();
        $this->postEmployee = $post->getEmployeePostShared();
        $this->postEmployeeJobTitle = $this->postEmployee->getJobTitleName();
        $this->postDate = $post->getDate();
        $this->postTime = $post->getTime();
        $this->postContent = $post->getText();
        $this->postNoOfLikes = $post->getNumberOfLikes();
        $this->postUnlike = $post->getNumberOfUnlikes();
        $this->postType = $post->getType();
        $this->employeeID = $post->getEmployeeNumber();
        $this->commentList = $post->getComment();
        $this->postEmployeeName = $post->getEmployeeFirstLastName($this->postEmployee);
        if ($this->postEmployeeName == ' ') {
            $this->postEmployeeName = '(' . __(BaseBuzzAction::LABEL_EMPLOYEE_DELETED) . ')';
            $this->postSharerDeleted = true;
        }
        $this->isLike = $post->isLike($this->loggedInUser);
        $this->isUnlike = $post->isUnLike($this->loggedInUser);
        $this->originalPost = $post->getPostShared();
        $this->postPhotos = $this->getBuzzService()->getPostPhotos($this->originalPost->getId());
        $this->postShareCount = $post->calShareCount($this->originalPost);
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
        $this->loggedInEmployeeUserRole = $this->getLoggedInEmployeeUserRole();
    }

    /**
     * initialize config valuves
     */
    protected function intializeConfigValuves() {
        $buzzConfigService = $this->getBuzzConfigService();
        $this->initialcommentCount = $buzzConfigService->getBuzzInitialCommentCount();
        $this->viewMoreComment = $buzzConfigService->getBuzzViewCommentCount();
        $this->likeCount = $buzzConfigService->getBuzzLikeCount();
        $this->postLenth = $buzzConfigService->getBuzzPostTextLenth();
        $this->postLines = $buzzConfigService->getBuzzPostTextLines();
        $this->commentLength = $buzzConfigService->getBuzzCommentTextLenth();
    }

    /**
     * get loged in employee user role
     * @return type
     */
    public function getLoggedInEmployeeUserRole() {
        $employeeUserRole = $this->getBuzzUserService()->getEmployeeUserRole();
        return $employeeUserRole;
    }

}
