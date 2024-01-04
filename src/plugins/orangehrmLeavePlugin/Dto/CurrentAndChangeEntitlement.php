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

namespace OrangeHRM\Leave\Dto;

class CurrentAndChangeEntitlement
{
    /**
     * @var array
     *
     * array(
     *   '2012-01-01' => array(1 => 1),
     *   '2012-01-02' => array(4 => 1),
     * )
     */
    private array $current;

    /**
     * @var array
     *
     * array(
     *   12 => array(3 => 1),
     *   13 => array(4 => 0.5),
     *   leave_id => array(entitlement_id => length),
     * )
     */
    private array $change;

    /**
     * @param array $current
     * @param array $change
     */
    public function __construct(array $current = [], array $change = [])
    {
        $this->current = $current;
        $this->change = $change;
    }

    /**
     * @return array
     */
    public function getCurrent(): array
    {
        return $this->current;
    }

    /**
     * @param array $current
     */
    public function setCurrent(array $current): void
    {
        $this->current = $current;
    }

    /**
     * @return array
     */
    public function getChange(): array
    {
        return $this->change;
    }

    /**
     * @param array $change
     */
    public function setChange(array $change): void
    {
        $this->change = $change;
    }
}
