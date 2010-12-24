<?php
require_once 'PHPUnit/Framework.php';

class EmailServiceTest extends PHPUnit_Framework_TestCase{
	
	private $mailService;	
	/**
     * PHPUnit setup function
     */
    public function setup() {
    	$this->mailService = new EmailService();	
    }
    
    public function testSendMail()
    {	
    	/*$this->mailService->setTo(array('isamantha@gmail.com'));
    	$this->mailService->setSubject("Cricket");
    	$this->mailService->setMailBody("adh adshagd adgad ");
    	$result	=	true;//$this->mailService->sendMail();
    	$this->assertTrue($result);*/
    }
}