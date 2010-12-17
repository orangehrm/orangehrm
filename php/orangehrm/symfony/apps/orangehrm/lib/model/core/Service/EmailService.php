<?php

class EmailService extends BaseService{
	
	const EMAILCONFIGURATION_SMTP_SECURITY_NONE = 'NONE';
	const EMAILCONFIGURATION_SMTP_SECURITY_TLS = 'TLS';
	const EMAILCONFIGURATION_SMTP_SECURITY_SSL = 'SSL';

	const EMAILCONFIGURATION_SMTP_AUTH_NONE = 'NONE';
	const EMAILCONFIGURATION_SMTP_AUTH_LOGIN = 'LOGIN';
	
	
	private $message 	=	'';
	
	/**
	 * Intialize the Mail Configeration
	 * @return unknown_type
	 */
	public function __construct(){
		include_once(sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'confs'.DIRECTORY_SEPARATOR.'mailConf.php');
                require_once sfConfig::get('sf_root_dir').'/lib/vendor/symfony/lib/vendor/swiftmailer/swift_required.php';
		
		$this->message	=	Swift_Message::newInstance();
		
	}
	
	/** Get Swift Mailler according to configeration
	 * 
	 * @return Swift Mailer
	 */
	private function getSwiftMailer(){
		switch($this->mailType)
		{
			case 'smtp':
				$transport = Swift_SmtpTransport::newInstance($this->smtpHost,$this->smtpPort);
						  
				$authType = $this->smtpAuth;
				if( $authType != self::EMAILCONFIGURATION_SMTP_AUTH_NONE ){
					$transport->setUsername($this->smtpUser);
  					$transport->setPassword($this->smtpPass);
				}
				
				$security = $this->smtpSecurity;
				if ($security != self::EMAILCONFIGURATION_SMTP_SECURITY_NONE) {
					$transport->setEncryption(strtolower($security));
				}	
				$mailer = Swift_Mailer::newInstance($transport);
				
			break;
			
			case 'sendmail':
				$transport = Swift_SendmailTransport::newInstance();
				$mailer    = Swift_Mailer::newInstance($transport);
			break;
			
			default:
				$transport = Swift_MailTransport::newInstance();

				//Create the Mailer using your created Transport
				$mailer = Swift_Mailer::newInstance($transport);

				
				
		}
		return $mailer;
	}
	
	
	/**
	 * Send Mail
	 * @return unknown_type
	 */
	public function sendMail(){
		$logPath = ROOT_PATH.'/lib/logs/';
		$logMessage = "";
		try{
			
			
			$logMessage = date('r')." Sending {$this->message->getSubject()} to ";
			foreach( $this->message->getTo() as $index=>$value){
				$logMessage .= $index.' ';
			}
			
			
			if( count($this->message->getTo()) > 0){
				$mailer = $this->getSwiftMailer();
				switch($this->mailType)
				{
					case 'smtp':
						$this->setFrom(array($this->mailAddress =>'OrangeHRM'));
						$numSent = $mailer->send($this->message);
					break;
					
					case 'sendmail':
						//if($mailer->getTransport()->isStarted())
						//{
							$this->message->setSender($this->mailAddress);
							$numSent = $mailer->send($this->message);
						//}
						//else
						//	$logMessage .= "\r\n fail to start send mailer transport";
					break;
					
					default:
						$numSent = $mailer->send($this->message);
				}
				
				
				
				error_log($logMessage."\r\n", 3, $logPath."notification_mails.log");
			}
			return true ;
		}catch (Exception $e) {
			error_log($e->getMessage()."\r\n", 3, $logPath."notification_mails.log");
			//throw new CoreServiceException($e->getMessage());
		}	 
	}
	
	/**
	 * Set Mail Subject
	 */
	public function setSubject( $subject ){
		$this->message->setSubject( $subject );
	}
	
	/**
	 * Set Mail Body
	 * @return unknown_type
	 */
	public function setMailBody( $mailBody ){
		$this->message->setBody( $mailBody );
	}
	
	/**
	 * Set To address
	 * @return unknown_type
	 */
	public function setTo( $mailTo )
	{
		$this->message->setTo( $mailTo );
	}
	
	/**
	 * Mail from address
	 * @param $mailFrom
	 * @return unknown_type
	 */
	public function setFrom( $mailFrom = array())
	{
		//if($this->mailType == 'smtp')
		 $this->message->setFrom( $mailFrom );
	}
	
	/**
	 * Get message
	 */
	public function getMessage( )
	{
		return $this->message;
	}
}
