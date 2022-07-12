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
use OrangeHRM\Entity\Kpi;
use OrangeHRM\Performance\Traits\Service\KpiServiceTrait;

class KpiModel implements Normalizable
{
    use ModelTrait {
        ModelTrait::toArray as entityToArray;
    }
    use KpiServiceTrait;

    private Kpi $kpi;

    public function __construct(Kpi $kpi)
    {
        $this->kpi = $kpi;
        $this->setEntity($kpi);
        $this->setFilters(
            [
                'id',
                'title',
                ['getJobTitle', 'getId'],
                ['getJobTitle', 'getJobTitleName'],
                ['getJobTitle', 'isDeleted'],
                'minRating',
                'maxRating',
                ['isDefaultKpi'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'title',
                ['jobTitle', 'id'],
                ['jobTitle', 'name'],
                ['jobTitle', 'deleted'],
                'minRating',
                'maxRating',
                'isDefault',
            ]
        );
    }

    public function toArray(): array
    {
        $deletable = $this->getKpiService()->getKpiDao()->isKpiDeletable(
            $this->kpi->getId()
        );
        $result = $this->entityToArray();
        $result['deletable'] = $deletable;
        return $result;
    }
}
