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

trait ohrmWidgetTrait
{
    /**
     * @var null|PDO
     */
    private $dbh = null;

    /**
     * @return PDO
     * @throws Doctrine_Manager_Exception
     */
    protected function getDbHandler()
    {
        if (is_null($this->dbh)) {
            $this->dbh = Doctrine_Manager::connection()->getDbh();
        }
        return $this->dbh;
    }

    /**
     * @param $string
     * @return false|string
     * @throws Doctrine_Manager_Exception
     */
    protected function getEscapedString($string)
    {
        return $this->getDbHandler()->quote($string, PDO::PARAM_STR);
    }

    /**
     * @param $string
     * @return false|string
     * @throws Doctrine_Manager_Exception
     */
    protected function getEscapedCommaSeparated($string)
    {
        $items = explode(',', $string);
        $escapedItems = [];
        foreach ($items as $item) {
            $escapedItems[] = $this->getDbHandler()->quote($item);
        }
        return implode(',', $escapedItems);
    }
}
