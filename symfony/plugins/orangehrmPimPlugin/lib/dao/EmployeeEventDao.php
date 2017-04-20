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
class EmployeeEventDao extends BaseDao
{
    /**
     * Save employee event
     *
     * @param EmployeeEvent $employeeEvent
     * @return EmployeeEvent
     * @throws DaoException
     */
    public function saveEmployeeEvent(EmployeeEvent $employeeEvent)
    {

        try {
            $employeeEvent->save();
            return $employeeEvent;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }

    }

    /**
     * Get employee event
     *
     * @param ParameterObject $parameters
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function getEmployeeEvent(ParameterObject $parameters)
    {

        try {

            $select = 'event.*, em.firstName, em.lastName, em.middleName';

            $q = Doctrine_Query::create()
                ->select($select)
                ->from('EmployeeEvent event')
                ->leftJoin('event.Employee em');

            $dateRange = $parameters->getParameter('dateRange', new DateRange());
            $fromDate = $dateRange->getFromDate();
            $toDate = $dateRange->getToDate();
            $employeeId = $parameters->getParameter('employeeId');
            $event = $parameters->getParameter('event');
            $type = $parameters->getParameter('type');


            if (!empty($type)) {
                $q->andWhere("event.type = ?", $type);
            }
            if (!empty($event)) {
                $q->andWhere("event.event = ?", $event);
            }
            if (!empty($employeeId)) {
                $q->andWhere("event.employee_id = ?", $employeeId);
            }
            if (!empty($fromDate)) {
                $q->andWhere("event.created_date >= ?", $fromDate);
            }

            if (!empty($toDate)) {
                $q->andWhere("event.created_date <= ?", $toDate);
            }

            return $q->execute();

        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }

    }


}