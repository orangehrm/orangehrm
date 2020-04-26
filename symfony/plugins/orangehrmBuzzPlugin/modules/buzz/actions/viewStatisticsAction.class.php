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
 * Description of viewStatisticsAction
 *
 * @author aruna
 */
class viewStatisticsAction extends BaseBuzzAction {

    protected $buzzService;

    /**
     * return number of shares that user share
     * @param int $userId
     * @return Int
     */
    private function getNoOfSharesBy($userId) {
        return $this->buzzService->getNoOfSharesByEmployeeNumber($userId);
    }

    /**
     * return number of comments that user commented
     * @param int $userId
     * @return Int
     */
    private function getNoOfCommentsBy($userId) {
        return $this->buzzService->getNoOfCommentsByEmployeeNumber($userId);
    }

    /**
     * return number of likes that user like on shares
     * @param int $userId
     * @return Int
     */
    private function getNoOfShareLikesFor($userId) {
        return $this->buzzService->getNoOfShareLikesForEmployeeByEmployeeNumber($userId);
    }

    /**
     * return number of likes that user like on comments
     * @param int $userId
     * @return Int
     */
    private function getNoOfCommentLikesFor($userId) {
        return $this->buzzService->getNoOfCommentLikesForEmployeeByEmployeeNumber($userId);
    }

    /**
     * return number of shares that user share
     * @param int $userId
     * @return Int
     */
    private function getNoOfCommentsFor($userId) {
        return $this->buzzService->getNoOfCommentsForEmployeeByEmployeeNumber($userId);
    }

    /**
     * @param sfForm $form
     * @return
     */
    protected function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {
        $this->loggedInUser = $this->getLogedInEmployeeNumber();

        $this->setForm(new RefreshStatsForm());

        if ($request->isMethod('post')) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->buzzService = $this->getBuzzService();
                $formValues = $this->form->getValues();
                $this->profileUserId = $formValues['profileUserId'];
                $this->noOfShares = $this->getNoOfSharesBy($this->profileUserId);
                $this->noOfComments = $this->getNoOfCommentsBy($this->profileUserId);
                $this->noOfShareLikesRecieved = $this->getNoOfShareLikesFor($this->profileUserId);
                $this->noOfCommentLikesRecieved = $this->getNoOfCommentLikesFor($this->profileUserId);
                $this->noOfCommentsRecieved = $this->getNoOfCommentsFor($this->profileUserId);
            }
        }
    }
}
