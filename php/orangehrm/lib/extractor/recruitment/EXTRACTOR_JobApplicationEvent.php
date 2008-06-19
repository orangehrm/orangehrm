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
 require_once ROOT_PATH . '/lib/models/recruitment/JobApplicationEvent.php';

 class EXTRACTOR_JobApplicationEvent {

	/**
	 * Parse data from interface and return JobApplicationEvent Object
	 * @param Array $postArr Array containing POST values
	 * @return JobApplicationEvent Job Application Event object
	 */
	public function parseUpdateData($postArr) {

        $id = $postArr['txtId'];
		$event = JobApplicationEvent::getJobApplicationEvent($id);

        if (isset($postArr['cmbStatus'])) {
            $status = $postArr['cmbStatus'];
            $event->setStatus($status);
        }

        if (isset($postArr['txtNotes'])) {
            $notes = $postArr['txtNotes'];
            $event->setNotes($notes);
        }
        return $event;
	}

    /**
     * Parse data from seek approval request and return JobApplicationEvent
     * object
     * @param Array $postArr Array containing POST values
     * @return JobApplicationEvent Job Application Event object
     */
    public function parseSeekApprovalData($postArr) {

        $event = new JobApplicationEvent();

        $id = $postArr['txtId'];
        $event->setApplicationId($id);

        $interviewer = $postArr['cmbDirector'];
        $event->setOwner($interviewer);

        $notes = $postArr['txtNotes'];
        $event->setNotes($notes);

        return $event;
    }

    /**
     * Parse data from interface and return JobApplicationEvent Object
     * @param Array $postArr Array containing POST values
     * @return JobApplicationEvent Job Application Event object
     */
    public function parseAddData($postArr) {

        $event = new JobApplicationEvent();

        // Application ID
        $id = $postArr['appId'];
        $event->setApplicationId($id);

        if (isset($postArr['txtOwner'])) {
            $event->setOwner($postArr['txtOwner']);
        }

        if (isset($postArr['txtNotes'])) {
            $event->setNotes($postArr['txtNotes']);
        }
        return $event;
    }

}
?>