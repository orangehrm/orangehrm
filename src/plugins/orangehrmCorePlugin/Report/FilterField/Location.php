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

namespace OrangeHRM\Core\Report\FilterField;

use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\ORM\QueryBuilderWrapper;

class Location extends FilterField implements ValueXNormalizable
{
    /**
     * @inheritDoc
     *
     * Default operator: Operator::IN
     * Possible x values:
     *   - 1,2,3     <--- when select all locations
     *   - 1,2,-1    <--- when select locations by country
     *   - 1         <--- when select specific location
     */
    public function addWhereToQueryBuilder(QueryBuilderWrapper $queryBuilderWrapper): void
    {
        $qb = $queryBuilderWrapper->getQueryBuilder();
        if ($this->getOperator() === Operator::IN && !is_null($this->getX())) {
            // explode comma seperated locations when defining the PIM report
            $locationIds = explode(',', $this->getX());

            // remove `-1` which used in 4.x when select by country
            if (($key = array_search('-1', $locationIds)) !== false) {
                unset($locationIds[$key]);
            }
            $qb->andWhere($qb->expr()->in('location.id', ':Location_locations'))
                ->setParameter('Location_locations', $locationIds);
        }
    }

    /**
     * @return array
     */
    private function getLocationIds(): array
    {
        // explode comma seperated locations chain when defining the PIM report
        return explode(',', $this->getX());
    }

    /**
     * @inheritDoc
     */
    public function getEntityAliases(): array
    {
        return ['location'];
    }

    /**
     * @inheritDoc
     */
    public function toArrayXValue(): ?array
    {
        if (empty($this->getX()) || !isset($this->getLocationIds()[0])) {
            return null;
        }
        $locationService = new LocationService();
        $location = $locationService->getLocationById($this->getLocationIds()[0]);
        if ($location instanceof \OrangeHRM\Entity\Location) {
            return [
                'id' => $location->getId(),
                'label' => $location->getName(),
            ];
        }
        return null;
    }
}
