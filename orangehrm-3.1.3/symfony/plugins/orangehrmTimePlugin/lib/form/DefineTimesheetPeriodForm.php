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
class DefineTimesheetPeriodForm extends sfForm {

    private $timesheetPeriodService;

    public function configure() {

        $dates = array('' => "-- " . __('Select') . " --", '1' => __('Monday'), '2' => __('Tuesday'), '3' => __('Wednesday'), '4' => __('Thursday'), '5' => __('Friday'), '6' => __('Saturday'), '7' => __('Sunday'));


        $this->setWidgets(array(
            'startingDays' => new sfWidgetFormSelect(array('choices' => $dates)),
        ));

        $this->widgetSchema->setNameFormat('time[%s]');

        $this->widgetSchema['startingDays']->setAttribute('style', 'width:150px');

        $this->setValidators(array(
            'startingDays' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($dates))),
        ));
    }

    public function save() {
        $startDay = $this->getValue('startingDays');
        $this->getTimesheetPeriodService()->setTimesheetPeriod($startDay);
    }

    public function getTimesheetPeriodService() {

        if (is_null($this->timesheetPeriodService)) {

            $this->timesheetPeriodService = new TimesheetPeriodService();
        }
        return $this->timesheetPeriodService;
    }

}

?>
