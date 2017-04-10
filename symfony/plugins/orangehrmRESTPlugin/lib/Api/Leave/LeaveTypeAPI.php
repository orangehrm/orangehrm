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

namespace Orangehrm\Rest\Api\Leave;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Leave\Entity\LeaveType;

use Orangehrm\Rest\Http\Response;

class LeaveTypeAPI extends EndPoint
{

    private $leaveTypeService;

    const PARAMETER_NAME = 'name';

    /**
     * @return mixed
     */
    public function getLeaveTypeService()
    {
        if ($this->leaveTypeService == null) {
            return new \LeaveTypeService();
        } else {
            return $this->leaveTypeService;
        }

    }

    /**
     * @param mixed $leaveTypeService
     */
    public function setLeaveTypeService($leaveTypeService)
    {
        $this->leaveTypeService = $leaveTypeService;
    }

    /**
     * Get leave types
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getLeaveTypes()
    {
        $leaveTypeList = $this->getLeaveTypeService()->getLeaveTypeList();
        $responseArray = null;
        foreach ($leaveTypeList as $type) {

            $leaveType = new LeaveType($type->getId(), $type->getName());
            $responseArray[] = $leaveType->toArray();
        }
        if (empty($responseArray)) {
            throw new RecordNotFoundException('No Leave Types Available');
        }
        return new Response($responseArray, array());
    }

    /**
     * Save leave type
     *
     * @return Response
     * @throws BadRequestException
     */
    public function saveLeaveType()
    {
        $message = $this->getLeaveTypeService()->saveLeaveType($this->createLeaveType());
         if($message){
             return new Response(array('success' => 'Successfully Saved'));
         } else {
             throw new BadRequestException('Saving Failed');
         }


    }

    /**
     * Create leave type for saving
     *
     * @return \LeaveType
     * @throws InvalidParamException
     */
    protected function createLeaveType()
    {
        $leaveTypeList = $this->getLeaveTypeService()->getLeaveTypeList();
        $leaveTypeName = null;
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_NAME))) {
            $leaveTypeName = $this->getRequestParams()->getPostParam(self::PARAMETER_NAME);
        } else {
            throw new InvalidParamException("Name Cannot Be Empty");
        }
        foreach ($leaveTypeList as $leaveType) {
            if ($leaveType->getName() === $leaveTypeName) {
                throw new InvalidParamException("Leave Type Already Exists");
            }
        }

        $type = new \LeaveType();
        $type->setName($leaveTypeName);
        return $type;

    }

    public function postValidationRules()
    {
        return array(
            self::PARAMETER_NAME => array('NotEmpty' => true, 'Length' => array(1, 50))

        );
    }


}


