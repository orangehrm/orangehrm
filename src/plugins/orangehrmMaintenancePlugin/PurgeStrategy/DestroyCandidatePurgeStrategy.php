<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Maintenance\PurgeStrategy;

use OrangeHRM\Entity\Candidate;

class DestroyCandidatePurgeStrategy extends PurgeStrategy
{
    /**
     * @param int $vacancyId
     */
    public function purge(int $vacancyId): void
    {
        $matchByValues = $this->getMatchByValues($vacancyId);
        $purgeEntities = $this->getEntityRecords($matchByValues, $this->getEntityClassName());
        foreach ($purgeEntities as $purgeEntity) {
            /** @var Candidate $purgeEntity */
            if (!$purgeEntity->isConsentToKeepData()) {
                $this->getEntityManager()->remove($purgeEntity);
            }
        }
    }
}
