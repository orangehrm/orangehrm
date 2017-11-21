<?php


class loginTestCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    
    public function testValidCredentials(AcceptanceTester $I)
    {
		$I->am('ohrm user');
		$I->wantTo('Login to application as admin');
		$I->lookForwardTo('access to orangehrm application');
		$I->amOnPage('/');
		$I->fillField('txtUsername','admin');
		$I->fillField('txtPassword','Ohrm@1423');
		$I->click('Submit');
		$I->see('Dashboard');
    }

    public function testInvalidCredentials(AcceptanceTester $I)
    {
		$I->am('ohrm user');
		$I->wantTo('Check for invalid credentials');
		$I->lookForwardTo('access to orangehrm application with invalid credentials');
		$I->amOnPage('/');
		$I->fillField('txtUsername','admin');
		$I->fillField('txtPassword','admin1');
		$I->click('Submit');
		$I->see('Invalid credentials');
    }
    /**
     * 
     * @example{"empname":"testemp1","user":"testuser1","pword":"12345"}
     * @example{"empname":"testemp2","user":"testuser2","pword":"12345"}
     * @example{"empname":"testemp3","user":"testuser3","pword":"12345"}
     * 
     * */
     
     /**
    public function testAddUserInOHRMApp( AcceptanceTester $I, \Codeception\Example $example)
    {
		$I->am('ohrm user');
		$I->wantTo('check add user functionality');
		$I->lookForwardTo('access to orangehrm application and add 3 ess users');
		$I->amOnPage('/');
		$I->fillField('txtUsername','admin');
		$I->fillField('txtPassword','admin');
		$I->click('Submit');
		$I->amOnPage('pim/addEmployee');
		$I->fillField('firstName',$example['empname']);
		$I->fillField('lastName',$example['empname']);
		$I->checkOption('Create Login Details');
		$I->fillField('User Name',$example['user']);
		$I->fillField('Password',$example['pword']);
		$I->fillField('Confirm Password',$example['pword']);
		
		$I->click('btnSave');


		
		$I->see($example['empname']+' '+$example['empname']);
    }
    * */
    
    /**
     * 
     * @example{"empname":"testemp1","user":"testuser1","pword":"12345"}
     * @example{"empname":"testemp2","user":"testuser2","pword":"12345"}
     * @example{"empname":"testemp3","user":"testuser3","pword":"12345"}
     * 
     * */
     
     /**
    public function testLoginWithNewUsersInOHRMApp(AcceptanceTester $I, \Codeception\Example $example)
    {
		$I->am('ohrm user');
		$I->wantTo('check login with new users');
		$I->lookForwardTo('access to orangehrm application using credentials of new users');
		$I->amOnPage('/');
		$I->fillField('txtUsername',$example['user']);
		$I->fillField('txtPassword',$example['pword']);
		$I->click('Submit');
		$I->see('Dashboard');
    }
    * */

    
    
}
