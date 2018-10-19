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
class TimesheetForm extends sfForm {

// Timesheet Data Access Object
    private $timesheetDao;
    private $timesheetService;
    private $timesheetPeriodService;

    public function configure() {

        $startDate = $this->getOption('date');
        $noOfDays = $this->getOption('noOfDays');
        $values = array('noOfDays' => $noOfDays);
        $tempRows = $this->getTimesheet($startDate, $this->getOption('employeeId'), $this->getOption('timesheetId'));

        $numberOfRows = $tempRows;
        if ($numberOfRows == null) {

            $timesheetRows = new sfForm();

            $emptyRowForm = new TimesheetRowForm(array(), $values);

            $emptyRowForm->setDefault('projectName', __('Type for hints').'...');
            $emptyRowForm->setDefault('projectActivity', '-- ' . __('Select') . ' --');
            for ($i = 0; $i < $noOfDays; $i++) {
                $emptyRowForm->setDefault($i, '');
            }
            $timesheetRows->embedForm(0, $emptyRowForm);

            $this->embedForm('initialRows', $timesheetRows);
        } else {

            $totalRows = sizeOf($numberOfRows);

            $keysArray = array_keys($numberOfRows[0]['timesheetItems']);

            $timesheetRows = new sfForm();
            $count = 0;

            for ($i = 0; $i < $totalRows; $i++) {
                $rowForm = new TimesheetRowForm(array(), $values);

                for ($j = 0; $j < $noOfDays; $j++) {
                    if ($numberOfRows[$i]['timesheetItems'][$keysArray[$j]]->getTimesheetId() > 0) {
                        $project = $numberOfRows[$i]['timesheetItems'][$keysArray[$j]]->getProject();
                        continue;
                    }
                }

                $activities = $this->getTimesheetService()->getProjectActivityListByPorjectId($project->getProjectId(), true);
                $activityArray = null;
                
                foreach ($activities as $activity) {
                    $activityId = $activity['activityId'];
                    $activityName = $activity['name'];
                    $activityIsDeleted = $activity['is_deleted'];
                    if ($activityIsDeleted != 1) {
                        $activityArray[$activityId] = $activityName;
                    }
                    if($activityId == $numberOfRows[$i]['activityId'] && $activityIsDeleted == 1){
                        $activityArray[$activityId] = $activityName;
                    }
                }

                $rowForm->setWidget('projectActivityName', new sfWidgetFormSelect(array('choices' => $activityArray), array('style' => 'width:225px', 'class' => 'projectActivity')));
                $rowForm->setDefault('projectName', $numberOfRows[$i]['projectName']);
                $rowForm->setDefault('projectActivityId', $numberOfRows[$i]['activityId']);
                $rowForm->setDefault('projectId', $numberOfRows[$i]['projectId']);
                $rowForm->setDefault('projectActivityName', $numberOfRows[$i]['activityId']);

                for ($j = 0; $j < $noOfDays; $j++) {
                    $rowForm->setDefault('TimesheetItemId' . $j, $numberOfRows[$i]['timesheetItems'][$keysArray[$j]]['timesheetItemId']);
                    $rowForm->setDefault($j, $numberOfRows[$i]['timesheetItems'][$keysArray[$j]]->getConvertTime());
                }
                $timesheetRows->embedForm($count, $rowForm);
                $count++;
            }

            $this->embedForm('initialRows', $timesheetRows);
        }
    }

    /**
     * Get the Timesheet Data Access Object
     * @return TimesheetDao
     */
    public function getTimesheetDao() {

        if (is_null($this->timesheetDao)) {
            $this->timesheetDao = new TimesheetDao();
        }

        return $this->timesheetDao;
    }

    /**
     * Set TimesheetData Access Object
     * @param TimesheetDao $TimesheetDao
     * @return void
     */
    public function setTimesheetDao(TimesheetDao $timesheetDao) {

        $this->timesheetDao = $timesheetDao;
    }

    /**
     * Get the Timesheet Data Access Object
     * @return TimesheetService
     */
    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {
            $this->timesheetService = new TimesheetService();
        }



