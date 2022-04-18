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

namespace OrangeHRM\Maintenance\PurgeStrategy;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Maintenance\Dto\InfoArray;
use OrangeHRM\Maintenance\Service\PurgeEmployeeService;

abstract class PurgeStrategy
{
    use EntityManagerHelperTrait;

    protected string $entityClassName;
    protected ?array $parameters;
    protected ?array $entityFieldMap;
    protected PurgeEmployeeService $purgeEmployeeService;

    /**
     * @param string $entityClassName
     * @param InfoArray $infoArray
     */
    public function __construct(string $entityClassName, InfoArray $infoArray)
    {
        $this->entityClassName = $entityClassName;
        $this->entityFieldMap = $infoArray->getMatchArray();
        $this->parameters = $infoArray->getParameterArray();
    }

    /**
     * @param int $employeeNumber
     * @return mixed
     */
    abstract public function purge(int $employeeNumber): void;

    /**
     * @return PurgeEmployeeService
     */
    public function getPurgeEmployeeService(): PurgeEmployeeService
    {
        if (!isset($this->purgeEmployeeService)) {
            $this->purgeEmployeeService = new PurgeEmployeeService();
        }
        return $this->purgeEmployeeService;
    }

    /**
     * @param int $employeeNumber
     * @return array
     */
    public function getMatchByValues(int $employeeNumber): array
    {
        $matchByValues = [];
        $matchByValues[$this->entityFieldMap['match']] = $employeeNumber;
        if (isset($this->entityFieldMap['join'])) {
            $matchByValues['join'] = $this->entityFieldMap['join'];
        }

        return $matchByValues;
    }

    /**
     * @param array $matchByValues
     * @param string $table
     * @return array
     */
    protected function getEntityRecords(array $matchByValues, string $table): array
    {
        return $this->getPurgeEmployeeService()->getPurgeEmployeeDao()->extractDataFromEmpNumber($matchByValues, $table);
    }

    /**
     * @return array|null
     */
    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    /**
     * @return array|null
     */
    public function getEntityFieldMap(): ?array
    {
        return $this->entityFieldMap;
    }
}
