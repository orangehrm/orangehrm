<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SimpleUserRoleFactoryTest
 *
 * @group Core
 */
class SimpleUserRoleFactoryTest extends PHPUnit_Framework_TestCase {

    /* Test decorateUserRole() method.
     * Testcase for the senario where the user is an Admin and not a Ess User.
     */
    public function testDecorateUserWhenUserIsAdminNotEssUser() {

        $userObj = new User();
        $simpleUserRoleFactory = new SimpleUserRoleFactory();

        
        $userRoleArray = array('isAdmin' => true, 'isSupervisor' => false, 'isEssUser' => false);
        
        $userDecorated = $simpleUserRoleFactory->decorateUserRole($userObj, $userRoleArray);
        $menuItemArray = $userDecorated->getAccessibleTimeSubMenus();
        $menuItem = $menuItemArray[0]->getDisplayName();

        $this->assertTrue($userDecorated instanceof User);
        $this->assertEquals( 1, count($menuItemArray));
        $this->assertEquals("Employee Timesheets", $menuItem);

       
        
    }

    /* Test decorateUserRole() method.
     * Testcase for the senario where the user is an Admin and an Ess User.
     */
    public function testDecorateUserWhenUserIsAdminAndEssUser() {

        $userObj = new User();
        $simpleUserRoleFactory = new SimpleUserRoleFactory();

        $userRoleArray = array('isAdmin' => true, 'isSupervisor' => false, 'isEssUser' => true);

        $userDecorated = $simpleUserRoleFactory->decorateUserRole($userObj, $userRoleArray);
        $menuItemArray = $userDecorated->getAccessibleTimeSubMenus();
        $menuItem1 = $menuItemArray[0]->getDisplayName();
        $menuItem2 = $menuItemArray[1]->getDisplayName();

        $this->assertTrue($userDecorated instanceof User);
        $this->assertEquals( 2, count($menuItemArray));
        $this->assertEquals("My Timesheets", $menuItem1);
        $this->assertEquals("Employee Timesheets", $menuItem2);

    }

    /* Test decorateUserRole() method.
     * Testcase for the senario where the user is an Supervisor and an Ess User.
     */
    public function testDecorateUserWhenUserIsSupervisorAndEssUser() {

        $userObj = new User();
        $simpleUserRoleFactory = new SimpleUserRoleFactory();

        $userRoleArray = array('isAdmin' => false, 'isSupervisor' => true, 'isEssUser' => true);

        $userDecorated = $simpleUserRoleFactory->decorateUserRole($userObj, $userRoleArray);
        $menuItemArray = $userDecorated->getAccessibleTimeSubMenus();
        $menuItem1 = $menuItemArray[0]->getDisplayName();
        $menuItem2 = $menuItemArray[1]->getDisplayName();

        $this->assertTrue($userDecorated instanceof User);
        $this->assertEquals( 2, count($menuItemArray));
        $this->assertEquals("My Timesheets", $menuItem1);
        $this->assertEquals("Employee Timesheets", $menuItem2);

    }

    /* Test decorateUserRole() method.
     * Testcase for the senario where the user is only an Ess User.
     */
    public function testDecorateUserWhenUserIsOnlyAnEssUser() {

        $userObj = new User();
        $simpleUserRoleFactory = new SimpleUserRoleFactory();

        $userRoleArray = array('isAdmin' => false, 'isSupervisor' => false, 'isEssUser' => true);

        $userDecorated = $simpleUserRoleFactory->decorateUserRole($userObj, $userRoleArray);
        $menuItemArray = $userDecorated->getAccessibleTimeSubMenus();
        $menuItem = $menuItemArray[0]->getDisplayName();

        $this->assertTrue($userDecorated instanceof User);
        $this->assertEquals( 1, count($menuItemArray));
        $this->assertEquals("My Timesheets", $menuItem);


    }

}

?>
