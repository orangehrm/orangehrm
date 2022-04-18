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

class GenericBasicDisplayField extends BasicDisplayField
{
    public const BASIC_DISPLAY_FIELD_MAP = [
        // Personal
        'employeeId' => ['entityAlias' => 'employee', 'field' => 'employeeId'],
        'employeeLastname' => ['entityAlias' => 'employee', 'field' => 'lastName'],
        'employeeFirstname' => ['entityAlias' => 'employee', 'field' => 'firstName'],
        'employeeMiddlename' => ['entityAlias' => 'employee', 'field' => 'middleName'],
        'maritalStatus' => ['entityAlias' => 'employee', 'field' => 'maritalStatus'],
        'driversLicenseNumber' => ['entityAlias' => 'employee', 'field' => 'drivingLicenseNo'],
        'otherId' => ['entityAlias' => 'employee', 'field' => 'otherId'],
        'employeeNationality' => ['entityAlias' => 'nationality', 'field' => 'name'],

        // Contact details
        'homeTelephone' => ['entityAlias' => 'employee', 'field' => 'homeTelephone'],
        'mobile' => ['entityAlias' => 'employee', 'field' => 'mobile'],
        'workTelephone' => ['entityAlias' => 'employee', 'field' => 'workTelephone'],
        'workEmail' => ['entityAlias' => 'employee', 'field' => 'workEmail'],
        'otherEmail' => ['entityAlias' => 'employee', 'field' => 'otherEmail'],

        // Job
        'empJobTitle' => ['entityAlias' => 'jobTitle', 'field' => 'jobTitleName'],
        'empEmploymentStatus' => ['entityAlias' => 'employmentStatus', 'field' => 'name'],
        'empJobCategory' => ['entityAlias' => 'jobCategory', 'field' => 'name'],
        'empSubUnit' => ['entityAlias' => 'subunit', 'field' => 'name'],
        'empLocation' => ['entityAlias' => 'location', 'field' => 'name'],
        'empTerminationReason' => ['entityAlias' => 'terminationReason', 'field' => 'name'],
        'terminationNote' => ['entityAlias' => 'employeeTerminationRecord', 'field' => 'note'],

        // Custom
        'customField1' => ['entityAlias' => 'employee', 'field' => 'custom1'],
        'customField2' => ['entityAlias' => 'employee', 'field' => 'custom2'],
        'customField3' => ['entityAlias' => 'employee', 'field' => 'custom3'],
        'customField4' => ['entityAlias' => 'employee', 'field' => 'custom4'],
        'customField5' => ['entityAlias' => 'employee', 'field' => 'custom5'],
        'customField6' => ['entityAlias' => 'employee', 'field' => 'custom6'],
        'customField7' => ['entityAlias' => 'employee', 'field' => 'custom7'],
        'customField8' => ['entityAlias' => 'employee', 'field' => 'custom8'],
        'customField9' => ['entityAlias' => 'employee', 'field' => 'custom9'],
        'customField10' => ['entityAlias' => 'employee', 'field' => 'custom10'],
    ];

    private string $entityAlias;
    private string $field;

    /**
     * @param \OrangeHRM\Entity\DisplayField $displayField
     */
    public function setDisplayField(\OrangeHRM\Entity\DisplayField $displayField): void
    {
        $mapping = self::BASIC_DISPLAY_FIELD_MAP[$displayField->getFieldAlias()];
        $this->setEntityAlias($mapping['entityAlias']);
        $this->setField($mapping['field']);
    }

    /**
     * @inheritDoc
     */
    public function getEntityAlias(): string
    {
        return $this->entityAlias;
    }

    /**
     * @param string $entityAlias
     */
    protected function setEntityAlias(string $entityAlias): void
    {
        $this->entityAlias = $entityAlias;
    }

    /**
     * @inheritDoc
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    protected function setField(string $field): void
    {
        $this->field = $field;
    }
}
