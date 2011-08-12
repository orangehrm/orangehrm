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
class ReportableDao {

    /**
     * Get all selected filter fields for the given report id
     * @param integer $reportId
     * @return array of Doctring objects
     */
    public function getSelectedFilterFields($reportId) {

        try {

            $query = Doctrine_Query::create()
                            ->select("filter_field_id")
                            ->from("SelectedFilterField")
                            ->where("report_id = ?", $reportId)
                            ->orderBy("filter_field_order");
            $results = $query->execute()->getData();

            if ($results[0]->getReportId() == null) {
                return null;
            } else {

                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get all selected display fields for the given report id
     * @param integer $reportId
     * @return array of Doctring objects
     */
    public function getSelectedDisplayFields($reportId) {

        try {

            $query = Doctrine_Query::create()
                            ->select("display_field_id")
                            ->from("SelectedDisplayField")
                            ->where("report_id = ?", $reportId);
            $results = $query->execute();

            if ($results[0]->getId() == null) {
                return null;
            } else {
                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get all selected composite display fields for the given report id
     * @param integer $reportId
     * @return array of Doctring objects
     */
    public function getSelectedCompositeDisplayFields($reportId) {

        try {

            $query = Doctrine_Query::create()
                            ->from("SelectedCompositeDisplayField")
                            ->where("report_id = ?", $reportId);
            $results = $query->execute();

            if ($results[0]->getId() == null) {
                return null;
            } else {
                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get all meta display fields for the given report id
     * @param integer $reportId
     * @return array of Doctring objects
     */
    public function getMetaDisplayFields($reportId) {

        try {

            $query = Doctrine_Query::create()
                            ->from("MetaDisplayField")
                            ->where("report_id = ?", $reportId);
            $results = $query->execute();

            if ($results[0]->getId() == null) {
                return null;
            } else {
                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function getReport($reportId) {

        try {

            $query = Doctrine_Query::create()
                            ->from("Report")
                            ->where("report_id = ?", $reportId);
            $result = $query->execute();

            if ($result[0]->getReportId() == null) {
                return null;
            } else {
                return $result[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function getReportGroup($reportGroupId) {

        try {

            $query = Doctrine_Query::create()
                            ->from("ReportGroup")
                            ->where("report_group_id = ?", $reportGroupId);
            $result = $query->execute();

            if ($result[0]->getReportGroupId() == null) {
                return null;
            } else {
                return $result[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function getSelectedGroupField($reportId) {

        try {

            $query = Doctrine_Query::create()
                            ->from("SelectedGroupField")
                            ->where("report_id = ?", $reportId);
            $result = $query->execute();

            if ($result[0]->getReportId() == null) {
                return null;
            } else {
                return $result[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function getGroupField($groupFieldId) {

        try {

            $query = Doctrine_Query::create()
                            ->from("GroupField")
                            ->where("group_field_id = ?", $groupFieldId);
            $result = $query->execute();

            if ($result[0]->getGroupFieldId() == null) {
                return null;
            } else {
                return $result[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get runtime filter fields
     * @param integer $reportId
     * @return array of Doctring objects
     */
    public function getRuntimeFilterFields($reportGroupId, $type, $selectedFilterFieldIds) {

        try {

            $query = Doctrine_Query::create()
                            ->from("FilterField")
                            ->where("report_group_id = ?", $reportGroupId)
                            ->andWhere("type = ?", $type)
                            ->andWhereIn("filter_field_id", $selectedFilterFieldIds);

            $results = $query->execute();


            if ($results[0]->getReportGroupId() == null) {
                return null;
            } else {
                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function executeSql($sql) {

        $result = Doctrine_Manager::getInstance()->getCurrentConnection()->fetchAssoc($sql);

        return $result;
    }

    /**
     * Gets FilterField, given filter field id.
     * @param integer $filterFieldId
     * @return FilterField
     */
    public function getFilterFieldById($filterFieldId) {

        try {

            $filterField = Doctrine::getTable("FilterField")
                            ->find($filterFieldId);

            return $filterField;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Gets Project Activity, given activity id.
     * @param integer $activityId
     * @return ProjectActivity
     */
    public function getProjectActivityByActivityId($activityId) {

        try {

            $projectActivity = Doctrine::getTable("ProjectActivity")
                            ->find($activityId);

            return $projectActivity;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

}

