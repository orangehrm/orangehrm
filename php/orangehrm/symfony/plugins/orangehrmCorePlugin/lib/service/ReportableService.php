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
class ReportableService {

    // ReportableDao Data Access Object
    private $reportableDao;

    /**
     * Gets the ReportableDao Data Access Object
     * @return ReportableDao
     */
    public function getReportableDao() {

        if (is_null($this->reportableDao)) {
            $this->reportableDao = new ReportableDao();
        }

        return $this->reportableDao;
    }

    /**
     * Sets Reportable Data Access Object
     * @param ReportableDao $ReportableDao
     * @return void
     */
    public function setReportableDao(ReportableDao $reportableDao) {

        $this->reportableDao = $reportableDao;
    }

    /**
     * Gets selected filter fields array
     * @param integer $reportId
     * @return SelectedFilterField[]
     */
    public function getSelectedFilterFields($reportId) {

        $selectedFilterFields = $this->getReportableDao()->getSelectedFilterFields($reportId);

        return $selectedFilterFields;
    }

    /**
     * Gets selected display fields array.
     * @param integer $reportId
     * @return SelectedDisplayField[]
     */
    public function getSelectedDisplayFields($reportId) {

        $selectedDisplayFields = $this->getReportableDao()->getSelectedDisplayFields($reportId);

        return $selectedDisplayFields;
    }

    /**
     * Gets meta display fields array.
     * @param integer $reportId
     * @return SelectedDisplayField[]
     */
    public function getMetaDisplayFields($reportId) {

        $metaDisplayFields = $this->getReportableDao()->getMetaDisplayFields($reportId);

        return $metaDisplayFields;
    }

    /**
     * Gets the report for the given report id.
     * @param integer $reportId
     * @return Report
     */
    public function getReport($reportId) {

        $report = $this->getReportableDao()->getReport($reportId);

        return $report;
    }

    /**
     * Gets the report group for the given report group id.
     * @param integer $reportGroupId
     * @return ReportGroup
     */
    public function getReportGroup($reportGroupId) {

        $reportGroup = $this->getReportableDao()->getReportGroup($reportGroupId);

        return $reportGroup;
    }

    /**
     * Gets selected group field for the given report id.
     * @param integer $reportId
     * @return SelectedGroupField
     */
    public function getSelectedGroupField($reportId) {

        $selectedGroupField = $this->getReportableDao()->getSelectedGroupField($reportId);

        return $selectedGroupField;
    }

    public function getRuntimeFilterFields($reportGroupId, $type, $selectedFilterFieldIds) {

        $runtimeFilterFields = $this->getReportableDao()->getRuntimeFilterFields($reportGroupId, $type, $selectedFilterFieldIds);

        return $runtimeFilterFields;
    }

    /**
     * Executes the query and return the results as an array.
     * @param string $sql
     * @return array
     */
    public function executeSql($sql) {

        $results = $this->getReportableDao()->executeSql($sql);

        return $results;
    }

    /**
     *
     */
    public function getFilterFieldById($filterFieldId) {

        $filterField = $this->getReportableDao()->getFilterFieldById($filterFieldId);

        return $filterField;
    }

    /**
     * Gets Project Activity, given activity id.
     * @param integer $activityId
     * @return ProjectActivity
     */
    public function getProjectActivityByActivityId($activityId) {

        $projectActivity = $this->getReportableDao()->getProjectActivityByActivityId($activityId);

        return $projectActivity;
    }

    public function getSelectedCompositeDisplayFields($reportId) {

        $selectedCompositeDisplayField = $this->getReportableDao()->getSelectedCompositeDisplayFields($reportId);

        return $selectedCompositeDisplayField;
    }

}

