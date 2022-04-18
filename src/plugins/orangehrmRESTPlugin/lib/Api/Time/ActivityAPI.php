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

namespace Orangehrm\Rest\Api\Time;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;
use Orangehrm\Rest\Api\Time\Entity\ProjectActivity;

class ActivityAPI extends EndPoint
{

    const PARAMETER_PROJECT_ID = "projectId";
    const PARAMETER_NAME = "name";
    const PARAMETER_ID = "id";
    const PARAMETER_ACTIVITY_ID = "activityId";

    private $projectService;


    /**
     *
     * @return ProjectService
     */
    public function getProjectService()
    {
        if (is_null($this->projectService)) {
            $this->projectService = new \ProjectService();
        }
        return $this->projectService;
    }

    public function setProjectService($projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * get getActivity
     *
     * @return Response
     * @throws InvalidParamException
     */
    public function getActivity()
    {
        $id = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $activities = $this->getProjectService()->getActivityListByProjectId($id);
        foreach ($activities as $activity) {

            $projectActivity = new ProjectActivity();
            $projectActivity->build($activity);
            $responseArray[] = $projectActivity->toArray();
        }
        if (count($responseArray) > 0) {
            return new Response($responseArray, array());
        } else {
            throw new InvalidParamException('No Records Found');
        }
    }

    /**
     * Save Activity
     *
     * @return Response
     * @throws InvalidParamException
     */
    public function saveActivity()
    {
        $filters = $this->filterParameters();
        $project = $this->getProjectService()->getProjectById($filters[self::PARAMETER_PROJECT_ID]);

        if ($project instanceof \Project && $this->checkActivityName($project, $filters[self::PARAMETER_NAME])) {

            $activity = $this->save($filters);
            if($activity instanceof \ProjectActivity){
                return new Response(array('success' => 'Successfully Saved','activityId'=>$activity->getActivityId()));
            }else {
                throw new BadRequestException('Unable To Save');
            }

        } else {
            throw new InvalidParamException('No Projects Found');
        }
    }

    /**
     * Update project activity
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    public function updateActivity()
    {
        $filters = $this->filterParameters();
        $project = $this->getProjectService()->getProjectById($filters[self::PARAMETER_PROJECT_ID]);
        $activityId = $filters[self::PARAMETER_ACTIVITY_ID];

        if ($project instanceof \Project && $this->checkActivityName($project, $filters[self::PARAMETER_NAME])) {

            if (empty($activityId)) {
                throw new InvalidParamException("Activity Id Cannot Be Empty");
            }
            $activity = $this->getProjectActivity($activityId);
            $activityReturned = $this->update($activity,$filters);

            if($activityReturned instanceof \ProjectActivity) {
                return new Response(array('success' => 'Successfully Updated'));
            } else {
                throw new BadRequestException('Unable To Save');
            }

        } else {
            throw new InvalidParamException('No Projects Found');
        }
    }

    /**
     * Delete project activity
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function deleteActivity()
    {
        $filters = $this->filterDeleteParameters();
        $activityId = $filters[self::PARAMETER_ACTIVITY_ID];
        $projectId = $filters[self::PARAMETER_PROJECT_ID];
        $project = $this->getProjectService()->getProjectById($projectId);

        if ($project instanceof \Project) {
            $activity = $this->getProjectService()->getProjectActivityById($activityId);
            $this->checkActivityForDelete($activity, $project);

            $isActivityHasTimeSheets = $this->getProjectService()->hasActivityGotTimesheetItems($activityId);
            if (!$isActivityHasTimeSheets) {

                $this->getProjectService()->deleteProjectActivities($activityId);
                return new Response(array('success' => 'Successfully Deleted'));
            } else {
                throw new InvalidParamException("Activity Cannot Be Deleted");
            }
        } else {
            throw new RecordNotFoundException("Project Not Found");
        }


    }

    /**
     * Filter parameters
     *
     * @return array
     * @throws InvalidParamException
     */
    protected function filterParameters()
    {
        $filters[] = array();

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_PROJECT_ID))) {
            $filters[self::PARAMETER_PROJECT_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_PROJECT_ID);
        } else {
            throw new InvalidParamException('Project Id Is Not Set');
        }

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_NAME))) {
            $filters[self::PARAMETER_NAME] = $this->getRequestParams()->getPostParam(self::PARAMETER_NAME);
        } else {
            throw new InvalidParamException('Activity Name Is Not Set');
        }

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_ACTIVITY_ID))) {
            $filters[self::PARAMETER_ACTIVITY_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_ACTIVITY_ID);
        }

        return $filters;

    }

    /**
     * filter parameters for delete
     *
     * @return array
     * @throws InvalidParamException
     */
    protected function filterDeleteParameters()
    {
        $filters[] = array();

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_PROJECT_ID))) {
            $filters[self::PARAMETER_PROJECT_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_PROJECT_ID);
        } else {
            throw new InvalidParamException('Project Id Is Not Set');
        }

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_ACTIVITY_ID))) {
            $filters[self::PARAMETER_ACTIVITY_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_ACTIVITY_ID);
        }

        return $filters;

    }

    public function getPostValidationRules()
    {
        return array(
            self::PARAMETER_PROJECT_ID => array('IntVal' => true, 'NotEmpty' => true, 'Length' => array(1, 200)),
            self::PARAMETER_NAME => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 100)),
        );
    }

    public function getPutValidationRules()
    {
        return array(
            self::PARAMETER_ACTIVITY_ID => array('IntVal' => true, 'NotEmpty' => true, 'Length' => array(1, 200)),
            self::PARAMETER_PROJECT_ID => array('IntVal' => true, 'NotEmpty' => true, 'Length' => array(1, 200)),
            self::PARAMETER_NAME => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 100)),
        );
    }

    public function getDeleteValidationRules()
    {
        return array(
            self::PARAMETER_ACTIVITY_ID => array('IntVal' => true, 'NotEmpty' => true),
            self::PARAMETER_PROJECT_ID => array('IntVal' => true, 'NotEmpty' => true, 'Length' => array(1, 200)),
        );
    }

    /**
     * Check Activity Name
     *
     * @param \Project $project
     * @param $activityName
     * @return bool
     * @throws InvalidParamException
     */
    public function checkActivityName(\Project $project, $activityName)
    {
        $activityList = $this->getProjectService()->getActivityListByProjectId($project->getProjectId());
        foreach ($activityList as $activity) {
            if ($activity->getName() == $activityName) {
                throw new InvalidParamException('Activity Name Already Exists');
            }

        }
        return true;
    }

    /**
     * Get Project Activity
     *
     * @param activityId
     * @return mixed
     * @throws InvalidParamException
     */
    public function getProjectActivity($activityId)
    {
        $activity = $this->getProjectService()->getProjectActivityById($activityId);
        if ($activity instanceof \ProjectActivity) {
            return $activity;
        } else {
            throw new InvalidParamException("Project Activity Not Found");
        }

    }

    /**
     * Check activity before delete
     *
     * @param $activity
     * @param $project
     * @param $newActivityName
     * @throws RecordNotFoundException
     */
    public function checkActivityForDelete($activity, $project)
    {
        if(!$activity instanceof \ProjectActivity || $activity->getIsDeleted() == 1){

            throw  new RecordNotFoundException('Activity Not Found');
        }

    }

    /**
     * Save
     *
     * @param $filters
     * @return \ProjectActivity
     */
    public function save($filters){

        $activity = new \ProjectActivity();
        $activity->setProjectId($filters[self::PARAMETER_PROJECT_ID]);
        $activity->setName($filters[self::PARAMETER_NAME]);
        $activity->save();
        return $activity;
    }

    /**
     * Update
     *
     * @param $activity
     * @param $filters
     * @return mixed
     */
    public function update($activity,$filters){

        $activity->setName($filters[self::PARAMETER_NAME]);
        $activity->save();
        return $activity;
    }

}

