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
class EditAttendanceRecordRowForm extends sfForm {
    
     private $attendanceService;

    public function configure() {
        $this->setWidgets(array(
            'punchIn' => new sfWidgetFormInputText(array(), array()),
            'inNote' => new sfWidgetFormInputText(array(), array()),
            'punchOut' => new sfWidgetFormInputText(array(), array()),
            'outNote' => new sfWidgetFormInputText(array(), array()),
        ));


        $this->widgetSchema->setNameFormat('attendance[%s]');

        $this->setValidators(array(
            'punchIn' => new sfValidatorDateTime(array(), array('required' => __('Enter Punch In Time'))),
            'inNote' => new sfValidatorDateTime(),
            'punchOut' => new sfValidatorDateTime(array('required' => __('Enter Punch Out Time'))),
            'outNote' => new sfValidatorString(),
        ));
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
    public function setTimesheetDao(AttendanceService $attendanceService) {

        $this->attendanceService = $attendanceService;
    }


}

