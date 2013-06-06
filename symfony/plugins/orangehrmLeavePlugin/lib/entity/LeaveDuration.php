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
 *
 */

/**
 * Description of LeaveDuration
 */
class LeaveDuration {
    const FULL_DAY = 'full_day';
    const HALF_DAY = 'half_day';
    const SPECIFY_TIME = 'specify_time';
    
    const HALF_DAY_AM = 'AM';
    const HALF_DAY_PM = 'PM';
    
    protected $type;
    protected $amPm;
    protected $fromTime;
    protected $toTime;
    
    function __construct($type = null, $amPm = null, $fromTime = null, $toTime = null) {
        $this->type = $type;
        $this->amPm = $amPm;
        $this->fromTime = $fromTime;
        $this->toTime = $toTime;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getAmPm() {
        return $this->amPm;
    }

    public function setAmPm($amPm) {
        $this->amPm = $amPm;
    }

    public function getFromTime() {
        return $this->fromTime;
    }

    public function setFromTime($fromTime) {
        $this->fromTime = $fromTime;
    }

    public function getToTime() {
        return $this->toTime;
    }

    public function setToTime($toTime) {
        $this->toTime = $toTime;
    }


}
