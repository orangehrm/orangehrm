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

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerServiceTrait;
use OrangeHRM\Pim\Api\Model\EmployeeModel;

class DetailedPerformanceTrackerModel implements Normalizable
{
    use NormalizerServiceTrait;
    use DateTimeHelperTrait;
    use PerformanceTrackerServiceTrait;

    /**
     * @var PerformanceTracker
     */
    private PerformanceTracker $performanceTracker;

    /**
     * @param PerformanceTracker $performanceTracker
     */
    public function __construct(PerformanceTracker $performanceTracker)
    {
        $this->performanceTracker = $performanceTracker;
    }

    /**
     * @return PerformanceTracker
     */
    public function getPerformanceTracker(): PerformanceTracker
    {
        return $this->performanceTracker;
    }


    public function toArray(): array
    {
        $detailedPerformanceTracker = $this->getPerformanceTracker();
        $reviewers = $this->getNormalizerService()->normalizeArray(
            PerformanceTrackReviewerModel::class,
            $this->getPerformanceTrackerService()
                ->getPerformanceTrackerDao()
                ->getReviewerListByTrackerId($detailedPerformanceTracker->getId())
        );
        return [
            'id' => $detailedPerformanceTracker->getId(),
            'trackerName' => $detailedPerformanceTracker->getTrackerName(),
            'addedDate' => $detailedPerformanceTracker->getDecorator()->getAddedDate(),
            'modifiedDate' => $detailedPerformanceTracker->getDecorator()->getModifiedDate(),
            'empNumber' => $detailedPerformanceTracker->getEmployee()->getEmpNumber(),
            'lastName' => $detailedPerformanceTracker->getEmployee()->getLastName(),
            'firstName' => $detailedPerformanceTracker->getEmployee()->getFirstName(),
            'terminationId' =>$detailedPerformanceTracker->getEmployee()->getEmployeeTerminationRecord(),
            'reviewers' => $reviewers,
        ];


    }
}