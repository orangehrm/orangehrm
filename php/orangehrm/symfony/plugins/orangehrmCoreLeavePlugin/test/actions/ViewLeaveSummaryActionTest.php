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

require_once sfConfig::get('sf_test_dir') . '/util/MockContext.class.php';
require_once sfConfig::get('sf_test_dir') . '/util/MockWebRequest.class.php';
require_once sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/modules/coreLeave/actions/viewLeaveSummaryAction.class.php';

/**
 * Testing ViewLeaveSummaryAction
 *
 * @author sujith
 */
class ViewLeaveSummaryActionTest extends PHPUnit_Framework_TestCase {

    /**
     * Set up method
     */
    protected function setUp() {

        /* Create mock objects required for testing */
        $this->context = MockContext::getInstance();

        $request = new MockWebRequest();

        // In sfConfigCache, we just need checkConfig method
        $configCache = $this->getMock('sfConfigCache', array('checkConfig'), array(), '', false);

        // Mock of controller, with redirect method mocked.
        $controller = $this->getMock('sfController', array('redirect', 'forward'), array(), '', false);
        $this->context->request = $request;
        $this->context->configCache = $configCache;
        $this->context->controller = $controller;
    }

    /**
     * Test whether entitlement box is editable for admin
     */
    public function testEntitlementEditableForAdmin() {
        // mock the User class
        $user = $this->getMock('sfUser', array('hasAttribute', 'getAttribute'), array(), '', false);
        $user->expects($this->once())
             ->method('hasAttribute')
             ->will($this->returnValue(true));

        $user->expects($this->once())
             ->method('getAttribute')
             ->will($this->returnValue(20));
        
        $this->context->user = $user;
        
        $request = $this->context->request;
        $request->setMethod(sfRequest::GET);

        $viewLeaveSummary = new viewLeaveSummaryAction($this->context, "coreLeave", "execute");
        $viewLeaveSummary->setUserDetails('isAdmin', 'Yes');

        //mocking the form
        $form = $this->getMock('LeaveSummaryForm', array('setRecordsLimitDefaultValue'), array());
        $form->expects($this->once())
             ->method('setRecordsLimitDefaultValue');

        $viewLeaveSummary->setForm($form);
        
        try {
            $viewLeaveSummary->execute($request);
            $this->assertTrue($form->leaveSummaryEditMode);
        } catch (Exception $e) {
            
        }
    }

    /**
     * Test whether entitlement box is dissabled for Supervisor
     */
    public function testEntitlementDissabledForSupervisor() {
        $user = $this->getMock('sfUser', array('hasAttribute', 'getAttribute'), array(), '', false);
        $user->expects($this->once())
             ->method('hasAttribute')
             ->will($this->returnValue(true));

        $user->expects($this->once())
             ->method('getAttribute')
             ->will($this->returnValue(20));

        $this->context->user = $user;

        $request = $this->context->request;
        $request->setMethod(sfRequest::GET);

        $viewLeaveSummary = new viewLeaveSummaryAction($this->context, "coreLeave", "execute");
        $viewLeaveSummary->setUserDetails('isSupervisor', 1);

        //mocking the form
        $form = $this->getMock('LeaveSummaryForm', array('setRecordsLimitDefaultValue'), array());
        $form->expects($this->once())
             ->method('setRecordsLimitDefaultValue');
        $viewLeaveSummary->setForm($form);

        try {
            $viewLeaveSummary->execute($request);
            $this->assertFalse($form->leaveSummaryEditMode);
        } catch (Exception $e) {

        }
    }

    /**
     * Test whether entitlement box is dissabled for ESS
     */
    public function testEntitlementDissabledForEss() {
        $user = $this->getMock('sfUser', array('hasAttribute', 'getAttribute'), array(), '', false);
        $user->expects($this->once())
             ->method('hasAttribute')
             ->will($this->returnValue(true));

        $user->expects($this->once())
             ->method('getAttribute')
             ->will($this->returnValue(20));

        $this->context->user = $user;

        $request = $this->context->request;
        $request->setMethod(sfRequest::GET);

        $viewLeaveSummary = new viewLeaveSummaryAction($this->context, "coreLeave", "execute");
        $viewLeaveSummary->setUserDetails('empNumber', '0001');

        //mocking the form
        $form = $this->getMock('LeaveSummaryForm', array('setRecordsLimitDefaultValue'), array());
        $form->expects($this->once())
             ->method('setRecordsLimitDefaultValue');
        $viewLeaveSummary->setForm($form);

        try {
            $viewLeaveSummary->execute($request);
            $this->assertFalse($form->leaveSummaryEditMode);
        } catch (Exception $e) {

        }
    }

    /**
     * Admin saves entitlement
     */
    public function testAdminSavedEntitlement() {
        // Set post parameters
        $parameters = array('hdnAction'=>'save',
                'hdnEmpId'=> array('0001'),
                'hdnLeaveTypeId'=> array('LT001'),
                'hdnLeavePeriodId'=> array(1),
                'txtLeaveEntitled' => array(15));
        
        // mock the User class
        $user = $this->getMock('sfUser', array('hasAttribute', 'getAttribute'), array(), '', false);
        $user->expects($this->once())
             ->method('hasAttribute')
             ->will($this->returnValue(true));

        $user->expects($this->once())
             ->method('getAttribute')
             ->will($this->returnValue(20));

        $this->context->user = $user;

        $request = $this->context->request;
        $request->setPostParameters($parameters);
        $request->setMethod(sfRequest::POST);

        $viewLeaveSummary = new viewLeaveSummaryAction($this->context, "coreLeave", "execute");
        $viewLeaveSummary->setUserDetails('isAdmin', 'Yes');

        //mocking the form
        $form = $this->getMock('LeaveSummaryForm', array('setRecordsLimitDefaultValue', 'isValid'), array());
        $form->expects($this->once())
             ->method('isValid')
             ->will($this->returnValue(true));

        $form->expects($this->once())
             ->method('setRecordsLimitDefaultValue');
        $viewLeaveSummary->setForm($form);

        try {
            $viewLeaveSummary->execute($request);
            $this->assertTrue($form->saveSuccess);
        } catch (Exception $e) {

        }
    }
}
?>
