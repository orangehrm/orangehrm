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
class EditAttendanceRecordForm extends sfForm {

    private $attendanceService;

    public function configure() {
        $employeeId = $this->getOption('employeeId');
        $date = $this->getOption('date');
        $records = $this->getAttendanceService()->getAttendanceRecord($employeeId, $date);
        $totalRows = sizeOf($records);
        if ($records != null) {
//        $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME, PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME);
//        $actionableStates = $this->userObj->getActionableAttendanceStates($actions);



            for ($i = 1; $i <= $totalRows; $i++) {

                $this->setWidget('recordId_' . $i, new sfWidgetFormInputHidden());
                $this->setWidget('InOffset_' . $i, new sfWidgetFormInputHidden());
                $this->setWidget('OutOffset_' . $i, new sfWidgetFormInputHidden());
                $this->setWidget('punchInDate_' . $i, new sfWidgetFormInputText());
                $this->setWidget('punchInTime_' . $i, new sfWidgetFormInputText());
                $this->setWidget('inNote_' . $i, new sfWidgetFormInputText(array(), array('class' => 'inNote')));
                $this->setWidget('punchOutDate_' . $i, new sfWidgetFormInputText());
                $this->setWidget('punchOutTime_' . $i, new sfWidgetFormInputText());
                $this->setWidget('outNote_' . $i, new sfWidgetFormInputText(array(), array('class' => 'outNote')));
            }
            $this->widgetSchema->setNameFormat('attendance[%s]');
            for ($i = 1; $i <= $totalRows; $i++) {
                $this->setValidator('recordId_' . $i, new sfValidatorString());
                $this->setValidator('InOffset_' . $i, new sfValidatorString());
                $this->setValidator('OutOffset_' . $i, new sfValidatorString());
                $this->setValidator('punchInDate_' . $i, new sfValidatorDate(array('required' => __('Enter Punch In Date'))));
                $this->setValidator('punchInTime_' . $i, new sfValidatorDateTime(array('required' => __('Enter Punch In Time'))));
                $this->setValidator('inNote_' . $i, new sfValidatorString(array('required' => false, 'max_length' => 255)));
                $this->setValidator('punchOutDate_' . $i, new sfValidatorDate(array('required' => false)));
                $this->setValidator('punchOutTime_' . $i, new sfValidatorDateTime(array('required' => false)));
                $this->setValidator('outNote_' . $i, new sfValidatorString(array('required' => false, 'max_length' => 255)));
            }
            $i = 1;
            foreach ($records as $record) {


                if ($record->getPunchOutUserTime() == null) {

                    $this->setDefault('recordId_' . $i, $record->getId());
                    $this->setDefault('InOffset_' . $i, $record->getPunchInTimeOffset());
                    $this->setDefault('punchInDate_' . $i, date('Y-m-d', strtotime($record->getPunchInUserTime())));
                    $this->setDefault('punchInTime_' . $i, date('H:i', strtotime($record->getPunchInUserTime())));
                    $this->setDefault('inNote_' . $i, $record->getPunchInNote());
                    $this->setDefault('outNote_' . $i, "");
                    $this->setDefault('OutOffset_' . $i, 0);
                } else {

                    $this->setDefault('recordId_' . $i, $record->getId());
                    $this->setDefault('InOffset_' . $i, $record->getPunchInTimeOffset());
                    $this->setDefault('OutOffset_' . $i, $record->getPunchOutTimeOffset());
                    $this->setDefault('punchInDate_' . $i, date('Y-m-d', strtotime($record->getPunchInUserTime())));
                    $this->setDefault('punchInTime_' . $i, date('H:i', strtotime($record->getPunchInUserTime())));
                    $this->setDefault('inNote_' . $i, $record->getPunchInNote());
                    $this->setDefault('punchOutDate_' . $i, date('Y-m-d', strtotime($record->getPunchOutUserTime())));
                    $this->setDefault('punchOutTime_' . $i, date('H:i', strtotime($record->getPunchOutUserTime())));
                    $this->setDefault('outNote_' . $i, $record->getPunchOutNote());
                }

                $i++;
            }
        }
    }

    /**
     * Get the Timesheet Data Access Object
     * @return AttendanceService
     */
    public function getAttendanceService() {

        if (is_null($this->attendanceService)) {
            $this->attendanceService = new AttendanceService();
        }

        return $this->attendanceService;
    }

    /**
     * Set TimesheetData Access Object
     * @param AttendanceService $TimesheetDao
     * @return void
     */
    public function setAttedanceService(AttendanceService $attendanceService) {

        $this->attendanceService = $attendanceService;
    }

    public function save($totalRows, $form) {

        $this->form = $form;

        for ($i = 1; $i <= $totalRows; $i++) {


            $id = $this->form->getValue('recordId_' . $i);
            $inOffset = $this->form->getValue('InOffset_' . $i);
            $outOffset = $this->form->getValue('OutOffset_' . $i);
            $punchInDate = $this->form->getValue('punchInDate_' . $i);
            $punchInTime = $this->form->getValue('punchInTime_' . $i);

            $punchOutDate = $this->form->getValue('punchOutDate_' . $i);
            $punchOutTime = $this->form->getValue('punchOutTime_' . $i);


            $attendanceRecord = $this->getAttendanceService()->getAttendanceRecordById($id);
            $punchInDateTime = $punchInDate . " " . date('H:i', strtotime($punchInTime));
            $punchOutDateTime = $punchOutDate . " " . date('H:i', strtotime($punchOutTime));

            $attendanceRecord->setPunchInUserTime($punchInDateTime);



            $timeStampDiff = $inOffset * 3600 - date('Z');
            $attendanceRecord->setPunchInUtcTime(date('Y-m-d H:i', strtotime($punchInDateTime) - $inOffset * 3600));

            if ($this->form->getValue('punchOutDate_' . $i) == null) {

                $attendanceRecord->setPunchOutNote("");
                $attendanceRecord->setPunchOutUserTime(null);
                $attendanceRecord->setPunchOutUtcTime(null);
            } else {

                $attendanceRecord->setPunchOutUserTime($punchOutDateTime);
                $timeStampDiff = $outOffset * 3600 - date('Z');
                $attendanceRecord->setPunchOutUtcTime(date('Y-m-d H:i', strtotime($punchOutDateTime) - $outOffset * 3600));
            }
            $this->getAttendanceService()->savePunchRecord($attendanceRecord);
        }
    }

}

