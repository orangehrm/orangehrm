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

require_once ROOT_PATH . '/lib/models/hrfunct/JobTitleHistory.php';
require_once ROOT_PATH . '/lib/models/hrfunct/LocationHistory.php';
require_once ROOT_PATH . '/lib/models/hrfunct/SubDivisionHistory.php';

class EXTRACTOR_EmpJobHistory {

	public function parseAddData($postArr) {

        $type = $postArr['cmbHistoryItemType'];
        $startDate = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtEmpHistoryItemFrom']);
        $endDate = LocaleUtil::getInstance()->convertToStandardDateFormat($postArr['txtEmpHistoryItemTo']);
        $empNum = trim($postArr['txtEmpID']);

        $history = null;

        switch ($type) {
            case 'JOB':
                $history = new JobTitleHistory();
                $code = $postArr['cmbJobTitleHistory'];
                break;

            case 'SUB':
                $history = new SubDivisionHistory();
                $code = $postArr['cmbHistorySubDiv'];
                break;

            case 'LOC':
                $history = new LocationHistory();
                $code = $postArr['cmbLocationHistory'];
                break;
        }

        $history->setEmpNumber($empNum);
        $history->setCode($code);
        $history->setStartDate($startDate);
        $history->setEndDate($endDate);

		return $history;
	}

    public function parseEditData($postArr) {

        $historyItems = array();

        $empNum = trim($postArr['txtEmpID']);

        // Get job title history
        if (isset($postArr['jobTitleHisId'])) {
            $jobTitleIds = $postArr['jobTitleHisId'];
            $jobTitleCodes = $postArr['jobTitleHisCode'];
            $jobTitleFromDates = $postArr['jobTitleHisFromDate'];
            $jobTitleToDates = $postArr['jobTitleHisToDate'];

            for ($i=0; $i<count($jobTitleIds); $i++) {
                $history = new JobTitleHistory();

                $id = $jobTitleIds[$i];
                $code = $jobTitleCodes[$i];
                $startDate = LocaleUtil::getInstance()->convertToStandardDateFormat($jobTitleFromDates[$i]);
                $endDate = LocaleUtil::getInstance()->convertToStandardDateFormat($jobTitleToDates[$i]);

                $history->setId($id);
                $history->setCode($code);
                $history->setEmpNumber($empNum);
                $history->setStartDate($startDate);
                $history->setEndDate($endDate);

                $historyItems[] = $history;
            }
        }

        // Get sub division history
        if (isset($postArr['subDivHisId'])) {
            $subDivIds = $postArr['subDivHisId'];
            $subDivCodes = $postArr['subDivHisCode'];
            $subDivFromDates = $postArr['subDivHisFromDate'];
            $subDivToDates = $postArr['subDivHisToDate'];

            for ($i=0; $i<count($subDivIds); $i++) {
                $history = new SubDivisionHistory();

                $id = $subDivIds[$i];
                $code = $subDivCodes[$i];
                $startDate = LocaleUtil::getInstance()->convertToStandardDateFormat($subDivFromDates[$i]);
                $endDate = LocaleUtil::getInstance()->convertToStandardDateFormat($subDivToDates[$i]);

                $history->setId($id);
                $history->setCode($code);
                $history->setEmpNumber($empNum);
                $history->setStartDate($startDate);
                $history->setEndDate($endDate);

                $historyItems[] = $history;
            }

        }

        // Get location history
        if (isset($postArr['locHisId'])) {

            $locIds = $postArr['locHisId'];
            $locCodes = $postArr['locHisCode'];
            $locFromDates = $postArr['locHisFromDate'];
            $locToDates = $postArr['locHisToDate'];

            for ($i=0; $i<count($locIds); $i++) {
                $history = new LocationHistory();

                $id = $locIds[$i];
                $startDate = LocaleUtil::getInstance()->convertToStandardDateFormat($locFromDates[$i]);
                $endDate = LocaleUtil::getInstance()->convertToStandardDateFormat($locToDates[$i]);

                $history->setId($id);
                $code = $locCodes[$i];
                $history->setCode($code);
                $history->setEmpNumber($empNum);
                $history->setStartDate($startDate);
                $history->setEndDate($endDate);

                $historyItems[] = $history;
            }

        }

        return $historyItems;
    }

}
?>
