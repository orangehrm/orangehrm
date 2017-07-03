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
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Http\Response;

class ActivityAPI extends EndPoint
{

    const PARAMETER_PROJECT_ID = "projectId";
    const PARAMETER_NAME = "name";
    const PARAMETER_ID = "id";

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

    /**
     * get getActivity
     *
     * @return Response
     * @throws InvalidParamException
     */
    public function getActivity()
    {
        $id =  $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $activities = $this->getProjectService()->getActivityListByProjectId($id);
        foreach ($activities as $activity) {
            $responseArray[] = $activity->toArray();
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

            $activity = new \ProjectActivity();
            $activity->setProjectId($filters[self::PARAMETER_PROJECT_ID]);
            $activity->setName($filters[self::PARAMETER_NAME]);
            $activity->save();

            return new Response(array('success' => 'Successfully Saved'));

        } else {
            throw new InvalidParamException('No Projects Found');
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

        if (!is_numeric($filters[self::PARAMETER_PROJECT_ID])) {
            throw new InvalidParamException("Project Id Should Be Numeric");
        }

        return $filters;

    }

    public function getPostValidationRules()
    {
        return array(
            self::PARAMETER_PROJECT_ID => array('NotEmpty' => true, 'Length' => array(1, 50)),
            self::PARAMETER_NAME => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 100)),
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

}


