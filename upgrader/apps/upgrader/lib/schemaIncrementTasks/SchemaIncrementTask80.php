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

/**
 * Class SchemaIncrementTask80
 */
class SchemaIncrementTask80 extends SchemaIncrementTask
{

    public function loadSql()
    {
        $sql[] = "INSERT INTO `hs_hr_config` (`key` ,`value`) VALUES ('help.url',  'https://opensourcehelp.orangehrm.com');";

        $sql[] = "INSERT INTO `hs_hr_config` (`key` ,`value`) VALUES ('help.processorClass',  'ZendeskHelpProcessor');";

        $sql[] = "UPDATE `hs_hr_config` SET `value` = '4.8' WHERE `hs_hr_config`.`key` = 'instance.version';";

        $sql[] = "UPDATE `hs_hr_config` SET `value` = '" . $this->incrementNumber . "' WHERE `hs_hr_config`.`key` = 'instance.increment_number';";

        $sql[] = "INSERT INTO `ohrm_i18n_group` (`name`,`title`) VALUES ('help','Help')";

        $sql[] =  "INSERT INTO ohrm_screen (`id`, `name`, `module_id`, `action_url`) VALUES
                (103, 'Dashboard', 10, 'index'),
                (104, 'Save KPI', 11, 'saveKpi'),
                (105, 'Search KPI', 11, 'searchKpi'),
                (106, 'My Performance Reviews', 11, 'myPerformanceReview'),
                (107, 'Save Review', 11, 'saveReview'),
                (108, 'Review Evaluate', 11, 'reviewEvaluate'),
                (109, 'Review Evaluate By Admin', 11, 'reviewEvaluateByAdmin'),
                (110, 'Search Evaluate Performance Review', 11, 'searchEvaluatePerformancReview'),
                (111, 'Search Performance Review', 11, 'searchPerformancReview'),
                (112, 'Add Performance Tracker', 11, 'addPerformanceTracker'),
                (113, 'View Employee Performance Tracker List', 11, 'viewEmployeePerformanceTrackerList'),
                (114, 'View My Performance Tracker List', 11, 'viewMyPerformanceTrackerList'),
                (115, 'Add Performance Tracker Log', 11, 'addPerformanceTrackerLog'),
                (116, 'Directory', 12, 'viewDirectory'),
                (117, 'Manage OpenId', 2, 'openIdProvider'),
                (118, 'Register OAuth Client', 2, 'registerOAuthClient'),
                (119, 'Purge Employee', 13, 'purgeEmployee'),
                (120, 'Purge Candidate Records', 13, 'purgeCandidateData'),
                (121, 'Access Employee Records', 13, 'accessEmployeeData'),
                (122, 'Addons', 14, 'ohrmAddons'),
                (123, 'Buzz', 15, 'viewBuzz'),
                (124, 'Language Packages', 2, 'languagePackage'),
                (125, 'Language Customization', 2, 'languageCustomization'),
                (126, 'Save Language Customization', 2, 'saveLanguageCustomization'),
                (127, 'Export Language Package', 2, 'exportLanguagePackage'),
                (129, 'Save Subscriber', 2, 'saveSubscriber'),
                (130, 'Employee', 3, 'viewEmployee'),
                (131, 'Personal Details', 3, 'viewPersonalDetails'),
                (132, 'Contact Details', 3, 'contactDetails'),
                (133, 'Emergency Contacts', 3, 'viewEmergencyContacts'),
                (134, 'Dependents', 3, 'viewDependents'),
                (135, 'Immigration', 3, 'viewImmigration'),
                (136, 'Job Details', 3, 'viewJobDetails'),
                (137, 'Salary List', 3, 'viewSalaryList'),
                (138, 'Us Tax Exemptions', 3, 'viewUsTaxExemptions'),
                (139, 'Report To Details', 3, 'viewReportToDetails'),
                (140, 'Qualifications', 3, 'viewQualifications'),
                (141, 'Memberships', 3, 'viewMemberships'),
                (142, 'Timesheet', 5, 'viewTimesheet'),
                (143, 'Punch Out', 6, 'punchOut'),
                (144, 'Edit Attendance Record', 6, 'editAttendanceRecord'),
                (145, 'proxy PunchIn PunchOut', 6, 'proxyPunchInPunchOut'),
                (146, 'Change Candidate Vacancy Status', 7, 'changeCandidateVacancyStatus'),
                (147, 'Subscriber', 3, 'subscriber');";
        $this->sql = $sql;
    }

    public function getUserInputWidgets()
    {
    }

    public function setUserInputs()
    {
    }

    public function getNotes()
    {
    }

    public function execute()
    {
        $this->incrementNumber = 80;
        parent::execute();
        $result = [];
        foreach ($this->sql as $sql) {
            $result[] = $this->upgradeUtility->executeSql($sql);
        }
        $this->checkTransactionComplete($result);
        $this->updateOhrmUpgradeInfo($this->transactionComplete, $this->incrementNumber);
        $this->upgradeUtility->finalizeTransaction($this->transactionComplete);
        $this->upgradeUtility->closeDbConnection();
    }
}
