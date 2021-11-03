<?php

namespace OrangeHRM\Recruitment\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Vacancy;

class VacancyModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Vacancy $vacancy)
    {
        $this->setEntity($vacancy);
        $this->setFilters([
            'id',
            'name',
            'description',
            'numOfPositions',
            'status',
            'isPublished',
            'definedTime',
            'updatedTime',
            ['getJobTitle', 'getId'],
            ['getJobTitle', 'getJobTitleName'],
            ['getEmployee', 'getEmpNumber'],
            ['getEmployee', 'getFirstName'],
            ['getEmployee', 'getMiddleName'],
            ['getEmployee', 'getLastName'],
            ['getEmployee', 'getEmployeeTerminationRecord'],
        ]);

        $this->setAttributeNames([
            'id',
            'name',
            'description',
            'numOfPositions',
            'status',
            'isPublished',
            'definedTime',
            'updatedTime',
            ['jobTitle', 'id'],
            ['jobTitle', 'title'],
            ['hiringManager', 'id'],
            ['hiringManager', 'firstName'],
            ['hiringManager', 'middleName'],
            ['hiringManager', 'lastName'],
            ['hiringManager', 'terminationId'],

        ]);
    }
}
