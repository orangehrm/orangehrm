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
class ReportGeneratorServiceTest extends PHPUnit_Framework_TestCase {

    private $reportGeneratorService;

    protected function setUp() {

        $this->reportGeneratorService = new ReportGeneratorService();
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/test/fixtures/ReportGeneratorService.yml');
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/test/fixtures/ReportGeneratorService.yml';      
    }

    /* Test getReportName */

    public function testGetReportName() {

        $reportId = 1;
        $report = TestDataService::fetchObject('Report', $reportId);

        $reportableServiceMock = $this->getMock('ReportableService', array('getReport'));
        $reportableServiceMock->expects($this->once())
                ->method('getReport')
                ->with($reportId)
                ->will($this->returnValue($report));

        $this->reportGeneratorService->setReportableService($reportableServiceMock);
        $result = $this->reportGeneratorService->getReportName($reportId);

        $this->assertEquals("Project Report", $result);
    }

    public function testGetRuntimeFilterFieldWidgetNamesAndLabels() {

        $reportId = 1;
        $report = TestDataService::fetchObject('Report', $reportId);

        $selectedFilterFields = new Doctrine_Collection("SelectedFilterField");

        $selectedFilterFields->add(TestDataService::fetchObject('SelectedFilterField', array(1, 1)));
        $selectedFilterFields->add(TestDataService::fetchObject('SelectedFilterField', array(1, 2)));

        $reportableServiceMock = $this->getMock('ReportableService', array('getReport', 'getSelectedFilterFieldsByType'));

        $reportableServiceMock->expects($this->once())
                ->method('getReport')
                ->with($reportId)
                ->will($this->returnValue($report));

        $reportableServiceMock->expects($this->once())
                ->method('getSelectedFilterFieldsByType')
                ->will($this->returnValue($selectedFilterFields));

        $this->reportGeneratorService->setReportableService($reportableServiceMock);
        $runtimeFilterFieldWidgetNamesAndLabels = $this->reportGeneratorService->getRuntimeFilterFieldWidgetNamesAndLabels($reportId);

        $this->assertEquals(2, count($runtimeFilterFieldWidgetNamesAndLabels));
        $this->assertEquals('ohrmWidgetInputCheckbox', $runtimeFilterFieldWidgetNamesAndLabels[1]['widgetName']);
        $this->assertEquals('activity_show_deleted', $runtimeFilterFieldWidgetNamesAndLabels[1]['labelName']);
    }

    /* Test getSelectedFilterFieldIdsByReportId method */

