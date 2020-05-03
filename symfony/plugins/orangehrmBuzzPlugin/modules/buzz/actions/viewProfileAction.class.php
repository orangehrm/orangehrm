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
 * Description of viewProfileAction
 *
 * @author dewmal
 */
class viewProfileAction extends BaseBuzzAction {

    const TERMINATED = "TERMINATED";

    /**
     * get employee search form
     * @return searchForm
     */
    private function getSearchForm() {
        if (!($this->searchForm instanceof BuzzEmployeeSearchForm)) {
            $this->searchForm = new BuzzEmployeeSearchForm();
        }
        return $this->searchForm;
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
        $this->searchForm = $this->getSearchForm();
        $this->actionValidateForm = $this->getActionValidateForm();

        try {
            $this->loggedInUser = $this->getLogedInEmployeeNumber();
            $this->profileUserId = $request->getParameter('empNumber');
            $this->employee = $this->getEmployeeService()->getEmployee($this->profileUserId);
            if ($this->employee) {
                if ($this->employee->getState() == viewProfileAction::TERMINATED) {
                    $this->redirect('buzz/viewBuzz');
                }
            }
            $this->intializeConfigValuves();
            $this->initializePostList();

            $this->refreshStatsForm = $this->getRefreshStatsForm();
            $this->likedOrSharedEmployeeForm = $this->getLikedOrSharedEmployeeForm();
            $this->loadMorePostsForm = $this->getLoadMorePostsForm();
            $this->deleteOrEditShareForm = $this->getDeleteOrEditShareForm();
            $this->deleteOrEditCommentForm = $this->getDeleteOrEditCommentForm();
            $this->imageUploadForm = $this->getImageUploadForm();
            if (! $request->isMethod(sfWebRequest::GET)) {
                $this->getResponse()->setStatusCode(HttpResponseCode::HTTP_BAD_REQUEST);
            }
        } catch (Exception $ex) {
            $this->redirect('auth/login');
        }

        $buzzNotificationMetadata = $this->getBuzzNotificationService()->getBuzzNotificationMetadata($this->loggedInUser);
        if (!$buzzNotificationMetadata instanceof BuzzNotificationMetadata) {
            $buzzNotificationMetadata = new BuzzNotificationMetadata();
            $buzzNotificationMetadata->setEmpNumber($this->loggedInUser);
        }
        $buzzNotificationMetadata->setLastBuzzViewTime(date("Y-m-d H:i:s"));
        $this->getBuzzNotificationService()->saveBuzzNotificationMetadata($buzzNotificationMetadata);
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
        $buzzService = $this->getBuzzService();
        $userId = $this->profileUserId;

        $this->postList = $buzzService->getSharesByEmployeeNumber($this->shareCount, $userId);
        $this->allShareCount = $buzzService->getNoOfSharesByEmployeeNumber($userId);
    }

    /**
     * initialize config valuves from database
     */
    protected function intializeConfigValuves() {
        $buzzConfigService = $this->getBuzzConfigService();
        $this->shareCount = $buzzConfigService->getBuzzShareCount();
        $this->commentCount = $buzzConfigService->getBuzzInitialCommentCount();
        $this->viewMoreComment = $buzzConfigService->getBuzzViewCommentCount();
        $this->likeCount = $buzzConfigService->getBuzzLikeCount();
        $this->refeshTime = $buzzConfigService->getRefreshTime();
    }

}
