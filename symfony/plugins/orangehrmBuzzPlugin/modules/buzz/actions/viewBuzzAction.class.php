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
 * Description of viewBuzzAction
 *
 * @author aruna
 */
class viewBuzzAction extends BaseBuzzAction {

    /**
     * function to set post form
     * @param PostForm $form
     */
    private function setPostForm($form) {
        $this->postForm = $form;
    }

    /**
     * get Post form
     * @return PostForm
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
     * function to get upload photo form
     * @return AddTaskForm
     */
    private function getUploadImageForm() {
        if (!$this->uploadImageForm) {
            $this->uploadImageForm = new UploadPhotoForm();
        }
        return $this->uploadImageForm;
    }

    /**
     * function to get upload photo form
     * @return AddTaskForm
     */
    private function getVideoForm() {
        if (!$this->videoForm) {
            $this->videoForm = new CreateVideoForm();
        }
        return $this->videoForm;
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
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->getResponse()->setStatusCode(HttpResponseCode::HTTP_BAD_REQUEST);
            return sfView::NONE;
        }

        $buzzConfigService = $this->getBuzzConfigService();
        $this->loggedInUser = $this->getLogedInEmployeeNumber();
        if (is_null($this->loggedInUser)) {
            $this->getUser()->setFlash('error.nofade', __("Buzz is accessible for the user who is assigned with an employee."), false);
            $this->getController()->forward('core', 'displayMessage');
            // OHRM-663: Should stop request further execution
            throw new sfStopException();
        }
        try {
            $this->setConfigurationValues();
            $this->postForm = $this->getPostForm();
            $this->commentForm = $this->getCommentForm();
            $this->editForm = $this->getEditForm();
            $this->uploadImageForm = $this->getUploadImageForm(); //image upload form
            $this->actionValidateForm = $this->getActionValidateForm();
            $this->buzzService = $this->getBuzzService();
            $this->allShareCount = $this->buzzService->getSharesCount();
            $this->initializePostList();
            $this->videoForm = $this->getVideoForm();  // video form added
            $this->timestamp = time();
            $this->imageMaxDimension = $buzzConfigService->getMaxImageDimension();
            
            $this->refreshStatsForm = $this->getRefreshStatsForm();
            $this->likedOrSharedEmployeeForm =  $this->getLikedOrSharedEmployeeForm();
            $this->loadMorePostsForm = $this->getLoadMorePostsForm();
            $this->deleteOrEditShareForm =  $this->getDeleteOrEditShareForm();
            $this->deleteOrEditCommentForm =  $this->getDeleteOrEditCommentForm();
            $this->imageUploadForm = $this->getImageUploadForm();
        
        } catch (Exception $ex) {
            $this->forward('auth', 'login');
        }

        if (!is_null($this->loggedInUser)) {
            $buzzNotificationMetadata = $this->getBuzzNotificationService()->getBuzzNotificationMetadata($this->loggedInUser);
            if (!$buzzNotificationMetadata instanceof BuzzNotificationMetadata) {
                $buzzNotificationMetadata = new BuzzNotificationMetadata();
                $buzzNotificationMetadata->setEmpNumber($this->loggedInUser);
            }
            $buzzNotificationMetadata->setLastBuzzViewTime(date("Y-m-d H:i:s"));
            $this->getBuzzNotificationService()->saveBuzzNotificationMetadata($buzzNotificationMetadata);
        }
    }
    
    protected function getRefreshStatsForm() {
        return new RefreshStatsForm();
    }

    protected function getLikedOrSharedEmployeeForm() {
        return new LikedOrSharedEmployeeForm();
    }

    protected function getLoadMorePostsForm() {
        return new LoadMorePostsForm();
    }

    protected function getDeleteOrEditShareForm() {
        return new DeleteOrEditShareForm();
    }

    protected function getDeleteOrEditCommentForm() {
        return new DeleteOrEditCommentForm();
    }
    
    protected function getImageUploadForm(){
        return new ImageUploadForm();
    }

    /**
     * Retrieving the list of posts from database.
     */
    protected function initializePostList() {
        $this->postList = $this->getBuzzService()->getShares($this->shareCount);
    }

    protected function setConfigurationValues() {

        if (!($this->getUser()->getAttribute("buzz_user_logged_in"))) {
            $buzzConfigService = $this->getBuzzConfigService();
            $this->getUser()->setAttribute("buzz_user_logged_in", TRUE);
            $this->getUser()->setAttribute("share_count", $buzzConfigService->getBuzzShareCount());
            $this->getUser()->setAttribute("comment_count", $buzzConfigService->getBuzzInitialCommentCount());
            $this->getUser()->setAttribute("view_more_comment", $buzzConfigService->getBuzzViewCommentCount());
            $this->getUser()->setAttribute("like_count", $buzzConfigService->getBuzzLikeCount());
            $this->getUser()->setAttribute("refresh_time", $buzzConfigService->getRefreshTime());
            $this->getUser()->setAttribute("initial_comment_count", $buzzConfigService->getBuzzInitialCommentCount());
            $this->getUser()->setAttribute("post_length", $buzzConfigService->getBuzzPostTextLenth());
            $this->getUser()->setAttribute("post_lines", $buzzConfigService->getBuzzPostTextLines());
        }
        $this->shareCount = $this->getUser()->getAttribute("share_count");
        $this->commentCount = $this->getUser()->getAttribute("comment_count");
        $this->viewMoreComment = $this->getUser()->getAttribute("view_more_comment");
        $this->likeCount = $this->getUser()->getAttribute("like_count");
        $this->refeshTime = $this->getUser()->getAttribute("refresh_time");
        $this->initialcommentCount = $this->getUser()->getAttribute("initial_comment_count");
        $this->postLenth = $this->getUser()->getAttribute("post_length");
        $this->postLines = $this->getUser()->getAttribute("post_lines");
    }

}
