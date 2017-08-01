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

namespace Orangehrm\Rest\Api\Time\Entity;

use Orangehrm\Rest\Api\Entity\Serializable;
use Orangehrm\Rest\Api\Time\Entity\ProjectActivity;
use Orangehrm\Rest\Api\Time\Entity\ProjectAdmin;

class Project implements Serializable
{
    /**
     * @var
     */
    private $projectId = '';

    private $customerId = '';

    private $customerName = '';

    private $projectName = '';

    private $description = '';

    private $isDeleted;

    private $projectAdmins = '';

    private $projectActivities;

    /**
     * @return mixed
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param mixed $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param string $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * @param string $customerName
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;
    }

    /**
     * @return string
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * @param string $projectName
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @param mixed $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return string
     */
    public function getProjectAdmins()
    {
        return $this->projectAdmins;
    }

    /**
     * @param string $projectAdmins
     */
    public function setProjectAdmins($projectAdmins)
    {
        $this->projectAdmins = $projectAdmins;
    }

    /**
     * @return mixed
     */
    public function getProjectActivities()
    {
        return $this->projectActivities;
    }

    /**
     * @param mixed $projectActivities
     */
    public function setProjectActivities($projectActivities)
    {
        $this->projectActivities = $projectActivities;
    }

    public function toArray()
    {
        return array(
            'projectId' => $this->getProjectId(),
            'projectName' => $this->getProjectName(),
            'customerId' => $this->getCustomerId(),
            'customerName' => $this->getCustomerName(),
            'description' => $this->getDescription(),
            'isDeleted' => $this->getIsDeleted(),
            'admins'    => $this->getProjectAdmins(),
            'activities' => $this->getProjectActivities()
        );
    }

    /**
     * Build project
     *
     * @param \Project $project
     */
    public function build(\Project $project)
    {
        $this->setProjectId($project->getProjectId());
        $this->setProjectName($project->getName());
        $this->setCustomerId($project->getCustomerId());
        $this->setCustomerName($project->getCustomerName());
        $this->setDescription($project->getDescription());
        $this->setIsDeleted($project->getIsDeleted());

        // set project activities

        if (count($project->getProjectActivity()) > 0) {

            $projectActivities = null;
            foreach ($project->getProjectActivity() as $activity) {
                $projectActivityEntity = new ProjectActivity();
                $projectActivityEntity->build($activity);
                $projectActivities[] = $projectActivityEntity->toArray();
            }
            $this->setProjectActivities($projectActivities);
        }

        // set project admins

        if(count($project->getProjectAdmin()) > 0){

            $projectAdmins = null;
            foreach ($project->getProjectAdmin() as $projectAdmin){
                $projectAdminEntity = new ProjectAdmin();
                $projectAdminEntity->build($projectAdmin);
                $projectAdmins = $projectAdminEntity->toArray();
            }
            $this->setProjectAdmins($projectAdmins);
        }


    }
}