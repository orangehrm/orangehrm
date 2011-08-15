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
class displayAttendanceTotalSummaryReportAction extends displayReportAction {

    public function setParametersForListComponent() {

        $param = array();

        if ($this->getRequest()->hasParameter("empName")) {
            $param['empName'] = $this->getRequest()->getParameter("empName");
        }

        if ($this->getRequest()->hasParameter("empStatusName")) {
            $param['empStatusName'] = $this->getRequest()->getParameter("empStatusName");
        }

        if ($this->getRequest()->hasParameter("jobTitName")) {
            $param['jobTitName'] = $this->getRequest()->getParameter("jobTitName");
        }

        if ($this->getRequest()->hasParameter("subUnit")) {
            $param['subUnit'] = $this->getRequest()->getParameter("subUnit");
        }

        $param['attendanceDateRangeFrom'] = $this->getRequest()->getParameter("attendanceDateRangeFrom");
        $param['attendanceDateRangeTo'] = $this->getRequest()->getParameter("attendanceDateRangeTo");

        return $param;
    }

    public function setConfigurationFactory() {

        $confFactory = new AttendanceTotalSummaryReportListConfigurationFactory();

        $this->setConfFactory($confFactory);
    }

    public function setListHeaderPartial() {

        ohrmListComponent::setHeaderPartial("time/attendanceTotalSummaryReportHeader");
    }

    public function setValues() {

    }

}

