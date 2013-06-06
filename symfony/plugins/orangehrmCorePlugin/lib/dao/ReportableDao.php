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
    public function getSelectedFilterFields($reportId, $order = false) {

        try {

            $query = Doctrine_Query::create()
                            ->select("filter_field_id")
                            ->from("SelectedFilterField")
                            ->where("report_id = ?", $reportId);

            if ($order) {
                $query->orderBy("filter_field_order");
            }

            $results = $query->execute()->getData();

            return $results;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function getSelectedFilterFieldNames($reportId, $order = false) {

        try {
            $query = Doctrine_Query::create()
                            ->select("s.*, f.name")
                            ->from("SelectedFilterField s")
                            ->leftJoin("s.FilterField f")
                            ->where("s.report_id = ?", $reportId);

            if ($order) {
                $query->orderBy("s.filter_field_order");
            }

            $results = $query->execute();

            return $results;
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
     * Get all selected display fields for the given report id
     * @param integer $reportId
     * @return array of Doctring objects
     */
    public function getSelectedDisplayFieldGroups($reportId) {

        try {

            $query = Doctrine_Query::create()
                            ->select("display_field_group_id")
                            ->from("SelectedDisplayFieldGroup")
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
    public function getMetaDisplayFields($reportGroupId) {

        try {

            $query = Doctrine_Query::create()
                            ->from("DisplayField")
                            ->where("report_group_id = ?", $reportGroupId)
                            ->andWhere('is_meta = ?', 1);
            $results = $query->execute();

            return $results;

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

    public function getDisplayFieldGroupsForReportGroup($reportGroupId) {
        try {

            $query = Doctrine_Query::create()
                            ->from("DisplayFieldGroup")
                            ->where("report_group_id = ?", $reportGroupId);
            $result = $query->execute();


            return $result;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Gets all display fields for given report group
     */
    public function getDisplayFieldsForReportGroup($reportGroupId) {
        try {

            $query = Doctrine_Query::create()
                            ->from("DisplayField f")
                            ->leftjoin("f.DisplayFieldGroup g")
                            ->where("f.report_group_id = ?", $reportGroupId);
            $result = $query->execute();


            return $result;
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

    public function getFilterFieldsForReportGroup($reportGroupId) {
        try {

            $query = Doctrine_Query::create()
                            ->from("FilterField")
                            ->where("report_group_id = ?", $reportGroupId);

            $results = $query->execute();

            return $results;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }
    
    public function getRequiredFilterFieldsForReportGroup($reportGroupId) {
        try {

            $query = Doctrine_Query::create()
                            ->from("FilterField")
                            ->where("report_group_id = ?", $reportGroupId)
                            ->andWhere("required = 'true'");

            $results = $query->execute();

            return $results;
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

    /**
     * Gets all reports that are of given type
     * @param string $type
     * @return Doctrine_Collection ( Report[])
     */
    public function getAllPredefinedReports($type, $sortField = 'name', $sortOrder = 'ASC') {
        try {
            $query = Doctrine_Query::create()
                            ->from("Report")
                            ->where("type = ?", $type)
                            ->orderBy("$sortField $sortOrder");

            $reports = $query->execute();

            if ($reports[0]->getReportId() == null) {
                return null;
            }

            return $reports;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Gets selected filter fields given the type of the filter field
     * @param integer $reportId
     * @param string $type
     * @param boolean $order
     * @return Doctrine_Collection ( SelectedFilterField[])
     */
    public function getSelectedFilterFieldsByType($reportId, $type, $order = false) {

        try {

            $query = Doctrine_Query::create()
                            ->select("filter_field_id")
                            ->from("SelectedFilterField")
                            ->where("report_id = ?", $reportId)
                            ->andWhere("type = ?", $type);

            if ($order) {
                $query->orderBy("filter_field_order");
            }

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
     * Gets predefined reports give part string of the report name
     * @param string $type
     * @param string $searchString
     * @return Doctrine_Collection ( Report[])
     */
    public function getPredefinedReportsByPartName($type, $searchString, $noOfRecords = NULL, $offset = NULL, $sortField = 'name', $sortOrder = 'ASC') {

        $searchString = '%' . $searchString . '%';

        try {

            $query = Doctrine_Query::create()
                            ->from('Report')
                            ->where('type = ?', $type)
                            ->andWhere('name LIKE ?', array('%' . $searchString . '%'))
                            ->orderBy("$sortField $sortOrder");

            if (!is_null($offset)) {
                $query->offset($offset);
            }

            if (!is_null($noOfRecords)) {
                $query->limit($noOfRecords);
            }

            $reports = $query->execute();

            if ($reports[0]->getReportId() == null) {
                return null;
            }

            return $reports;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Gets predefined reports give part string of the report name
     * @param string $type
     * @param string $searchString
     * @return Doctrine_Collection ( Report[])
     */
    public function getPredefinedReportCountByPartName($type, $searchString) {

        $searchString = '%' . $searchString . '%';

        try {

            $query = Doctrine_Query::create()
                            ->from('Report r')
                            ->where('r.type = ?', $type)
                            ->andWhere('r.name LIKE ?', array('%' . $searchString . '%'));


            $count = $query->count();

            return $count;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /*
     * Delete reports given report ids
     * @param integer[] $reportIds
     * @return integer
     */

    public function deleteReports($reportIds) {
        try {
            $query = Doctrine_Query::create()
                            ->delete()
                            ->from('Report')
                            ->whereIn('report_id', $reportIds);
            $results = $query->execute();
            return $results;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function getPredefinedReports($type, $noOfRecords = NULL, $offset = NULL, $sortField = 'name', $sortOrder = 'ASC') {

        try {
            $query = Doctrine_Query::create()
                            ->from("Report")
                            ->where("type = ?", $type);

            if (!is_null($offset)) {
                $query->offset($offset);
            }

            if (!is_null($noOfRecords)) {
                $query->limit($noOfRecords);
            }

            $query->orderBy("$sortField $sortOrder");
            $reports = $query->execute();

            if ($reports[0]->getReportId() == null) {
                return null;
            }

            return $reports;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function getPredefinedReportsCount($type) {

        try {
            $query = Doctrine_Query::create()
                            ->from("Report")
                            ->where("type = ?", $type);

            $results = $query->execute()->count();
            return $results;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Saves a report
     * @param <type> $reportName
     * @param <type> $reportGroupId
     * @param <type> $useFilterField
     * @param <type> $type
     * @return Report
     */
    public function saveReport($reportName, $reportGroupId, $useFilterField, $type) {
        try {

            $report = new Report();
            $report->setName($reportName);
            $report->setReportGroupId($reportGroupId);
            $report->setUseFilterField($useFilterField);
            $report->setType($type);
            $report->save();

            return $report;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function updateReportName($reportId, $name) {
        try {
            $query = Doctrine_Query::create()
                            ->update("Report")
                            ->set("name", '?', $name)
                            ->where("report_id = ?", $reportId);

            $results = $query->execute();
            return $results;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function saveSelectedFilterField($reportId, $filterFieldId, $filterFieldOrder, $value1, $value2, $whereCondition, $type) {
        try {

            $selectedFilterField = Doctrine::getTable("SelectedFilterField")->find(array($reportId, $filterFieldId));

            if ($selectedFilterField == null) {
                $selectedFilterField = new SelectedFilterField();
                $selectedFilterField->setReportId($reportId);
                $selectedFilterField->setFilterFieldId($filterFieldId);
            }

            $selectedFilterField->setFilterFieldOrder($filterFieldOrder);
            $selectedFilterField->setValue1($value1);
            $selectedFilterField->setValue2($value2);
            $selectedFilterField->setWhereCondition($whereCondition);
            $selectedFilterField->setType($type);

            $selectedFilterField->save();
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function saveSelectedDispalyField($displayFieldId, $reportId) {

        try {

            $selectedDisplayField = new SelectedDisplayField();

            $selectedDisplayField->setDisplayFieldId($displayFieldId);
            $selectedDisplayField->setReportId($reportId);

            $selectedDisplayField->save();
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function saveSelectedDisplayFieldGroup($displayFieldGroupId, $reportId) {

        try {

            $selectedDisplayFieldGroup = new SelectedDisplayFieldGroup();

            $selectedDisplayFieldGroup->setDisplayFieldGroupId($displayFieldGroupId);
            $selectedDisplayFieldGroup->setReportId($reportId);

            $selectedDisplayFieldGroup->save();
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /*
     * Gets a filter field by given name
     * @param  string $name
     * @return FilterField
     */

    public function getFilterFieldByName($name) {
        try {

            $query = Doctrine_Query::create()
                            ->from("FilterField")
                            ->where("name = ?", $name);

            $filterField = $query->execute();

            if ($filterField[0]->getFilterFieldId() == null) {
                return null;
            } else {
                return $filterField[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function removeSelectedFilterFields($reportId) {
        try {
            $query = Doctrine_Query::create()
                            ->delete()
                            ->from('SelectedFilterField')
                            ->where('report_id =  ?', $reportId);
            $results = $query->execute();
            return $results;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function removeSelectedDisplayFieldGroups($reportId) {
        try {
            $query = Doctrine_Query::create()
                            ->delete()
                            ->from('SelectedDisplayFieldGroup')
                            ->where('report_id =  ?', $reportId);
            $results = $query->execute();
            return $results;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function removeSelectedDisplayFields($reportId) {
        try {
            $query = Doctrine_Query::create()
                            ->delete()
                            ->from('SelectedDisplayField')
                            ->where('report_id =  ?', $reportId);
            $results = $query->execute();
            return $results;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     *
     * @param <type> $columns
     * @return DisplayField 
     */
    public function saveCustomDisplayField($columns) {

        try {

            if (array_key_exists('displayFieldId', $columns)) {
                $displayField = Doctrine::getTable("DisplayField")->find($columns['displayFieldId']);
                $displayField->setLabel($columns['label']);
            } else {
                $displayField = new DisplayField();
                $displayField->setReportGroupId($columns['reportGroupId']);
                $displayField->setName($columns['name']);
                $displayField->setLabel($columns['label']);
                $displayField->setFieldAlias($columns['fieldAlias']);
                $displayField->setIsSortable($columns['isSortable']);
                $displayField->setSortOrder($columns['sortOrder']);
                $displayField->setSortField($columns['sortField']);
                $displayField->setElementType($columns['elementType']);
                $displayField->setElementProperty($columns['elementProperty']);
                $displayField->setWidth($columns['width']);
                $displayField->setIsExportable($columns['isExportable']);
                $displayField->setTextAlignmentStyle($columns['textAlignmentStyle']);
                $displayField->setIsValueList($columns['isValueList']);
                $displayField->setDisplayFieldGroupId($columns['displayFieldGroupId']);
                $displayField->setDefaultValue($columns['defaultValue']);
                $displayField->setIsEncrypted($columns['isEncrypted']);
            }

            $displayField->save();

            return $displayField;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     *
     * @param <type> $customDisplayFieldName
     * @return <type>
     */
    public function deleteCustomDisplayField($customDisplayFieldName) {

        try {
            $q = Doctrine_Query::create()
                            ->delete('DisplayField')
                            ->where('name = ?', $customDisplayFieldName);

            $numDeleted = $q->execute();

            if ($numDeleted > 0) {
                return true;
            }

            return false;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     *
     * @param <type> $name
     * @return <type> 
     */
    public function getDisplayFieldByName($name) {
        try {

            $query = Doctrine_Query::create()
                            ->from("DisplayField")
                            ->where("name = ?", $name);

            $displayField = $query->execute();

            if ($displayField[0]->getDisplayFieldId() == null) {
                return null;
            } else {
                return $displayField;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

}

