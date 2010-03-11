<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * sfMail class.
 *
 * @package    symfony
 * @subpackage addon
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfMail.class.php 5106 2007-09-15 09:25:00Z fabien $
 */
class sfMail
{
  protected $mailer;

  public function __construct()
  {
    require_once(dirname(__FILE__).'/vendor/phpmailer/class.phpmailer.php');
    require_once(dirname(__FILE__).'/vendor/phpmailer/class.smtp.php');

    $this->mailer = new PHPMailer();
  }

  public function initialize()
  {
  }

  public function setCharset($charset)
  {
    $this->mailer->CharSet = $charset;
  }

  public function getCharset()
  {
    return $this->mailer->CharSet;
  }

  public function setContentType($content_type)
  {
    $this->mailer->ContentType = $content_type;
  }

  public function getContentType()
  {
    return $this->mailer->ContentType;
  }

  public function setPriority($priority)
  {
    $this->mailer->Priority = $priority;
  }

  public function getPriority()
  {
    return $this->mailer->Priority;
  }

  public function setEncoding($encoding)
  {
    $this->mailer->Encoding = $encoding;
  }

  public function getEncoding()
  {
    return $this->mailer->Encoding;
  }

  public function setSubject($subject)
  {
    $this->mailer->Subject = $subject;
  }

  public function getSubject()
  {
    return $this->mailer->Subject;
  }

  public function setBody($body)
  {
    $this->mailer->Body = $body;
  }

  public function getBody()
  {
    return $this->mailer->Body;
  }

  public function setMailer($type = 'mail', $options = array())
  {
    switch ($type)
    {
      case 'smtp':
        $this->mailer->IsSMTP();
        if (isset($options['keep_alive'])) $this->mailer->SMTPKeepAlive = true;
        break;
      case 'sendmail':
        $this->mailer->IsSendmail();
        break;
      default:
        $this->mailer->IsMail();
        break;
    }
  }
  
  public function getMailer()
  {
    return $this->mailer->Mailer;
  }

  public function setSender($address, $name = null)
  {
    if (!$address)
    {
      return;
    }

    if ($name == null)
    {
      list($address, $name) = $this->splitAddress($address);
    }
    $this->mailer->Sender = $address;
  }

  public function getSender()
  {
    return $this->mailer->Sender;
  }

  public function setFrom($address, $name = null)
  {
    if (!$address)
    {
      return;
    }

    if ($name == null)
    {
      list($address, $name) = $this->splitAddress($address);
    }
    $this->mailer->From     = $address;
    $this->mailer->FromName = $name;
  }

  public function getFrom()
  {
    return $this->mailer->From;
  }

  /*
   * $recipents:
   * test@example.com
   * Example email <test@example.com>
   * array('test@example.com', 'test1@example.com')
   * array('Example email <test@example.com>', 'test1@example.com')
   */
  public function addAddresses($addresses)
  {
    if (!$addresses)
    {
      return;
    }

    if (is_array($addresses))
    {
      foreach ($addresses as $address)
      {
        list($address, $name) = $this->splitAddress($address);
        $this->mailer->AddAddress($address, $name);
      }
    }
    else
    {
      list($address, $name) = $this->splitAddress($addresses);
      $this->mailer->AddAddress($address, $name);
    }
  }

  private function splitAddress($address)
  {
    if (preg_match('/^(.+)\s<(.+?)>$/', $address, $matches))
    {
      return array($matches[2], $matches[1]);
    }
    else
    {
      return array($address, '');
    }
  }

  public function addAddress($address, $name = null)
  {
    if ($name == null)
    {
      list($address, $name) = $this->splitAddress($address);
    }
    $this->mailer->AddAddress($address, $name);
  }

  public function addCc($address, $name = null)
  {
    if ($name == null)
    {
      list($address, $name) = $this->splitAddress($address);
    }
    $this->mailer->AddCc($address, $name);
  }

  public function addBcc($address, $name = null)
  {
    if ($name == null)
    {
      list($address, $name) = $this->splitAddress($address);
    }
    $this->mailer->AddBcc($address, $name);
  }

  public function addReplyTo($address, $name = null)
  {
    if (!$address)
    {
      return;
    }

    if ($name == null)
    {
      list($address, $name) = $this->splitAddress($address);
    }
    $this->mailer->AddReplyTo($address, $name);
  }

  public function clearAddresses()
  {
    $this->mailer->ClearAddresses();
  }

  public function clearCcs()
  {
    $this->mailer->ClearCcs();
  }

  public function clearBccs()
  {
    $this->mailer->ClearBccs();
  }

  public function clearReplyTos()
  {
    $this->mailer->ClearReplyTos();
  }

  public function clearAllRecipients()
  {
    $this->mailer->ClearAllRecipients();
  }

  public function addAttachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream')
  {
    $this->mailer->AddAttachment($path, $name, $encoding, $type);
  }

  public function addStringAttachment($string, $filename, $encoding = 'base64', $type = 'application/octet-stream')
  {
    $this->mailer->AddStringAttachment($string, $filename, $encoding, $type);
  }

  public function addEmbeddedImage($path, $cid, $name = '', $encoding = 'base64', $type = 'application/octet-stream')
  {
    $this->mailer->AddEmbeddedImage($path, $cid, $name, $encoding, $type);
  }

  public function setAttachments($attachments)
  {
    if ($attachments instanceof sfMailAttachments)
    {
      $this->mailer->setAttachments($attachments->getAttachments());
    }
  }

  public function clearAttachments()
  {
    $this->mailer->ClearAttachments();
  }

  function addCustomHeader($name, $value)
  {
    $this->mailer->AddCustomHeader("$name: $value");
  }

  function clearCustomHeaders()
  {
    $this->mailer->ClearCustomHeaders();
  }

  public function prepare()
  {
    // Set whether the message is multipart/alternative
    if (!empty($this->mailer->AltBody))
    {
      $this->mailer->ContentType = "multipart/alternative";
    }

    $this->mailer->SetMessageType();
  }

  public function send()
  {
    if (!$this->mailer->Send())
    {
      throw new sfException($this->mailer->ErrorInfo);
    }
  }

  public function smtpClose()
  {
    $this->mailer->SmtpClose();
  }
  
  public function getRawHeader()
  {
    return $this->mailer->CreateHeader();
  }

  public function getRawBody()
  {
    return $this->mailer->CreateBody();
  }

  public function setDomain($hostname)
  {
    $this->mailer->Hostname = $hostname;
  }

  public function getDomain()
  {
    return $this->mailer->Hostname;
  }

  public function setHostname($hostname)
  {
    $this->mailer->Host = $hostname;
  }

  public function getHostname()
  {
    return $this->mailer->Host;
  }

  public function setPort($port)
  {
    $this->mailer->Port = $port;
  }

  public function getPort()
  {
    return $this->mailer->Port;
  }

  public function setUsername($username)
  {
    $this->mailer->Username = $username;
    $this->mailer->SMTPAuth = $username ? true : false;
  }

  public function getUsername()
  {
    return $this->mailer->Username;
  }

  public function setPassword($password)
  {
    $this->mailer->Password = $password;
  }

  public function getPassword()
  {
    return $this->mailer->Password;
  }

  public function setWordWrap($wordWrap)
  {
    $this->mailer->WordWrap = $wordWrap;
  }

  public function getWordWrap()
  {
    return $this->mailer->WordWrap;
  }

  public function setAltBody($text)
  {
    $this->mailer->AltBody = $text;
  }

  public function getAltBody()
  {
    return $this->mailer->AltBody;
  }
}
