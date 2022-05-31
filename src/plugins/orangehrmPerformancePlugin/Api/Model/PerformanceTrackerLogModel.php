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

namespace OrangeHRM\Performance\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\PerformanceTrackerLog;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerLogServiceTrait;

class PerformanceTrackerLogModel implements Normalizable
{
    use ModelTrait {ModelTrait::toArray as modelToArray;}
    use PerformanceTrackerLogServiceTrait;

    public function __construct(PerformanceTrackerLog $performanceTrackerLog)
    {
        $this->setEntity($performanceTrackerLog);
        $this->setFilters(
            [
                'id',
                'log',
                'comment',
                'achievement',
                ['getDecorator', 'getAddedDate'],
                ['getDecorator', 'getModifiedDate'],
                ['getReviewer', 'getEmpNumber'],
                ['getReviewer', 'getLastName'],
                ['getReviewer', 'getFirstName'],
                ['getReviewer', 'getEmployeeTerminationRecord', 'getId'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'log',
                'comment',
                'achievement',
                'addedDate',
                'modifiedDate',
                ['reviewer', 'empNumber'],
                ['reviewer', 'lastName'],
                ['reviewer', 'firstName'],
                ['reviewer', 'terminationId'],
            ]
        );
    }

    public function toArray(): array
    {
        $editability = $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()->checkTrackerLogEditable($this->getEntity());
        $result = $this->modelToArray();
        $result['editable'] = $editability;
        return $result;
    }
}
