<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebugPanelMailer adds a panel to the web debug toolbar with sent emails.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebDebugPanelMailer.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfWebDebugPanelMailer extends sfWebDebugPanel
{
  protected $mailer = null;

  /**
   * Constructor.
   *
   * @param sfWebDebug $webDebug The web debug toolbar instance
   */
  public function __construct(sfWebDebug $webDebug)
  {
    parent::__construct($webDebug);

    $this->webDebug->getEventDispatcher()->connect('mailer.configure', array($this, 'listenForMailerConfigure'));
  }

  public function getTitle()
  {
    if ($this->mailer && ($logger = $this->mailer->getLogger()) && $logger->countMessages())
    {
      return '<img src="'.$this->webDebug->getOption('image_root_path').'/email.png" alt="Emailer" /> '.$logger->countMessages();
    }
  }

  public function getPanelTitle()
  {
    return 'Emails';
  }

  public function getPanelContent()
  {
    $logger = $this->mailer->getLogger();

    if (!$logger || !$messages = $logger->getMessages())
    {
      return false;
    }

    $html = array();

    // configuration information
    $strategy = $this->mailer->getDeliveryStrategy();
    $html[] = '<h2>Configuration</h2>';
    $html[] = '<em>Delivery strategy</em>: '.$strategy;

    if (sfMailer::SINGLE_ADDRESS == $strategy)
    {
      $html[] = ' - <em>all emails are delivered to</em>: '.$this->mailer->getDeliveryAddress();
    }

    // email sent
    $html[] = '<h2>Email sent</h2>';
    foreach ($messages as $message)
    {
      $html[] = $this->renderMessageInformation($message);
    }

    return implode("\n", $html);
  }

  protected function renderMessageInformation(Swift_Message $message)
  {
    static $i = 0;

    $i++;

    $to = null === $message->getTo() ? '' : implode(', ', array_keys($message->getTo()));

    $html = array();
    $html[] = sprintf('<h3>%s (to: %s) %s</h3>', $message->getSubject(), $to, $this->getToggler('sfWebDebugMailTemplate'.$i));
    $html[] = '<div id="sfWebDebugMailTemplate'.$i.'" style="display:'.(1 == $i ? 'block' : 'none').'">';
    $html[] = '<pre>'.htmlentities($message->toString(), ENT_QUOTES, $message->getCharset()).'</pre>';
    $html[] = '</div>';

    return implode("\n", $html);
  }

  /**
   * Listens for the mailer.configure event and captures a reference to the mailer.
   *
   * @param sfEvent $event
   */
  public function listenForMailerConfigure(sfEvent $event)
  {
    $this->mailer = $event->getSubject();
  }
}
