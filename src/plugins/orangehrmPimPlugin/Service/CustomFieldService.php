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

namespace OrangeHRM\Pim\Service;

use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Pim\Dao\CustomFieldDao;

class CustomFieldService
{
    public const EMPLOYEE_CUSTOM_FIELD_PREFIX = 'custom';

    /**
     * @var CustomFieldDao|null
     */
    private ?CustomFieldDao $customFieldDao = null;

    /**
     * @return CustomFieldDao|null
     */
    public function getCustomFieldDao(): CustomFieldDao
    {
        if (!($this->customFieldDao instanceof CustomFieldDao)) {
            $this->customFieldDao = new CustomFieldDao();
        }
        return $this->customFieldDao;
    }

    /**
     * @param int[] $fieldNumbers
     * @return array<int, string>
     */
    public function generateGettersByFieldNumbers(array $fieldNumbers): array
    {
        $getters = [];
        $getterPrefix = "get" . ucfirst(self::EMPLOYEE_CUSTOM_FIELD_PREFIX);
        foreach ($fieldNumbers as $fieldNum) {
            $getters[$fieldNum] = $getterPrefix . $fieldNum;
        }
        return $getters;
    }

    /**
     * @param int $fieldNum
     * @return string
     */
    public function generateFieldKeyByFieldId(int $fieldNum): string
    {
        return self::EMPLOYEE_CUSTOM_FIELD_PREFIX . $fieldNum;
    }

    /**
     * @param string $fieldKey e.g. 'custom1'
     * @return string
     */
    public function generateSetterByFieldKey(string $fieldKey): string
    {
        return "set" . ucfirst($fieldKey);
    }

    /**
     * @param string[] $fieldKeys e.g. ['custom1', 'custom2']
     * @return int[]
     */
    public function extractFieldNumbersFromFieldKeys(array $fieldKeys): array
    {
        return array_map(
            function (string $fieldKey) {
                return (int)str_replace(self::EMPLOYEE_CUSTOM_FIELD_PREFIX, '', $fieldKey);
            },
            $fieldKeys
        );
    }

    /**
     * @param int $customFieldId
     * @param string $newExtraData
     * @throws DaoException
     */
    public function deleteRelatedEmployeeCustomFieldsExtraData(int $customFieldId, string $newExtraData): void
    {
        $prevExtraData = $this->getCustomFieldDao()->getCustomFieldById($customFieldId)->getExtraData();
        $prevExtraDataArray = array_map('trim', explode(',', $prevExtraData));
        $newExtraDataArray = array_map('trim', explode(',', $newExtraData));
        foreach ($prevExtraDataArray as $extraData) {
            if (!in_array($extraData, $newExtraDataArray)) {
                $this->getCustomFieldDao()->updateEmployeesIfDropDownValueInUse($customFieldId, $extraData);
            }
        }
    }

    /**
     * @return array
     * @throws DaoException
     */
    public function getAllFieldsInUse(): array
    {
        $fieldsInUse = [];
        for ($i = 1; $i <= 10; $i++) {
            if ($this->getCustomFieldDao()->isCustomFieldInUse($i)) {
                array_push($fieldsInUse, $i);
            }
        }
        return $fieldsInUse;
    }
}
