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
		$I->fillField('txtPassword','admin');
		$I->click('Submit');
		$I->see('Dashboard');
    }
    
    
}
