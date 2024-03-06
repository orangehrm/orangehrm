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

class GenericBasicDisplayFieldWithAggregate extends GenericBasicDisplayField
{
    public const BASIC_DISPLAY_FIELD_MAP = [
        'empLocation' => ['entityAlias' => 'location', 'field' => 'name', 'aggregate' => 'MIN'],
    ];

    private string $aggregate;

    /**
     * @param \OrangeHRM\Entity\DisplayField $displayField
     */
    public function setDisplayField(\OrangeHRM\Entity\DisplayField $displayField): void
    {
        $mapping = self::BASIC_DISPLAY_FIELD_MAP[$displayField->getFieldAlias()];
        $this->setEntityAlias($mapping['entityAlias']);
        $this->setField($mapping['field']);
        $this->setAggregate($mapping['aggregate']);
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
     * @return string
     */
    public function getSelectPart(): string
    {
        return $this->getAggregate() . '(' . $this->getEntityAlias() . '.' . $this->getField() . ')';
    }
}
