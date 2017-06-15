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

class ProjectAPI extends EndPoint
{

    const PARAMETER_CUSTOMER_ID = "customerId";
    const PARAMETER_NAME = "name";
    const PARAMETER_DESCRIPTION = "description";

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
     * Get projects
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getProjects()
    {
        $projects = $this->getProjectService()->getAllProjects();

        foreach ($projects as $project) {

            $responseArray[] = $project->toArray();
        }
        if(count($responseArray) > 0){
            return new Response($responseArray, array());
        }else {
            throw new RecordNotFoundException('No Projects Found');
        }


    }


    public function saveProject()
    {
        $filters = $this->filterParameters();

        $project = new \Project();
        $project->setCustomerId($filters[self::PARAMETER_CUSTOMER_ID]);
        $project->setName($filters[self::PARAMETER_NAME]);
        $project->setDescription($filters[self::PARAMETER_DESCRIPTION]);
        $project->save();
        return new Response($this->getRequestParams(), array());

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

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_CUSTOMER_ID))) {
            $filters[self::PARAMETER_CUSTOMER_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_CUSTOMER_ID);
        }else {
            throw new InvalidParamException('Customer Id Needed');
        }

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_NAME))) {
            $filters[self::PARAMETER_NAME] = $this->getRequestParams()->getPostParam(self::PARAMETER_NAME);
        }else {
            throw new InvalidParamException('Project Name Needed');
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_DESCRIPTION))) {
            $filters[self::PARAMETER_DESCRIPTION] = $this->getRequestParams()->getPostParam(self::PARAMETER_DESCRIPTION);
        }

        return $filters;

    }


    public function getPostValidationRules()
    {
        return array(
            self::PARAMETER_CUSTOMER_ID => array('NotEmpty' => true,'Length' => array(0, 5)),
            self::PARAMETER_NAME => array('StringType' => true, 'NotEmpty' => true,'Length' => array(1,52)),
            self::PARAMETER_DESCRIPTION => array('Length' => array(0, 256)),
        );
    }


}


