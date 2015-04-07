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
 * updateComment
 */
class updateCommentAction extends basePerformanceAction {

    public function execute($request) {

        $trackLogId = trim($request->getParameter("trackLogId"));
        $comment = trim($request->getParameter("trackLogComment"));
        $userId = $this->getUser()->getAttribute('auth.userId');

        //echo $trackLogId . '   ===' . $comment;
        //get track log by id
        //set comment
        //save track log

        $performanceTrackerLog = $this->getPerformanceTrackerService()->getPerformanceTrackerLog($trackLogId);
        $logOwner = $performanceTrackerLog->getUserId();
        //echo 'cur=' . $userId . ' owner=' . $logOwner . '#';
        if ($userId == $logOwner) {
            $performanceTrackerLog->setComment($comment);
            $flag = $this->getPerformanceTrackerService()->savePerformanceTrackerLog($performanceTrackerLog);
            $flag =1;
        }else{
            $flag = 0;
        }
        return $this->renderText($flag);
    }

    protected function isEssMode() {
        $userMode = 'ESS';

        if ($_SESSION['isSupervisor']) {
            $userMode = 'Supervisor';
        }

        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
            $userMode = 'Admin';
        }

        return ($userMode == 'ESS');
    }

}