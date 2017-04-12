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

class EmployeeEvent implements Serializable
{

    /**
     * @var
     */
    private $employeeId = 0;

    private $employeeName = '';

    private $type = '';

    private $event = '';

    private $note = '';

    private $createdDate = '';

    private $createdBy = '';




    /**
     * Supervisor constructor.
     * @param $name
     * @param $id
     */
    public function __construct($employeeId, $type, $event )
    {
        $this->setEmployeeId($employeeId);
        $this->setType($type);
        $this->setEvent($event);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @param mixed $employeeId
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return string
     */
    public function getEmployeeName()
    {
        return $this->employeeName;
    }

    /**
     * @param string $employeeName
     */
    public function setEmployeeName($employeeName)
    {
        $this->employeeName = $employeeName;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param string $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param string $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }

    /**
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param string $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }


    public function build(\EmployeeEvent $event){

        $this->setEmployeeName($event->getEmployee()->getFullName());
        $this->setCreatedBy($event->getCreatedBy());
        $this->setCreatedDate($event->getCreatedDate());
        $this->setNote($event->getNote());
    }
    /**
     * Employee event to array conversion
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'employeeId' => $this->getEmployeeId(),
            'employeeName' => $this->getEmployeeName(),
            'event' => $this->getEvent(),
            'type' => $this->getType(),
            'createdBy' => $this->getCreatedBy(),
            'createdDate' => $this->getCreatedDate(),
            'note' => $this->getNote()
        );
    }


}
