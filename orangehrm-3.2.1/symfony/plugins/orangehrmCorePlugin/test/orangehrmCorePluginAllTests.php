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
class orangehrmCorePluginAllTests {

    protected function setUp() {

    }

    public static function suite() {

        $suite = new PHPUnit_Framework_TestSuite('orangehrmCorePluginAllTest');

        /* Component Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/components/ListHeaderTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/PropertyPopulatorTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/LinkCellTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/ButtonTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/LabelCellTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/SortableHeaderCellTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/ListHeaderTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/CheckboxTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/HeaderCellTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/ohrmCellFilterTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/EnumCellFilterTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/components/CellTest.php');
        
        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/dao/ConfigDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/dao/EmailDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/authorization/dao/HomePageDaoTest.php');
 
        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/service/ConfigServiceTest.php');

        /* Factory Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/factory/SimpleUserRoleFactoryTest.php');

        /* AccessFlowStateMachine Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/AccessFlowStateMachineDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/AccessFlowStateMachineServiceTest.php');

        /* ReportGenerator Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/ReportableDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/ReportableServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/ReportGeneratorServiceTest.php');

        /* BaseService Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/service/BaseServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/BaseServiceDataTest.php');
        
        /* form validators */
        $suite->addTestFile(dirname(__FILE__) . '/form/validate/ohrmValidatorSchemaCompareTest.php');
        
        /* form widgets */
        $suite->addTestFile(dirname(__FILE__) . '/form/widget/ohrmWidgetFormTimeRangeTest.php');

        /* Extensions to Doctrine Models */
        $suite->addTestFile(dirname(__FILE__) . '/model/doctrine/PluginWorkflowStateMachineTest.php');        

        /* Cache tests */
        $suite->addTestFile(dirname(__FILE__) . '/cache/ohrmKeyValueCacheTest.php');
        
        /* Authorization */
        $suite->addTestFile(dirname(__FILE__) . '/authorization/service/UserRoleManagerServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/authorization/manager/BasicUserRoleManagerTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/authorization/dao/ScreenPermissionDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/authorization/service/ScreenPermissionServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/authorization/dao/ScreenDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/authorization/userrole/AdminUserRoleTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/authorization/userrole/SupervisorUserRoleTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/authorization/dao/DataGroupDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/authorization/service/DataGroupServiceTest.php');
        
        $suite->addTestFile(dirname(__FILE__) . '/authorization/dao/MenuDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/authorization/service/MenuServiceTest.php');
        
        return $suite;
    }

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

}

if (PHPUnit_MAIN_METHOD == 'orangehrmCorePluginAllTests::main') {
    orangehrmCorePluginAllTests::main();
}

