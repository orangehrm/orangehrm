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
 *
 */

class RecruitmentAuthManager {

    const ROLE_ADMIN = 1;
    const ROLE_HIRING_MANAGER = 2;
    const ROLE_INTERVIEW1_MANAGER = 3;
    const ROLE_INTERVIEW2_MANAGER = 4;
    const ROLE_DIRECTOR = 5;
    const ROLE_OTHER_MANAGER = 6;
    const ROLE_OTHER = 7;
    const ROLE_OTHER_DIRECTOR = 8;

    /**
     * Array that defines the various actions permitted to different users at different states
     * of the job application
     */
    private static $permissions = array(
        JobApplication::STATUS_SUBMITTED => array(
            self::ROLE_ADMIN => array(JobApplication::ACTION_REJECT, JobApplication::ACTION_SCHEDULE_FIRST_INTERVIEW),
            self::ROLE_HIRING_MANAGER => array(JobApplication::ACTION_REJECT, JobApplication::ACTION_SCHEDULE_FIRST_INTERVIEW),
            self::ROLE_INTERVIEW1_MANAGER => array(),
            self::ROLE_INTERVIEW2_MANAGER => array(),
            self::ROLE_DIRECTOR => array(),
            self::ROLE_OTHER_MANAGER => array(),
            self::ROLE_OTHER => array(),
            self::ROLE_OTHER_DIRECTOR => array(),
        ),
        JobApplication::STATUS_FIRST_INTERVIEW_SCHEDULED => array(
            JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED => array(
                self::ROLE_ADMIN => array(JobApplication::ACTION_REJECT),
                self::ROLE_HIRING_MANAGER => array(JobApplication::ACTION_REJECT),
                self::ROLE_INTERVIEW1_MANAGER => array(JobApplication::ACTION_REJECT),
                self::ROLE_INTERVIEW2_MANAGER => array(),
                self::ROLE_DIRECTOR => array(),
                self::ROLE_OTHER_MANAGER => array(),
                self::ROLE_OTHER => array(),
                self::ROLE_OTHER_DIRECTOR => array(),
            ),
            JobApplicationEvent::STATUS_INTERVIEW_FINISHED => array(
                self::ROLE_ADMIN => array(JobApplication::ACTION_REJECT, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW),
                self::ROLE_HIRING_MANAGER => array(JobApplication::ACTION_REJECT, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW),
                self::ROLE_INTERVIEW1_MANAGER => array(JobApplication::ACTION_REJECT, JobApplication::ACTION_SCHEDULE_SECOND_INTERVIEW),
                self::ROLE_INTERVIEW2_MANAGER => array(),
                self::ROLE_DIRECTOR => array(),
                self::ROLE_OTHER_MANAGER => array(),
                self::ROLE_OTHER => array(),
                self::ROLE_OTHER_DIRECTOR => array(),
            )
        ),
        JobApplication::STATUS_SECOND_INTERVIEW_SCHEDULED => array(
            JobApplicationEvent::STATUS_INTERVIEW_SCHEDULED => array(
                self::ROLE_ADMIN => array(JobApplication::ACTION_REJECT),
                self::ROLE_HIRING_MANAGER => array(JobApplication::ACTION_REJECT),
                self::ROLE_INTERVIEW1_MANAGER => array(),
                self::ROLE_INTERVIEW2_MANAGER => array(JobApplication::ACTION_REJECT),
                self::ROLE_DIRECTOR => array(),
                self::ROLE_OTHER_MANAGER => array(),
                self::ROLE_OTHER => array(),
                self::ROLE_OTHER_DIRECTOR => array(),
            ),
            JobApplicationEvent::STATUS_INTERVIEW_FINISHED => array(
                self::ROLE_ADMIN => array(JobApplication::ACTION_REJECT, JobApplication::ACTION_OFFER_JOB),
                self::ROLE_HIRING_MANAGER => array(JobApplication::ACTION_REJECT, JobApplication::ACTION_OFFER_JOB),
                self::ROLE_INTERVIEW1_MANAGER => array(),
                self::ROLE_INTERVIEW2_MANAGER => array(JobApplication::ACTION_REJECT, JobApplication::ACTION_OFFER_JOB),
                self::ROLE_DIRECTOR => array(),
                self::ROLE_OTHER_MANAGER => array(),
                self::ROLE_OTHER => array(),
                self::ROLE_OTHER_DIRECTOR => array(),
            ),
        ),
        JobApplication::STATUS_JOB_OFFERED => array(
            self::ROLE_ADMIN => array(JobApplication::ACTION_MARK_OFFER_DECLINED, JobApplication::ACTION_SEEK_APPROVAL),
            self::ROLE_HIRING_MANAGER => array(JobApplication::ACTION_MARK_OFFER_DECLINED, JobApplication::ACTION_SEEK_APPROVAL),
            self::ROLE_INTERVIEW1_MANAGER => array(),
            self::ROLE_INTERVIEW2_MANAGER => array(),
            self::ROLE_DIRECTOR => array(),
            self::ROLE_OTHER_MANAGER => array(),
            self::ROLE_OTHER => array(),
            self::ROLE_OTHER_DIRECTOR => array(),
        ),
        JobApplication::STATUS_OFFER_DECLINED => array(
            self::ROLE_ADMIN => array(),
            self::ROLE_HIRING_MANAGER => array(),
            self::ROLE_INTERVIEW1_MANAGER => array(),
            self::ROLE_INTERVIEW2_MANAGER => array(),
            self::ROLE_DIRECTOR => array(),
            self::ROLE_OTHER_MANAGER => array(),
            self::ROLE_OTHER => array(),
            self::ROLE_OTHER_DIRECTOR => array(),
        ),
        JobApplication::STATUS_PENDING_APPROVAL => array(
            self::ROLE_ADMIN => array(),
            self::ROLE_HIRING_MANAGER => array(),
            self::ROLE_INTERVIEW1_MANAGER => array(),
            self::ROLE_INTERVIEW2_MANAGER => array(),
            self::ROLE_DIRECTOR => array(JobApplication::ACTION_REJECT, JobApplication::ACTION_APPROVE),
            self::ROLE_OTHER_MANAGER => array(),
            self::ROLE_OTHER => array(),
            self::ROLE_OTHER_DIRECTOR => array(),
        ),
        JobApplication::STATUS_HIRED => array(
            self::ROLE_ADMIN => array(),
            self::ROLE_HIRING_MANAGER => array(),
            self::ROLE_INTERVIEW1_MANAGER => array(),
            self::ROLE_INTERVIEW2_MANAGER => array(),
            self::ROLE_DIRECTOR => array(),
            self::ROLE_OTHER_MANAGER => array(),
            self::ROLE_OTHER => array(),
            self::ROLE_OTHER_DIRECTOR => array(),
        ),
        JobApplication::STATUS_REJECTED => array(
            self::ROLE_ADMIN => array(),
            self::ROLE_HIRING_MANAGER => array(),
            self::ROLE_INTERVIEW1_MANAGER => array(),
            self::ROLE_INTERVIEW2_MANAGER => array(),
            self::ROLE_DIRECTOR => array(),
            self::ROLE_OTHER_MANAGER => array(),
            self::ROLE_OTHER => array(),
            self::ROLE_OTHER_DIRECTOR => array(),
        )
    );

