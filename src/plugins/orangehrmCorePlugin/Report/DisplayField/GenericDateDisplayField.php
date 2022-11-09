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

namespace OrangeHRM\Core\Report\DisplayField;

class GenericDateDisplayField extends CombinedDisplayField
{
    public const DATE_DISPLAY_FIELD_MAP = [
        // Personal
        'empBirthday' => ['entityAlias' => 'employee', 'field' => 'employee.birthday'],
        'licenseExpiryDate' => ['entityAlias' => 'employee', 'field' => 'employee.drivingLicenseExpiredDate'],

        // Job
        'empJoinedDate' => ['entityAlias' => 'employee', 'field' => 'employee.joinedDate'],
        'empContStartDate' => ['entityAlias' => 'employmentContract', 'field' => 'employmentContract.startDate'],
        'empContEndDate' => ['entityAlias' => 'employmentContract', 'field' => 'employmentContract.endDate'],
        'terminationDate' => [
            'entityAlias' => 'employeeTerminationRecord',
            'field' => 'employeeTerminationRecord.date'
        ],
    ];

    private string $entityAlias;
    private string $field;

    /**
     * @param \OrangeHRM\Entity\DisplayField $displayField
     */
    public function setDisplayField(\OrangeHRM\Entity\DisplayField $displayField): void
    {
        $mapping = self::DATE_DISPLAY_FIELD_MAP[$displayField->getFieldAlias()];
        $this->setEntityAlias($mapping['entityAlias']);
        $this->setField($mapping['field']);
    }

    /**
     * @param string $entityAlias
     */
    protected function setEntityAlias(string $entityAlias): void
    {
        $this->entityAlias = $entityAlias;
    }

    /**
     * @param string $field
     */
    protected function setField(string $field): void
    {
        $this->field = $field;
    }


    /**
     * @inheritDoc
     */
    public function getDtoClass(): string
    {
        return GenericDateDisplayFieldDTO::class;
    }

    /**
     * @inheritDoc
     */
    public function getEntityAliases(): array
    {
        return [$this->entityAlias];
    }

    /**
     * @inheritDoc
     */
    public function getFields(): array
    {
        return [$this->field];
    }
}
