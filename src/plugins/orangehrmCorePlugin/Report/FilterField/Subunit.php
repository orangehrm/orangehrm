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

use OrangeHRM\Admin\Traits\Service\CompanyStructureServiceTrait;
use OrangeHRM\ORM\QueryBuilderWrapper;

class Subunit extends FilterField implements ValueXNormalizable, ValueXModifiable
{
    use CompanyStructureServiceTrait;

    /**
     * @inheritDoc
     *
     * Possible operators:
     *   - Operator::IN    <--- default operator
     *   - NULL            <--- when select all subunits
     * Possible x values:
     *   - 1,2,3     <--- when select top level subunit
     *   - 1         <--- when select leaf level subunit
     *   - 0         <--- when select all subunits
     */
    public function addWhereToQueryBuilder(QueryBuilderWrapper $queryBuilderWrapper): void
    {
        $qb = $queryBuilderWrapper->getQueryBuilder();
        if ($this->getOperator() === Operator::IN && !empty($this->getX())) {
            $qb->andWhere($qb->expr()->in('employee.subDivision', ':Subunit_subDivisions'))
                ->setParameter('Subunit_subDivisions', $this->getSubunitIds());
        }
    }

    /**
     * @return array
     */
    private function getSubunitIds(): array
    {
        // explode comma seperated subunit chain when defining the PIM report
        return explode(',', $this->getX());
    }

    /**
     * @inheritDoc
     */
    public function getEntityAliases(): array
    {
        return ['employee'];
    }

    /**
     * @inheritDoc
     */
    public function toArrayXValue(): ?array
    {
        if (empty($this->getX()) || !isset($this->getSubunitIds()[0])) {
            return null;
        }

        $subunit = $this->getCompanyStructureService()->getSubunitById($this->getSubunitIds()[0]);
        if ($subunit instanceof \OrangeHRM\Entity\Subunit) {
            return [
                'id' => $subunit->getId(),
                'label' => $subunit->getName(),
            ];
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function xValueModifier(?string $x): ?string
    {
        if (is_null($this->getX())) {
            return null;
        }
        return implode(
            ',',
            $this->getCompanyStructureService()
                ->getSubunitChainById($this->getX())
        );
    }
}