    /**
     * Get the role of the given user in relation to the given job application
     *
     * @param authorize $authObj authorize class representing logged in user
     * @param JobApplication Job Application relative to which roles are required
     *
     * @return int One of the ROLE_ constants defined in this class
     */
    public function getRoleForApplication($authObj, $jobApplication) {

        if ($authObj->isAdmin()) {
            return self::ROLE_ADMIN;
        }

        if ($authObj->isManager() || $authObj->isOfferer()) {

            // Check if director
            $event = $jobApplication->getEventOfType(JobApplicationEvent::EVENT_SEEK_APPROVAL);
            if (!empty($event) && $event->getOwner() == $authObj->getEmployeeId()) {
                return self::ROLE_DIRECTOR;
            }

            // Check if hiring manager
            $vacancy = JobVacancy::getJobVacancy($jobApplication->getVacancyId());
            if ($authObj->getEmployeeId() == $vacancy->getManagerId()) {
                return self::ROLE_HIRING_MANAGER;
            }

            // Check if interview 2 manager
            $event = $jobApplication->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW);
            if (!empty($event) && $event->getOwner() == $authObj->getEmployeeId()) {
                return self::ROLE_INTERVIEW2_MANAGER;
            }

            // Check if interview 1 manager
            $event = $jobApplication->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW);
            if (!empty($event) && $event->getOwner() == $authObj->getEmployeeId()) {
                return self::ROLE_INTERVIEW1_MANAGER;
            }

