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
            ['getJobTitle', 'getJobTitleName'],
            ['getEmployee', 'getLastName'],
            ['getEmployee', 'getFirstName'],

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
            ['jobTitle', 'title'],
            ['hiringManager', 'lastName'],
            ['hiringManager', 'firstName'],

        ]);
    }
}