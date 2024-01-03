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

namespace OrangeHRM\Maintenance\AccessStrategy;

use OrangeHRM\Maintenance\Service\MaintenanceService;

abstract class AccessStrategy
{
    protected array $parameters = [];
    protected ?string $entityClassName = null;
    protected array $matchByArray = [];
    protected array $matchingCriteria = [];
    protected ?MaintenanceService $maintenanceService = null;
    protected $getRealValueClass = null;

    /**
     * AccessStrategy constructor.
     * @param string $entityClassName
     * @param array $infoArray
     */
    public function __construct(string $entityClassName, array $infoArray)
    {
        $this->setEntityClassName($entityClassName);
        if (isset($infoArray['match_by'])) {
            $this->setMatchBy($infoArray['match_by']);
        }
        if (isset($infoArray['parameters'])) {
            $this->setParameters($infoArray['parameters']);
        }
    }

    /**
     * @param int $employeeNumber
     * @return array|null
     */
    abstract public function access(int $employeeNumber): ?array;

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parametersArray
     */
    public function setParameters(array $parametersArray)
    {
        $this->parameters = $parametersArray;
    }

    /**
     * @return string|null
     */
    public function getEntityClassName(): ?string
    {
        return $this->entityClassName;
    }

    /**
     * @param string $entityClassName
     */
    public function setEntityClassName(string $entityClassName)
    {
        $this->entityClassName = $entityClassName;
    }


    /**
     * @return array
     */
    public function getMatchBy(): array
    {
        return $this->matchByArray;
    }

    /**
     * @param array $matchByArray
     */
    public function setMatchBy(array $matchByArray)
    {
        $this->matchByArray = $matchByArray;
    }

    /**
     * @return MaintenanceService
     */
    public function getMaintenanceService(): MaintenanceService
    {
        if (!isset($this->maintenanceService)) {
            $this->maintenanceService = new MaintenanceService();
        }
        return $this->maintenanceService;
    }

    /**
     * @param MaintenanceService $maintenanceService
     */
    public function setMaintenanceService(MaintenanceService $maintenanceService): void
    {
        $this->maintenanceService = $maintenanceService;
    }

    /**
     * @param int $employeeNumber
     * @return array
     */
    public function getMatchByValues(int $employeeNumber): array
    {
        $entityFieldMap = [];
        $entityFieldMap[$this->getMatchBy()[0]['match']] = $employeeNumber;

        if (isset($this->getMatchBy()[0]['join'])) {
            $entityFieldMap['join'] = $this->getMatchBy()[0]['join'];
        }

        return $entityFieldMap;
    }

    /**
     * @param array $matchByValues
     * @param string $table
     * @return array|null
     */
    public function getEntityRecords(array $matchByValues, string $table): ?array
    {
        return $this->getMaintenanceService()->extractDataFromEmpNumber($matchByValues, $table);
    }

    /**
     * @param string $accessClassName
     * @param string $currentValue
     * @return string|null
     */
    public function getFormattedValue($accessClassName, $currentValue)
    {
        $accessClassName = 'OrangeHRM\\Maintenance\\AccessStrategy\\FormatValue\\' . $accessClassName;
        $this->getRealValueClass = new  $accessClassName();
        return $this->getRealValueClass->getFormattedValue($currentValue);
    }

    /**
     * @param $accessEntity
     * @return array
     */
    public function addRecordsToArray($accessEntity): array
    {
        $parameters = $this->getParameters();
        $data = [];

        foreach ($parameters as $field) {
            $columnName = $field['field'];
            if (isset($field['getter'])) {
                $getterMethod = $field['getter'];
            } else {
                $getterMethod = 'get' . ucfirst($columnName);
            }

            if (is_array($getterMethod)) {
                $value = $accessEntity;
                foreach ($getterMethod as $getter) {
                    $value = $value->$getter();
                    if (is_null($value)) {
                        break;
                    }
                }
            } else {
                $value = $accessEntity->$getterMethod();
            }

            if (!is_null($value) && $value !== "") {
                if (isset($field['class'])) {
                    $value = $this->getFormattedValue($field['class'], $value);
                }
                if (!is_null($value)) {
                    $data[$columnName] = $value;
                }
            }
        }

        return $data;
    }
}