            return self::ROLE_OTHER_MANAGER;
        }

        if ($authObj->isDirector() || $authObj->isAcceptor()) {

            // Check if director
            $event = $jobApplication->getEventOfType(JobApplicationEvent::EVENT_SEEK_APPROVAL);
            if (!empty($event) && $event->getOwner() == $authObj->getEmployeeId()) {
                return self::ROLE_DIRECTOR;
            }

            return self::ROLE_OTHER_DIRECTOR;
        }

        return self::ROLE_OTHER;
    }

    /**
     * Get actions allowed for the given user on the given Job Application
     *
     * @param authorize $authObj authorize class representing logged in user
     * @param JobApplication Job Application against which action should be tested.
     *
     * @return array Array of allowed actions
     */
    public function getAllowedActions($authObj, $jobApplication) {

        $actions = array();

        $role = $this->getRoleForApplication($authObj, $jobApplication);
        $status = $jobApplication->getStatus();

        if ($status == JobApplication::STATUS_FIRST_INTERVIEW_SCHEDULED) {

            $event = $jobApplication->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW);
            if (!is_null($event)) {
                $eventStatus = $event->getStatus();

                if (isset(self::$permissions[$status][$eventStatus][$role])) {
                    $actions = self::$permissions[$status][$eventStatus][$role];
                }
            }
        } else if ($status == JobApplication::STATUS_SECOND_INTERVIEW_SCHEDULED) {

            $event = $jobApplication->getEventOfType(JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW);
            if (!is_null($event)) {
                $eventStatus = $event->getStatus();

                if (isset(self::$permissions[$status][$eventStatus][$role])) {
                    $actions = self::$permissions[$status][$eventStatus][$role];
                }
            }
        } else {
            if (isset(self::$permissions[$status][$role])) {
                $actions = self::$permissions[$status][$role];
            }
        }

        return $actions;
    }

    /**
     * Checks if the given action is allowed on the given Job Application by the given
     * user.
     *
     * @param authorize $authObj authorize class representing logged in user
     * @param JobApplication Job Application against which action should be tested.
     * @param int Action constant from class JobApplication
     *
     * @return bool True if action is allowed, false otherwise.
     */
    public function isActionAllowed($authObj, $jobApplication, $action) {

        $allowedActions = $this->getAllowedActions($authObj, $jobApplication);
        return (array_search($action, $allowedActions) !== false);
    }

    /**
     * Checks if the given user is allowed to edit the given event
     *
     * @param authorize $authObj authorize class representing logged in user
     * @param JobApplicationEvent Job Application Event which needs to be edited
     *
     * @return bool True if action is allowed, false otherwise.
     */
    public function isAllowedToEditEvent($authObj, $jobApplicationEvent) {

        $application = JobApplication::getJobApplication($jobApplicationEvent->getApplicationId());
        $role = $this->getRoleForApplication($authObj, $application);

        // Admin always allowed.
        if ($role == self::ROLE_ADMIN) {
            return true;
        }

        // Owner is also allowed to edit (only for interviews)
        $owner = $jobApplicationEvent->getOwner();
        if (($owner == $authObj->getEmployeeId()) &&
                (($jobApplicationEvent->getEventType() == JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW) ||
                 ($jobApplicationEvent->getEventType() == JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW))) {
            return true;
        }

        // creator is also always allowed to edit
        $creator = $jobApplicationEvent->getCreatedBy();
        $users = new Users();
        $userInfo = $users->filterUsers($creator);
        if (isset($userInfo[0][11]) && ($userInfo[0][11] == $authObj->getEmployeeId())) {
            return true;
        }

        // Others not allowed to edit approve event by directors
        $eventType = $jobApplicationEvent->getEventType();
        if ($eventType == JobApplicationEvent::EVENT_APPROVE) {
            return false;
        }

        // Hiring manager always allowed. (Except for director's approve action)
        if ($role == self::ROLE_HIRING_MANAGER) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the given user is allowed to change the status of the given event
     *
     * @param authorize $authObj authorize class representing logged in user
     * @param JobApplicationEvent Job Application Event which needs to be edited
     *
     * @return bool True if action is allowed, false otherwise.
     */
    public function isAllowedToChangeEventStatus($authObj, $jobApplicationEvent) {

        if (!$this->isAllowedToEditEvent($authObj, $jobApplicationEvent)) {
            return false;
        }

        $application = JobApplication::getJobApplication($jobApplicationEvent->getApplicationId());
        $status = $application->getStatus();
        $eventType = $jobApplicationEvent->getEventType();
        switch ($eventType) {
            case JobApplicationEvent::EVENT_SCHEDULE_FIRST_INTERVIEW:
                return ($status == JobApplication::STATUS_FIRST_INTERVIEW_SCHEDULED);
                break;
            case JobApplicationEvent::EVENT_SCHEDULE_SECOND_INTERVIEW:
                return ($status == JobApplication::STATUS_SECOND_INTERVIEW_SCHEDULED);
                break;
            default:
                return false;
        }
    }
}
?>
