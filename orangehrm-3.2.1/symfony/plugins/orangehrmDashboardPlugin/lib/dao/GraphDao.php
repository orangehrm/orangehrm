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

/**
 * Description of GraphDao
 */
class GraphDao {

    public function getEmployeeCountBySubUnit() {
        try {

            $q = "
                SELECT (
                        SELECT f.name
                        FROM ohrm_subunit f 
                        WHERE dashboard_get_subunit_parent_id(f.id) = 1
                            AND f.lft <= c.lft and f.rgt >= c.rgt
                    ) AS sub_unit, 
                    COUNT(emp_number) AS emp_count
                FROM hs_hr_employee e
                LEFT JOIN ohrm_subunit c 
                ON e.work_station = c.id
                WHERE (e.termination_id IS NULL)
                GROUP BY sub_unit;  ";
            $pdo = Doctrine_Manager::connection()->getDbh();
            $stmt = $pdo->prepare($q);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_FUNC, array(__CLASS__, "SubunitFormatter"));
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    function SubunitFormatter($subunitName, $count) {
        if (empty($subunitName)) {
            $subunitName = __("Not assigned to Subunits");
        }
        return array("name" => $subunitName, "COUNT" => $count);
    }

}
