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
class WorkShiftService extends BaseService {
    // Default if default is not defined in database.

    const DEFAULT_WORKSHIFT_START_TIME = '09:00';
    const DEFAULT_WORKSHIFT_END_TIME = '17:00';

    private $workShiftDao;
    protected $configService;

    /**
     * Construct
     */
    public function __construct() {
        $this->workShiftDao = new WorkShiftDao();
    }

    /**
     *
     * @return <type>
     */
    public function getWorkShiftDao() {
        return $this->workShiftDao;
    }

    /**
     * Get ConfigService instance
     * @return ConfigService
     */
    public function getConfigService() {
        if (!($this->configService instanceof ConfigService)) {
            $this->configService = new ConfigService();
        }
        return $this->configService;
    }

    /**
     * Set ConfigService
     * @param ConfigService $configService
     */
    public function setConfigService(ConfigService $configService) {
        $this->configService = $configService;
    }

    /**
     *
     * @param WorkShiftDao $workShiftDao 
     */
    public function setWorkShiftDao(WorkShiftDao $workShiftDao) {
        $this->workShiftDao = $workShiftDao;
    }

    public function getWorkShiftList() {
        return $this->workShiftDao->getWorkShiftList();
    }

    public function getWorkShiftById($workShiftId) {
        return $this->workShiftDao->getWorkShiftById($workShiftId);
    }

    public function getWorkShiftEmployeeListById($workShiftId) {
        return $this->workShiftDao->getWorkShiftEmployeeListById($workShiftId);
    }

    public function getWorkShiftEmployeeNameListById($workShiftId) {
        return $this->workShiftDao->getWorkShiftEmployeeNameListById($workShiftId);
    }

    public function getWorkShiftEmployeeList() {
        return $this->workShiftDao->getWorkShiftEmployeeList();
    }

    public function updateWorkShift($workShift) {
        return $this->workShiftDao->updateWorkShift($workShift);
    }

    public function getWorkShiftEmployeeIdList() {
        return $this->workShiftDao->getWorkShiftEmployeeIdList();
    }

    public function saveEmployeeWorkShiftCollection(Doctrine_Collection $empWorkShiftCollection) {
        $this->workShiftDao->saveEmployeeWorkShiftCollection($empWorkShiftCollection);
    }

    /**
     * Return the default start time and end time for workshifts
     * 
     * NOTE: If configured start/end time in hs_hr_config is not valid,
     * it defaults to 09:00 to 17:00
     * 
     * @return Array with indexes 'start_time' and 'end_time'
     */
    public function getWorkShiftDefaultStartAndEndTime() {
        $startTime = $this->getConfigService()->getDefaultWorkShiftStartTime();
        $endTime = $this->getConfigService()->getDefaultWorkShiftEndTime();

        $valid = false;

        /* Validate start/end time */
        $timePattern = "/(2[0-3]|[01][0-9]):[0-5][0-9]/";

        if (preg_match($timePattern, $startTime) && preg_match($timePattern, $endTime)) {
            list($startHour, $startMin) = explode(':', $startTime);
            list($endHour, $endMin) = explode(':', $endTime);

            if (intVal($endHour) > intVal($startHour)) {
                $valid = true;
            } else if ((intVal($endHour) == intVal($startHour)) && (intVal($endMin) > intVal($startMin))) {
                $valid = true;
            }
        }

        if (!$valid) {
            $startTime = self::DEFAULT_WORKSHIFT_START_TIME;
            $endTime = self::DEFAULT_WORKSHIFT_END_TIME;
        }

        return array(
            'start_time' => $startTime,
            'end_time' => $endTime
        );
    }

}

