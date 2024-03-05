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

namespace OrangeHRM\Core\Report\DisplayField;

class GenericDateDisplayFieldWithAggregate extends GenericDateDisplayField
{
    public const DATE_DISPLAY_FIELD_MAP = [
        'empContStartDate' => ['entityAlias' => 'employmentContract', 'field' => 'employmentContract.startDate', 'aggregate' => 'MAX'],
        'empContEndDate' => ['entityAlias' => 'employmentContract', 'field' => 'employmentContract.endDate', 'aggregate' => 'MAX'],
    ];

    private string $aggregate;

    /**
     * @param \OrangeHRM\Entity\DisplayField $displayField
     */
    public function setDisplayField(\OrangeHRM\Entity\DisplayField $displayField): void
    {
        $mapping = self::DATE_DISPLAY_FIELD_MAP[$displayField->getFieldAlias()];
        $this->setEntityAlias($mapping['entityAlias']);
        $this->setField($mapping['field']);
        $this->setAggregate($mapping['aggregate']);
    }

    /**
     * @inheritDoc
     */
    public function getDtoClass(): string
    {
        return GenericDateDisplayFieldWithAggregateDTO::class;
    }

    /**
     * @return string
     */
    public function getAggregate(): string
    {
        return $this->aggregate;
    }

    /**
     * @param string $aggregate
     */
    public function setAggregate(string $aggregate): void
    {
        $this->aggregate = $aggregate;
    }

    /**
     * @inheritDoc
     */
    public function getSelectPart(): string
    {
        return 'NEW ' . $this->getDtoClass() . '(' .
            $this->getAggregate() . '(' .
                implode(self::SEPARATOR, $this->getFields()) .
                ')'.
            ')';
    }
}
