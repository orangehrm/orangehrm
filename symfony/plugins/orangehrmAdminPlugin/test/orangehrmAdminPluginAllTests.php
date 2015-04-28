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
class orangehrmAdminPluginAllTests {

    protected function setUp() {

    }

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('orangehrmCoreLeavePluginAllTest');

        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/SystemUserDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/SkillDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/OrganizationDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/CompanyStructureDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/ProjectDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/JobTitleDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/CustomerDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/LocationDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/OperationalCountryDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/CountryDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/EmploymentStatusDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/SkillDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/LanguageDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/LicenseDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/EducationDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/MembershipDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/NationalityDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/PayGradeDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/JobCategoryDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/EmailNotificationDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/WorkShiftDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/ModuleDaoTest.php');

        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/service/LocalizationServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/PimCsvDataImportServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/CompanyStructureServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/JobTitleServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/CustomerServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/ProjectServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/LocationServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/OperationalCountryServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/CountryServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/EmploymentStatusServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/MembershipServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/NationalityServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/PayGradeServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/JobCategoryServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/WorkShiftServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/SystemUserServiceTest.php');
        
        /* other Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/wrapper/AdminWebServiceWrapperTest.php');
        return $suite;
    }

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

}

if (PHPUnit_MAIN_METHOD == 'orangehrmAdminPluginAllTests::main') {
    orangehrmCoreLeavePluginAllTests::main();
}

?>
