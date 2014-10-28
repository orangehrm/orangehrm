<?php


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
    
    public function testGetEmailTemplateBestMatchLocaleFallback() {
        
        $locale = 'fr_FR';
        $email = new Email();
        $email->setName('leave.apply');
        $recipientRole = 'subscriber';
        $performerRole = 'ess';
        
        $template = new EmailTemplate();
        $template->fromArray(array('id' => 1, 'email_id' => 1, 'locale' => 'en_US', 'recipient_role' => 'supervisor', 
                'body' => 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/leaveApplicationBody.txt', 
                'subject' => 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/leaveApplicationSubject.txt'));
        
        $configService = $this->getMock('ConfigService', array('getAdminLocalizationDefaultLanguage'));
        $configService->expects($this->once())
                ->method('getAdminLocalizationDefaultLanguage')
                ->will($this->returnValue($locale)); 
        
        $emailDao = $this->getMock('EmailDao', array('getEmailTemplateMatches'));
        $emailDao->expects($this->at(0))
                ->method('getEmailTemplateMatches')
                ->with($email, $locale, $recipientRole, $performerRole)
                ->will($this->returnValue(array()));    
        $emailDao->expects($this->at(1))
                ->method('getEmailTemplateMatches')
                ->with($email, 'en_US', $recipientRole, $performerRole)
                ->will($this->returnValue(array($template))); 
        
        $this->mailService->setConfigService($configService);
        $this->mailService->setEmailDao($emailDao);
        $result = $this->mailService->getEmailTemplateBestMatch($email, $recipientRole, $performerRole);
        $this->assertEquals($template, $result);
    }
    
    public function testGetEmailTemplateMultipleMatches() {
        
        $locale = 'en_US';
        $email = new Email();
        $email->setName('leave.apply');
        $recipientRole = 'supervisor';
        $performerRole = 'ess';
        
        $template1 = new EmailTemplate();
        $template1->fromArray(array('id' => 1, 'email_id' => 1, 'locale' => 'en_US', 'recipient_role' => NULL, 'performer_role' => NULL,
                'body' => 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/leaveApplicationBody.txt', 
                'subject' => 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/leaveApplicationSubject.txt'));        
        
        $template2 = new EmailTemplate();
        $template2->fromArray(array('id' => 2, 'email_id' => 1, 'locale' => 'en_US', 'recipient_role' => 'supervisor', 'performer_role' => 'ess',
                'body' => 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/leaveApplicationBody.txt', 
                'subject' => 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/leaveApplicationSubject.txt'));    
        
        $configService = $this->getMock('ConfigService', array('getAdminLocalizationDefaultLanguage'));
        $configService->expects($this->once())
                ->method('getAdminLocalizationDefaultLanguage')
                ->will($this->returnValue($locale)); 
        
        $emailDao = $this->getMock('EmailDao', array('getEmailTemplateMatches'));
        $emailDao->expects($this->once())
                ->method('getEmailTemplateMatches')
                ->with($email, $locale, $recipientRole, $performerRole)
                ->will($this->returnValue(array($template1, $template2)));    
        
        $this->mailService->setConfigService($configService);
        $this->mailService->setEmailDao($emailDao);
        $result = $this->mailService->getEmailTemplateBestMatch($email, $recipientRole, $performerRole);
        $this->assertEquals($template2, $result);
    }    
    
    
}