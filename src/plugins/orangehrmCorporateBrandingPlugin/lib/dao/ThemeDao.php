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
 * Boston, MA 02110-1301, USA
 */

/**
 * Class ThemeDao
 */
class ThemeDao extends BaseDao
{
    /**
     * @param Theme $theme
     * @return boolean
     */
    public function addTheme(Theme $theme)
    {
        try {
            $theme->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $themeName
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function getVariablesByThemeName($themeName)
    {
        try {
            $q = Doctrine_Query::create()
                ->select('t.variables')
                ->from('Theme t')
                ->where('t.themeName = ?', $themeName);
            $value = $q->execute(array(),Doctrine::HYDRATE_SINGLE_SCALAR);
            return $value;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $themeName
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function getThemeByThemeName($themeName)
    {
        try {
            $q = Doctrine_Query::create()
                ->from('Theme t')
                ->where('t.themeName = ?', $themeName);
            $value = $q->fetchOne();
            return $value;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $themeName
     * @return array|Doctrine_Collection|Doctrine_Collection_OnDemand|int
     * @throws DaoException
     */
    public function deleteThemeByThemeName($themeName) {
        try {
            $q = Doctrine_Query::create()
                               ->delete('Theme')
                               ->whereIn('themeName', $themeName);

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}