    public function testGetSelectedFilterFieldIdsByReportId() {

        $reportId = 1;

        $selecteFilterFields = new Doctrine_Collection("SelectedFilterField");

        $selecteFilterFields->add(TestDataService::fetchObject('SelectedFilterField', array(1, 1)));
        $selecteFilterFields->add(TestDataService::fetchObject('SelectedFilterField', array(1, 2)));

        $reportableServiceMock = $this->getMock('ReportableService', array('getSelectedFilterFields'));
        $reportableServiceMock->expects($this->once())
                ->method('getSelectedFilterFields')
                ->with($reportId, true)
                ->will($this->returnValue($selecteFilterFields));

        $this->reportGeneratorService->setReportableService($reportableServiceMock);
        $result = $this->reportGeneratorService->getSelectedFilterFieldIdsByReportId($reportId);

        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]);
        $this->assertEquals(2, $result[1]);
    }

    /* Test getReportGroupIdOfAReport method */

    public function testGetReportGroupIdOfAReport() {

        $reportId = 1;
        $report = TestDataService::fetchObject('Report', $reportId);

        $reportableServiceMock = $this->getMock('ReportableService', array('getReport'));

        $reportableServiceMock->expects($this->once())
                ->method('getReport')
                ->with($reportId)
                ->will($this->returnValue($report));

        $this->reportGeneratorService->setReportableService($reportableServiceMock);
        $reportGroupId = $this->reportGeneratorService->getReportGroupIdOfAReport($reportId);

        $this->assertEquals(1, $reportGroupId);
    }

    /* Test getSelectedRuntimeFilterFields method */

    public function testGetSelectedRuntimeFilterFields() {

        $reportId = 1;
        $report = TestDataService::fetchObject('Report', $reportId);

        $selectedFilterFields = new Doctrine_Collection("SelectedFilterField");

        $selectedFilterFields->add(TestDataService::fetchObject('SelectedFilterField', array(1, 1)));
        $selectedFilterFields->add(TestDataService::fetchObject('SelectedFilterField', array(1, 2)));

        $reportableServiceMock = $this->getMock('ReportableService', array('getReport', 'getSelectedFilterFieldsByType'));

        $reportableServiceMock->expects($this->once())
                ->method('getReport')
                ->with($reportId)
                ->will($this->returnValue($report));

        $reportableServiceMock->expects($this->once())
                ->method('getSelectedFilterFieldsByType')
                ->will($this->returnValue($selectedFilterFields));

        $this->reportGeneratorService->setReportableService($reportableServiceMock);
        $selectedRuntimeFilterFieldList = $this->reportGeneratorService->getSelectedRuntimeFilterFields($reportId);

        $this->assertEquals(2, count($selectedRuntimeFilterFieldList));
        $this->assertEquals(1, $selectedRuntimeFilterFieldList[0]->getFilterFieldId());
        $this->assertEquals(2, $selectedRuntimeFilterFieldList[1]->getFilterFieldId());
    }

    /* Test generateRuntimeWhereClauseConditions method */

    public function testGenerateRuntimeWhereClauseConditions() {

        $userRoleArray['isAdmin'] = true;
        $userObj = new User();
        $simpleUserRoleFactory = new SimpleUserRoleFactory();
        $decoratedUser = $simpleUserRoleFactory->decorateUserRole($userObj, $userRoleArray);

        sfContext::getInstance()->getUser()->setAttribute("user", $decoratedUser);
                
        $userRoleManager = new UnitTestUserRoleManager();
        sfContext::getInstance()->setUserRoleManager($userRoleManager);          

//        $selectedRuntimeFilterFields = TestDataService::loadObjectList('FilterField', $this->fixture, 'FilterField');
        $selectedRuntimeFilterFields = new Doctrine_Collection("FilterField");
        $selectedRuntimeFilterFields->add(TestDataService::fetchObject('FilterField', 1));
        $selectedRuntimeFilterFields->add(TestDataService::fetchObject('FilterField', 2));
        $selectedRuntimeFilterFields->add(TestDataService::fetchObject('FilterField', 3));
        
        $values = array('project_name' => 2, 'project_date_range' => Array('from' => 2011 - 05 - 17, 'to' => 2011 - 05 - 24), 'activity_show_deleted' => '');

        $conditionArray = $this->reportGeneratorService->generateRuntimeWhereClauseConditions($selectedRuntimeFilterFields, $values);

        $this->assertEquals(2, count($conditionArray));
        $this->assertEquals("ohrm_project.project_id = 2 AND ohrm_project_activity.is_deleted = 0", $conditionArray[2]);
        $this->assertEquals("( date BETWEEN '1989' AND '1982' )", $conditionArray[1]);
    }

    /* Tests getSelectConditionWithoutSummaryFunction method */

    public function testGetSelectConditionWithoutSummaryFunction() {

        $reportId = 1;

        $selectedDisplayFields = new Doctrine_Collection("SelectedDisplayField");

        $selectedDisplayFields->add(TestDataService::fetchObject('SelectedDisplayField', array(1, 1, 1)));
        $selectedDisplayFields->add(TestDataService::fetchObject('SelectedDisplayField', array(2, 2, 1)));

        $reportableServiceMock = $this->getMock('ReportableService', array('getSelectedDisplayFields'));

        $reportableServiceMock->expects($this->once())
                ->method('getSelectedDisplayFields')
                ->with($reportId)
                ->will($this->returnValue($selectedDisplayFields));

        $this->reportGeneratorService->setReportableService($reportableServiceMock);
        $selectStatement = $this->reportGeneratorService->getSelectConditionWithoutSummaryFunction($reportId);

        $this->assertEquals('ohrm_project.name AS projectname,ohrm_project_activity.name AS activityname,CONCAT(hs_hr_employee.emp_firstname, " " ,hs_hr_employee.emp_lastname) AS employeeName', $selectStatement);        
    }

    /* Tests generateSql method */

    public function testGenerateSql() {

        $reportId = 1;
        $reportGroupId = 1;
        $report = TestDataService::fetchObject('Report', $reportId);
        $reportGroup = TestDataService::fetchObject('ReportGroup', $reportGroupId);
        $selectedGroupField = TestDataService::fetchObject('SelectedGroupField', array(1, 1, 1));

        $reportableServiceMock = $this->getMock('ReportableService', array('getReport', 'getReportGroup', 'getSelectedGroupField'));

        $reportableServiceMock->expects($this->once())
                ->method('getReport')
                ->with($reportId)
                ->will($this->returnValue($report));

        $reportableServiceMock->expects($this->once())
                ->method('getReportGroup')
                ->will($this->returnValue($reportGroup));

        $reportableServiceMock->expects($this->once())
                ->method('getSelectedGroupField')
                ->with($reportId)
                ->will($this->returnValue($selectedGroupField));

        $selectConditionWithoutSummaryFunction = "ohrm_project_activity.name AS activityname";

        $reportGeneratorServiceMock = $this->getMock('ReportGeneratorService', array('getSelectConditionWithoutSummaryFunction'));

        $reportGeneratorServiceMock->expects($this->once())
                ->method('getSelectConditionWithoutSummaryFunction')
                ->with($reportId)
                ->will($this->returnValue($selectConditionWithoutSummaryFunction));

        $conditionArray = array('2' => "ohrm_project.project_id = 1 AND ohrm_project_activity.is_deleted = 0");
        $reportGeneratorServiceMock->setReportableService($reportableServiceMock);
        $sql = $reportGeneratorServiceMock->generateSql($reportId, $conditionArray);

        $this->assertEquals("SELECT ohrm_project_activity.name AS activityname,ROUND(COALESCE(sum(duration)/3600, 0),2) AS totalduration FROM ohrm_project_activity LEFT JOIN (SELECT * FROM ohrm_timesheet_item WHERE true) AS ohrm_timesheet_item  ON (ohrm_timesheet_item.activity_id = ohrm_project_activity.activity_id) LEFT JOIN ohrm_project ON (ohrm_project.project_id = ohrm_project_activity.project_id) WHERE ohrm_project.project_id = 1 AND ohrm_project_activity.is_deleted = 0 GROUP BY ohrm_project_activity.activity_id", $sql);
    }

    /* Test getProjectActivityNameByActivityId */

    public function testGetProjectActivityNameByActivityId() {

        $activityId = 1;
        $activity = TestDataService::fetchObject('ProjectActivity', $activityId);

        $reportableServiceMock = $this->getMock('ReportableService', array('getProjectActivityByActivityId'));
        $reportableServiceMock->expects($this->once())
                ->method('getProjectActivityByActivityId')
                ->with($activityId)
                ->will($this->returnValue($activity));

        $this->reportGeneratorService->setReportableService($reportableServiceMock);
        $result = $this->reportGeneratorService->getProjectActivityNameByActivityId($activityId);

        $this->assertEquals("Create Schema", $result);
    }

    public function testSimplexmlToArray() {

        $elementPropertyXmlString = "<xml><labelGetter>activityname</labelGetter><placeholderGetters><id>activity_id</id><total>totalduration</total><projectId>projectId</projectId><from>fromDate</from><to>toDate</to></placeholderGetters><urlPattern>../../displayProjectActivityDetailsReport?reportId=3&amp;activityId={id}&amp;total={total}&amp;from={from}&amp;to={to}&amp;projectId={projectId}</urlPattern></xml>";

        $xmlIterator = new SimpleXMLIterator($elementPropertyXmlString);

        $elementPropertyArray = $this->reportGeneratorService->simplexmlToArray($xmlIterator);

        $this->assertEquals(3, count($elementPropertyArray));
        $this->assertEquals("activityname", $elementPropertyArray['labelGetter']);
        $this->assertEquals("totalduration", $elementPropertyArray["placeholderGetters"]["total"]);
    }

    public function testGetHeaderGroups() {

        $reportId = 1;

        $headers = $this->reportGeneratorService->getHeaderGroups($reportId);

        $this->assertTrue(true);
    }

    /* Tests generateSql with meta display fields method */

    public function testGenerateSqlWithStaticColumns() {

        $reportId = 1;
        $reportGroupId = 1;
        $report = TestDataService::fetchObject('Report', $reportId);
        $reportGroup = TestDataService::fetchObject('ReportGroup', $reportGroupId);
        $selectedGroupField = TestDataService::fetchObject('SelectedGroupField', array(1, 1, 1));

        $reportableServiceMock = $this->getMock('ReportableService', array('getReport', 'getReportGroup', 'getSelectedGroupField'));

        $reportableServiceMock->expects($this->once())
                ->method('getReport')
                ->with($reportId)
                ->will($this->returnValue($report));

        $reportableServiceMock->expects($this->once())
                ->method('getReportGroup')
                ->will($this->returnValue($reportGroup));

        $reportableServiceMock->expects($this->once())
                ->method('getSelectedGroupField')
                ->with($reportId)
                ->will($this->returnValue($selectedGroupField));

        $selectConditionWithoutSummaryFunction = "ohrm_project_activity.name AS activityname";

        $reportGeneratorServiceMock = $this->getMock('ReportGeneratorService', array('getSelectConditionWithoutSummaryFunction'));

        $reportGeneratorServiceMock->expects($this->once())
                ->method('getSelectConditionWithoutSummaryFunction')
                ->with($reportId)
                ->will($this->returnValue($selectConditionWithoutSummaryFunction));

        $staticColumns = null;
        $staticColumns["projectId"] = 1;
        $staticColumns["fromDate"] = "1970-01-01";
        $staticColumns["toDate"] = date("Y-m-d");

        $conditionArray = array('2' => "ohrm_project.project_id = 1 AND ohrm_project_activity.is_deleted = 0");
        $reportGeneratorServiceMock->setReportableService($reportableServiceMock);
        $sql = $reportGeneratorServiceMock->generateSql($reportId, $conditionArray, $staticColumns);

        $this->assertEquals("SELECT '1' AS projectId , '1970-01-01' AS fromDate , '" . $staticColumns["toDate"] . "' AS toDate , ohrm_project_activity.name AS activityname,ROUND(COALESCE(sum(duration)/3600, 0),2) AS totalduration FROM ohrm_project_activity LEFT JOIN (SELECT * FROM ohrm_timesheet_item WHERE true) AS ohrm_timesheet_item  ON (ohrm_timesheet_item.activity_id = ohrm_project_activity.activity_id) LEFT JOIN ohrm_project ON (ohrm_project.project_id = ohrm_project_activity.project_id) WHERE ohrm_project.project_id = 1 AND ohrm_project_activity.is_deleted = 0 GROUP BY ohrm_project_activity.activity_id", $sql);
    }

    public function testLinkFilterFieldIdsToFormValues() {

        $formValues = array(
            'project_name' => 2,
            'activity_show_deleted' => 'on',
            'project_date_range' => array('from' => '2011-01-12', 'to' => '2011-09-23')
        );


        $selectedRuntimeFilterFieldList = new Doctrine_Collection("FilterField");

        $selectedRuntimeFilterFieldList->add(TestDataService::fetchObject('FilterField', 1));
        $selectedRuntimeFilterFieldList->add(TestDataService::fetchObject('FilterField', 2));

        $filterFieldIdAndValueArray = $this->reportGeneratorService->linkFilterFieldIdsToFormValues($selectedRuntimeFilterFieldList, $formValues);

        $this->assertEquals(2, count($filterFieldIdAndValueArray));
        $this->assertEquals(2, $filterFieldIdAndValueArray[1]['filterFieldId']);
        $this->assertEquals('on', $filterFieldIdAndValueArray[1]['value']);
    }

    public function testConstructWhereStatementForUneryOperator() {

        $selectedFilterField = TestDataService::fetchObject('SelectedFilterField', array(2, 4));
        $whereCondition = '=';

        $whereClause = $this->reportGeneratorService->constructWhereStatementForUneryOperator($selectedFilterField, $whereCondition);
        $this->assertEquals("ohrm_project.project_id = 'nus'", $whereClause);
    }

    public function testConstructWhereStatementForBetweenOperator() {

        $selectedFilterField = TestDataService::fetchObject('SelectedFilterField', array(2, 5));
        $whereCondition = 'BETWEEN';

        $whereClause = $this->reportGeneratorService->constructWhereStatementForBetweenOperator($selectedFilterField, $whereCondition);
        
        $this->assertEquals("hs_hr_emp_basicsalary.ebsal_basic_salary BETWEEN '12000' AND '25000'", $whereClause);
    }

    public function testGenerateWhereClauseForPredefinedReport(){

        $selectedFilterField = TestDataService::fetchObject('SelectedFilterField', array(2, 4));

        $whereClause = $this->reportGeneratorService->generateWhereClauseForPredefinedReport($selectedFilterField);

        $this->assertEquals("ohrm_project.project_id = 'nus'", $whereClause);
    }

    public function testGenerateWhereClauseConditionArrayForPredefinedFilterFields(){

        $selectedFilterFieldList = new Doctrine_Collection("SelectedFilterField");
        $values = null;

        $selectedFilterFieldList->add(TestDataService::fetchObject('SelectedFilterField', array(3, 4)));
        $selectedFilterFieldList->add(TestDataService::fetchObject('SelectedFilterField', array(3, 5)));
        $selectedFilterFieldList->add(TestDataService::fetchObject('SelectedFilterField', array(3, 6)));
        $selectedFilterFieldList->add(TestDataService::fetchObject('SelectedFilterField', array(3, 7)));

        $conditionArray = $this->reportGeneratorService->generateWhereClauseConditionArray($selectedFilterFieldList, $values);

        $this->assertEquals(2, count($conditionArray));
        $this->assertEquals("ohrm_project.project_id = 'nus' AND hs_hr_employee.city_code = 'C123'", $conditionArray[2]);
    }

