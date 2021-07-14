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

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_api_permission", uniqueConstraints={@ORM\UniqueConstraint(name="api_name", columns={"api_name"})})
 * @ORM\Entity
 */
class ApiPermission
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="api_name", type="string", length=255)
     */
    private string $apiName;

    /**
     * @var Module
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Module")
     * @ORM\JoinColumn(name="module_id", referencedColumnName="id")
     */
    private Module $module;

    /**
     * @var DataGroup
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\DataGroup", inversedBy="apiPermissions")
     * @ORM\JoinColumn(name="data_group_id", referencedColumnName="id")
     */
    private DataGroup $dataGroup;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getApiName(): string
    {
        return $this->apiName;
    }

    /**
     * @param string $apiName
     */
    public function setApiName(string $apiName): void
    {
        $this->apiName = $apiName;
    }

    /**
     * @return Module
     */
    public function getModule(): Module
    {
        return $this->module;
    }

    /**
     * @param Module $module
     */
    public function setModule(Module $module): void
    {
        $this->module = $module;
    }

    /**
     * @return DataGroup
     */
    public function getDataGroup(): DataGroup
    {
        return $this->dataGroup;
    }

    /**
     * @param DataGroup $dataGroup
     */
    public function setDataGroup(DataGroup $dataGroup): void
    {
        $this->dataGroup = $dataGroup;
    }
}