        return $this->timesheetService;
    }

    /**
     *
     * @return TimesheetPeriodService
     */
    public function getTimesheetPeriodService() {

        if (is_null($this->timesheetPeriodService)) {

            $this->timesheetPeriodService = new TimesheetPeriodService();
        }

        return $this->timesheetPeriodService;
    }

    /**
     * Set TimesheetData Access Object
     * @param TimesheetService $TimesheetService
     * @return void
     */
    public function setTimesheetService(TimesheetService $timesheetService) {


        $this->timesheetService = $timesheetService;
    }

    public function getTimesheet($date, $employeeId, $timesheetId) {

        $timesheetItems = $this->getTimesheetDao()->getTimesheetItem($timesheetId, $employeeId);

        $startDate = $date;

        if (!empty($timesheetItems)) {

            $timesheet = $this->getTimesheetDao()->getTimesheetByStartDateAndEmployeeId($startDate, $employeeId);
            $endDate = $timesheet->getEndDate();
            $dates = $this->getDatesOfTheTimesheetPeriod($startDate, $endDate);

            $temp = current($timesheetItems);
            $projectId = $temp["projectId"];
            $activityId = $temp["activityId"];


            $i = 0;

            foreach ($timesheetItems as $timesheetItem) {

                if (($timesheetItem["projectId"] == $projectId) && ($timesheetItem["activityId"] == $activityId)) {

                    $rows[$i][] = $timesheetItem;
                } else {

                    $projectId = $timesheetItem["projectId"];
                    $activityId = $timesheetItem["activityId"];
                    $i++;
                    $rows[$i][] = $timesheetItem;
                }
            }

            foreach ($rows as $row) {
	
                $rowArray['projectId'] = $row[0]["projectId"];
                $rowArray['projectName'] = $row[0]->getProject()->getCustomer()->getName() . " - ##" . $row[0]->getProject()->getName();
                $rowArray['isProjectDeleted'] = $row[0]->getProject()->getIsDeleted();
                $rowArray['activityId'] = $row[0]["activityId"];
                $rowArray['activityName'] = $row[0]->getProjectActivity()->getName();
                $rowArray['isActivityDeleted'] = $row[0]->getProjectActivity()->getIsDeleted();

                foreach ($dates as $date) {

                    $current = current($row);

                    if ($date == $current["date"]) {

                        $timesheetItemsArray[$date] = array_shift($row);
                        continue;
                    }

                    $newTimesheetItem = new TimesheetItem();
                    $newTimesheetItem->setDate($date);
                    $timesheetItemsArray[$date] = $newTimesheetItem;
                }
                $rowArray['timesheetItems'] = $timesheetItemsArray;
                $printableArray[] = $rowArray;
            }

            return $printableArray;
        } else {

            return null;
        }
    }

    public function getDatesOfTheTimesheetPeriod($startDate, $endDate) {

        $clientTimeZoneOffset = sfContext::getInstance()->getUser()->getUserTimeZoneOffset();
        date_default_timezone_set($this->getLocalTimezone($clientTimeZoneOffset));

        if ($startDate < $endDate) {
            $dates_range[] = $startDate;

            $startDate = strtotime($startDate);
            $endDate = strtotime($endDate);


            while (date('Y-m-d', $startDate) != date('Y-m-d', $endDate)) {
                $startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate) + 1, date("Y", $startDate));
                $dates_range[] = date('Y-m-d', $startDate);
            }
        }
        return $dates_range;
    }

    public function addInitialRows($numberOfRows) {

        $timesheetRows = new sfForm();
        $count = 0;

        for ($i = 0; $i <= $numberOfRows; $i++) {
            $rowForm = new TimesheetForm();
            $timesheetRows->embedForm($count, $rowForm);
            $count++;
        }

        $this->embedForm('initialRows', $timesheetRows);
    }

    public function addRow($num, $values) {

        $rowForm = new TimesheetRowForm(array(), $values);
        $this->embeddedForms['initialRows']->embedForm($num, $rowForm);
        $this->embedForm('initialRows', $this->embeddedForms['initialRows']);
    }

    public function getProjectListAsJson() {

        $jsonArray = array();
        $projectList = $this->getTimesheetService()->getProjectNameList();

        foreach ($projectList as $project) {
            $jsonArray[] = array('name' => $project['customerName'] . " - ##" . $project['projectName'], 'id' => $project['projectId']);
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    public function getProjectListAsJsonForValidation() {

        $jsonArray = array();
        $projectList = $this->getTimesheetService()->getProjectNameList(false);

        foreach ($projectList as $project) {
            $jsonArray[] = array('name' => $project['customerName'] . " - ##"  . $project['projectName'], 'id' => $project['projectId']);
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    public function getLocalTimezone($clientTimeZoneOffset) {


        $offset = $clientTimeZoneOffset;
        $zonelist =
                array
                    (
                    'Kwajalein' => -12.00,
                    'Pacific/Midway' => -11.00,
                    'Pacific/Honolulu' => -10.00,
                    'America/Anchorage' => -9.00,
                    'America/Los_Angeles' => -8.00,
                    'America/Denver' => -7.00,
                    'America/Tegucigalpa' => -6.00,
                    'America/New_York' => -5.00,
                    'America/Caracas' => -4.50,
                    'America/Halifax' => -4.00,
                    'America/St_Johns' => -3.50,
                    'America/Argentina/Buenos_Aires' => -3.00,
                    'America/Sao_Paulo' => -3.00,
                    'Atlantic/South_Georgia' => -2.00,
                    'Atlantic/Azores' => -1.00,
                    'Europe/Dublin' => 0,
                    'Europe/Belgrade' => 1.00,
                    'Europe/Minsk' => 2.00,
                    'Asia/Kuwait' => 3.00,
                    'Asia/Tehran' => 3.50,
                    'Asia/Muscat' => 4.00,
                    'Asia/Yekaterinburg' => 5.00,
                    'Asia/Kolkata' => 5.50,
                    'Asia/Katmandu' => 5.45,
                    'Asia/Dhaka' => 6.00,
                    'Asia/Rangoon' => 6.50,
                    'Asia/Krasnoyarsk' => 7.00,
                    'Asia/Brunei' => 8.00,
                    'Asia/Seoul' => 9.00,
                    'Australia/Darwin' => 9.50,
                    'Australia/Canberra' => 10.00,
                    'Asia/Magadan' => 11.00,
                    'Pacific/Fiji' => 12.00,
                    'Pacific/Tongatapu' => 13.00
        );
        $index = array_keys($zonelist, $offset);
        if (sizeof($index) != 1)
            return false;
        return $index[0];
    }

}

?>