//    public function testGenerateWhereClauseConditionArrayForRuntimeFilterFields(){
//
//        $selectedFilterFieldList = new Doctrine_Collection("SelectedFilterField");
//        $formValues = null;
//
//
//    }

    public function testSaveSelectedDisplayFields(){
        
        $displayFieldIds = array('0' => 1, '1' => 2);
        $reportId = 1;

        $this->reportGeneratorService->saveSelectedDisplayFields($displayFieldIds, $reportId);
        
        $this->assertTrue(true);
    }

    public function testConstructSelectClauseForDisplayField() {
        
        $displayField = new DisplayField();
        $fieldName = 'Acme';
        $displayField->setName($fieldName);
        
        
        $options = array(
                      array('is_value_list' => true, 'is_encrypted' => true, 'field_alias' => 'Abcd'),
                      array('is_value_list' => true, 'is_encrypted' => true, 'field_alias' => null),
                      array('is_value_list' => true, 'is_encrypted' => false, 'field_alias' => 'Abcd'),
                      array('is_value_list' => true, 'is_encrypted' => false, 'field_alias' => null),
                      array('is_value_list' => false, 'is_encrypted' => false, 'field_alias' => 'Abcd'),
                      array('is_value_list' => false, 'is_encrypted' => false, 'field_alias' => null),
                      array('is_value_list' => false, 'is_encrypted' => true, 'field_alias' => 'Abcd'),
                      array('is_value_list' => false, 'is_encrypted' => true, 'field_alias' => null)
    
            );            
        
        $encrypt = KeyHandler::keyExists();
        
        if ($encrypt) {
            $key = KeyHandler::readKey();
        }
        
        foreach ($options as $option) {
            $displayField = new DisplayField();
            $displayField->setName($fieldName);
            $displayField->setIsValueList($option['is_value_list']);
            $displayField->setIsEncrypted($option['is_encrypted']);
            $displayField->setFieldAlias($option['field_alias']);
            
            $expected = $fieldName;
            
            if ($encrypt && $option['is_encrypted']) {
                $expected = 'AES_DECRYPT(UNHEX('. $fieldName . '),"' . $key . '")';
            }
            if ($option['is_value_list']) {
                $expected = 'GROUP_CONCAT(DISTINCT ' . $expected . " SEPARATOR '|" . '\n' . "|' ) ";
            }
            if ($option['field_alias']) {
                $expected = $expected . ' AS ' . $option['field_alias'];
            }
            
            $selectStatement = null;
            $selectStatement = $this->reportGeneratorService->constructSelectClauseForDisplayField($selectStatement, $displayField);
            
            $this->assertEquals($expected, $selectStatement);
            
            $selectStatement = "x";

            $expected = 'x,' . $expected;
                    
            $selectStatement = $this->reportGeneratorService->constructSelectClauseForDisplayField($selectStatement, $displayField);
            $this->assertEquals($expected, $selectStatement);
            
        }
    }

}

