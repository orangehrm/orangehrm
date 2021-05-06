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
 * DataGroupScreenPermission
 *
 * @ORM\Table(name="ohrm_data_group_screen")
 * @ORM\Entity
 */
class DataGroupScreenPermission
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
     * @var int
     *
     * @ORM\Column(name="permission", type="integer")
     */
    private int $permission;

    /**
     * @var DataGroup
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\DataGroup")
     * @ORM\JoinColumn(name="data_group_id", referencedColumnName="id")
     */
    private DataGroup $dataGroup;

    /**
     * @var Screen
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Screen")
     * @ORM\JoinColumn(name="screen_id", referencedColumnName="id")
     */
    private Screen $screen;

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
     * @return int
     */
    public function getPermission(): int
    {
        return $this->permission;
    }

    /**
     * @param int $permission
     */
    public function setPermission(int $permission): void
    {
        $this->permission = $permission;
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

    /**
     * @return Screen
     */
    public function getScreen(): Screen
    {
        return $this->screen;
    }

    /**
     * @param Screen $screen
     */
    public function setScreen(Screen $screen): void
    {
        $this->screen = $screen;
    }
}
