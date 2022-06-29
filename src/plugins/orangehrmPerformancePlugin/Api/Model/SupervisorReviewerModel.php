<?php

namespace OrangeHRM\Performance\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Reviewer;

class SupervisorReviewerModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Reviewer $reviewer)
    {
        $this->setEntity($reviewer);
        $this->setFilters(
            [
                'id',
                ['getEmployee', 'getEmpNumber'],
                ['getEmployee', 'getLastName'],
                ['getEmployee', 'getFirstName'],
                ['getEmployee', 'getMiddleName'],
                ['getEmployee', 'getEmployeeId'],
                ['getEmployee', 'getEmployeeTerminationRecord', 'getId'],
                ['getEmployee', 'getJobTitle', 'getId'],
                ['getEmployee', 'getJobTitle', 'getJobTitleName'],
                ['getEmployee','getJobTitle', 'isDeleted'],
                'status'
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                ['employee','empNumber'],
                ['employee','lastName'],
                ['employee','firstName'],
                ['employee','middleName'],
                ['employee','employeeId'],
                ['employee','terminationId'],
                ['employee','jobTitle','id'],
                ['employee','jobTitle','name'],
                ['employee','jobTitle', 'deleted'],
                'status'
            ]
        );
    }
}
