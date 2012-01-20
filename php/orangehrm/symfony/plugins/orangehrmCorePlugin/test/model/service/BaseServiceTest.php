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
 * @group Core
 */
class BaseServiceTest extends PHPUnit_Framework_TestCase {

    private $baseService;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp() {
        $this->baseService = new BaseService();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/test/fixtures/sample_query_extensions.yml';
        PluginQueryExtensionManager::instance()->setQueryExtensions(sfYaml::load($this->fixture));
    }

    /**
     * Testing decorateQuery method for raw SQL for additional select fields
     */
    public function testDecorateQuery_SQL_SelectFields() {
        /* Single field */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10'";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname`, `emp_middle_name` FROM `hs_hr_employee` WHERE `emp_number` = '10'";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForSelect', 'sampleMethod1', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);

        /* Multiple fields */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10'";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname`, `emp_middle_name`, `job_title_code`, `joined_date` FROM `hs_hr_employee` WHERE `emp_number` = '10'";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForSelect', 'sampleMethod2', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);

        /* Single field with alias */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10'";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname`, `emp_middle_name` AS `middleName` FROM `hs_hr_employee` WHERE `emp_number` = '10'";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForSelect', 'sampleMethod3', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);

        /* Multiple fields with aliases */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10'";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname`, `emp_middle_name` AS `middleName`, `job_title_code`, `joined_date` AS `active` FROM `hs_hr_employee` WHERE `emp_number` = '10'";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForSelect', 'sampleMethod4', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);

        /* Single field with table id */

        /* Multiple fields with table ids */
    }

    /**
     * Testing decorateQuery method for raw SQL for additional select fields
     */
    public function testDecorateQuery_SQL_WhereClauses() {
        /* Single field without a WHERE clause */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee`";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_middle_name` = 'Charles'";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForWhere', 'sampleMethod1', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);

        /* Single field */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10'";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10' AND `emp_middle_name` = 'Charles'";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForWhere', 'sampleMethod1', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);

        /* Multiple fields without a WHERE clause */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee`";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_middle_name` = 'Charles' AND `joined_date` = '1'";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForWhere', 'sampleMethod2', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);

        /* Multiple fields */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10'";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10' AND `emp_middle_name` = 'Charles' AND `joined_date` = '1'";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForWhere', 'sampleMethod2', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);

        /* Single field with table id */

        /* Multiple fields with table ids */

        /* Single field with a GROUP BY clause */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10' GROUP BY `job_title_code`";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10' AND `emp_middle_name` = 'Charles' GROUP BY `job_title_code`";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForWhere', 'sampleMethod1', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);

        /* Single field with a ORDER BY clause */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10' ORDER BY `emp_firstname`";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10' AND `emp_middle_name` = 'Charles' ORDER BY `emp_firstname`";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForWhere', 'sampleMethod1', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);

        /* Single field with a LIMIT clause */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10' LIMIT 0, 50";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10' AND `emp_middle_name` = 'Charles' LIMIT 0, 50";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForWhere', 'sampleMethod1', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);

        /* Single field with a GROUP BY, ORDER BY and LIMIT clauses */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10' GROUP BY `job_title_code` ORDER BY `emp_firstname` LIMIT 0, 50";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` WHERE `emp_number` = '10' AND `emp_middle_name` = 'Charles' GROUP BY `job_title_code` ORDER BY `emp_firstname` LIMIT 0, 50";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForWhere', 'sampleMethod1', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);
    }

    public function testDecorateQuery_SQL_OrderByClauses() {
        /* Single field without a WHERE clause */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee`";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` ORDER BY `emp_firstname`";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForOrderBy', 'sampleMethod1', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);

        /* Single field */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` ORDER BY `emp_lastname` LIMIT 0,10";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` ORDER BY `emp_firstname` , `emp_lastname`   LIMIT  0,10";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForOrderBy', 'sampleMethod2', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);

        /* Multiple fields without a WHERE clause */
        $originalQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` ORDER BY `emp_lastname` LIMIT 0,10";
        $expectedQuery = "SELECT `emp_firstname`, `emp_lastname` FROM `hs_hr_employee` ORDER BY `emp_firstname`, `job_title_code`, `joined_date` , `emp_lastname`   LIMIT  0,10";
        $resultQuery = $this->baseService->decorateQuery('SampleService_ForOrderBy', 'sampleMethod3', $originalQuery);
        $this->assertEquals($expectedQuery, $resultQuery);    }

}
