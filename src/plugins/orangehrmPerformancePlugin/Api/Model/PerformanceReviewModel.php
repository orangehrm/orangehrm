<?php

namespace OrangeHRM\Performance\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\PerformanceReview;

class PerformanceReviewModel implements Normalizable
{
    use ModelTrait;

    public function __construct(PerformanceReview $performanceReview)
    {
        $this->setEntity($performanceReview);
        $this->setFilters(
            [
                'id',
                ['getJobTitle', 'getId'],
                ['getJobTitle', 'getJobTitleName'],
                ['getDepartment','getId'],
                ['getDepartment','getName'],
                ['getDecorator','getWorkPeriodStart'],
                ['getDecorator','getWorkPeriodEnd'],
                ['getDecorator','getDueDate'],
                'statusId',
                ['getDecorator','getStatusName']
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                ['jobTitle','id'],
                ['jobTitle','name'],
                ['department','id'],
                ['department','name'],
                'workPeriodStart',
                'workPeriodEnd',
                'dueDate',
                'statusId',
                'status',
            ]
        );
    }
}
