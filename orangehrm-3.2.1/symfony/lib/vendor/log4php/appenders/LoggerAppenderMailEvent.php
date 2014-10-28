<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *	   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package log4php
 */

/**
 * Log every events as a separate email.
 * 
 * Configurable parameters for this appender are:
 * 
 * - layout             - Sets the layout class for this appender (required)
 * - to                 - Sets the recipient of the mail (required)
 * - from               - Sets the sender of the mail (optional)
 * - subject            - Sets the subject of the mail (optional)
 * - smtpHost           - Sets the mail server (optional, default is ini_get('SMTP'))
 * - port               - Sets the port of the mail server (optional, default is 25)
 *
 * An example:
 * 
 * {@example ../../examples/php/appender_mailevent.php 19}
 *  
 * {@example ../../examples/resources/appender_mailevent.properties 18}
 *
 * 
 * The above will output something like:
 * <pre>
 *      Date: Tue,  8 Sep 2009 21:51:04 +0200 (CEST)
 *      From: someone@example.com
 *      To: root@localhost
 *      Subject: Log4php test
 *
 *      Tue Sep  8 21:51:04 2009,120 [5485] FATAL root - Some critical message!
 * </pre>
 *
 * @version $Revision: 883108 $
 * @package log4php
 * @subpackage appenders
 */
class LoggerAppenderMailEvent extends LoggerAppender {

	/**  'from' field (defaults to 'sendmail_from' from php.ini on win32).
	 * @var string
	 */
	private $from = null;

	/** Mailserver port (win32 only).
	 * @var integer 
	 */
	private $port = 25;

	/** Mailserver hostname (win32 only).
	 * @var string   
	 */
	private $smtpHost = null;

	/**
	 * @var string 'subject' field
	 */
	private $subject = '';

	/**
	 * @var string 'to' field
	 */
	private $to = null;
	
	/**
	 * @access private
	 */
	protected $requiresLayout = true;

	/** @var indiciates if this appender should run in dry mode */
	private $dry = false;
	
	/**
	 * Constructor.
	 *
	 * @param string $name appender name
	 */
	public function __construct($name = '') {
		parent::__construct($name);
	}

	public function __destruct() {
       $this->close();
   	}
   	
	public function activateOptions() {
	    if (empty($this->layout)) {
	        throw new LoggerException("LoggerAppenderMailEvent requires layout!");
	    }
	    if (empty($this->to)) {
            throw new LoggerException("LoggerAppenderMailEvent was initialized with empty 'from' ($this->from) or 'to' ($this->to) Adress!");
        }
        
        $sendmail_from = ini_get('sendmail_from');
        if (empty($this->from) and empty($sendmail_from)) {
            throw new LoggerException("LoggerAppenderMailEvent requires 'from' or on win32 at least the ini variable sendmail_from!");
        }
        
        $this->closed = false;
	}
	
	public function close() {
		$this->closed = true;
	}

	public function setFrom($from) {
		$this->from = $from;
	}
	
	public function setPort($port) {
		$this->port = (int)$port;
	}
	
	public function setSmtpHost($smtpHost) {
		$this->smtpHost = $smtpHost;
	}
	
	public function setSubject($subject) {
		$this->subject = $subject;
	}
	
	public function setTo($to) {
		$this->to = $to;
	}

	public function setDry($dry) {
		$this->dry = $dry;
	}
	
	public function append(LoggerLoggingEvent $event) {
		$smtpHost = $this->smtpHost;
		$prevSmtpHost = ini_get('SMTP');
		if(!empty($smtpHost)) {
			ini_set('SMTP', $smtpHost);
		} 

		$smtpPort = $this->port;
		$prevSmtpPort= ini_get('smtp_port');		
		if($smtpPort > 0 and $smtpPort < 65535) {
			ini_set('smtp_port', $smtpPort);
		}

		// On unix only sendmail_path, which is PHP_INI_SYSTEM i.e. not changeable here, is used.

		$addHeader = empty($this->from) ? '' : "From: {$this->from}\r\n";
		
		if(!$this->dry) {
			$result = mail($this->to, $this->subject, 
				$this->layout->getHeader() . $this->layout->format($event) . $this->layout->getFooter($event), 
				$addHeader);			
		    if ($result === false) {
		        // The error message is only printed to stderr as warning. Any idea how to get it?
		        throw new LoggerException("Error sending mail to '".$this->to."'!");
		    }
		} else {
		    echo "DRY MODE OF MAIL APP.: Send mail to: ".$this->to." with additional headers '".trim($addHeader)."' and content: ".$this->layout->format($event);
		}
			
		ini_set('SMTP', $prevSmtpHost);
		ini_set('smtp_port', $prevSmtpPort);
	}
}

