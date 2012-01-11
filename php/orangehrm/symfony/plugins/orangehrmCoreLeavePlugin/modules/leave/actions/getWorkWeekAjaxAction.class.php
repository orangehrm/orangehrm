<?php

/*
 *
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
 * Displaying ApplyLeave UI and saving data
 *
 * @author Samantha Jayasinghe
 */
class getWorkWeekAjaxAction extends sfAction {

    /**
     * get Method for WorkWeek Service
     *
     * @return WorkWeekService $workWeekService
     */
    public function getWorkWeekService() {
        if (is_null($this->workWeekService)) {
            $this->workWeekService = new WorkWeekService();
            $this->workWeekService->setWorkWeekDao(new WorkWeekDao());
        }
        return $this->workWeekService;
    }

    /**
     * Set WorkWeekService
     * @param WorkWeekService $workWeekService
     */
    public function setWorkWeekService(WorkWeekService $workWeekService) {
        $this->workWeekService = $workWeekService;
    }
    
    public function execute( $request ){
        $workWeekList = $this->getWorkWeekService()->getWorkWeekList();

        $dates = "";
        foreach ($workWeekList as $workWeek) {
            $htype = $workWeek->getLength();
            $day = (int) $workWeek->getDay();
            if ($htype == 8) {
                $dates .= "[" . ( $day ) . ", 'w'],";
            } elseif ($htype == 4) {
                $dates .= "[" . ( $day ) . ", 'h'],";
            }
        }

        $dates = rtrim($dates, ",");

        echo "[";
        echo $dates;
        echo "]";
        exit;
    }
    
    

}

?>
