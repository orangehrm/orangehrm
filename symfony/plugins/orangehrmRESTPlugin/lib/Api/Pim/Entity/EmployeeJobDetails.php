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

namespace Orangehrm\Rest\Api\Pim\Entity;

use Orangehrm\Rest\Api\Entity\Serializable;

class EmployeeJobDetails implements Serializable
{
    /**
     * @var
     */
    private $title = '';

    private $category = '';

    private $joinedDate = '';

    private $startDate = '';

    private $endDate = '';

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getJoinedDate()
    {
        return $this->joinedDate;
    }

    /**
     * @param string $joinedDate
     */
    public function setJoinedDate($joinedDate)
    {
        $this->joinedDate = $joinedDate;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param string $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param string $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }


    public function toArray() {
        return array(
            'title' => $this->getTitle(),
            'category'=> $this->getCategory(),
            'joinedDate' => $this->getJoinedDate(),
            'startDate' => $this->getStartDate(),
            'endDate' => $this->getEndDate()
      );
    }

    /**
     * Setting Job details values
     *
     * @param $employee
     */
    public function build($employee){

        $this->setTitle($employee->getJobTitle()->jobTitleName);
        $this->setJoinedDate($employee->getJoinedDate());
        $this->setCategory($employee->getJobCategory()->getName());
        if(!empty($employee->getContract())){
            $this->setStartDate($employee->getContract()->getStartDate());
            $this->setEndDate($employee->getContract()->getEndDate());
        }
    }
}