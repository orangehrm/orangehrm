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
 * @package    symfony
 * @subpackage view
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfMailView.class.php 7691 2008-02-29 16:56:22Z fabien $
 */
class sfMailView extends sfPHPView
{
  /**
   * Retrieves the template engine associated with this view.
   *
   * @return string sfMail
   */
  public function getEngine()
  {
    return 'sfMail';
  }

  /**
   * Configures template for this view.
   *
   * @throws <b>sfActionException</b> If the configure fails
   */
  public function configure()
  {
    // view.yml configure
    parent::configure();

    // require our configuration
    $moduleName = $this->moduleName;
    require($this->context->getConfigCache()->checkConfig('modules/'.$this->moduleName.'/config/mailer.yml'));
  }

  /**
   * Renders the presentation and send the email to the client.
   *
   * @return mixed Raw data of the mail
   */
  public function render()
  {
    $retval = null;

    // execute pre-render check
    $this->preRenderCheck();

    // get sfMail object from action
    $mail = $this->attributeHolder->get('mail');
    if (!$mail)
    {
      throw new sfActionException('You must define a sfMail object named $mail ($this->mail) in your action to be able to use a sfMailView.');
    }

    // render main template
    $template = $this->getDirectory().'/'.$this->getTemplate();
    $retval = $this->renderFile($template);

    // render main and alternate templates
    $all_template_dir  = dirname($template);
    $all_template_regex = preg_replace('/\\.php$/', '\..+\.php', basename($template));
    $all_templates = sfFinder::type('file')->name('/^'.$all_template_regex.'$/')->in($all_template_dir);
    $all_retvals = array();
    foreach ($all_templates as $templateFile)
    {
      if (preg_match('/\.([^.]+?)\.php$/', $templateFile, $matches))
      {
        $all_retvals[$matches[1]] = $this->renderFile($templateFile);
      }
    }

    // send email
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array('Send email to client')));
    }

    // configuration prefix
    $config_prefix = 'sf_mailer_'.strtolower($this->moduleName).'_';

    $vars = array(
      'mailer',
      'priority', 'content_type', 'charset', 'encoding', 'wordwrap',
      'hostname', 'port', 'domain', 'username', 'password'
    );
    $defaultMail = new sfMail();

    foreach ($vars as $var)
    {
      $setter = 'set'.sfInflector::camelize($var);
      $getter = 'get'.sfInflector::camelize($var);
      $value  = $mail->$getter() !== $defaultMail->$getter() ? $mail->$getter() : sfConfig::get($config_prefix.strtolower($var));
      $mail->$setter($value);
    }

    $mail->setBody($retval);

    // alternate bodies
    $i = 0;
    foreach ($all_retvals as $type => $retval)
    {
      if ($type == 'altbody' && !$mail->getAltBody())
      {
        $mail->setAltBody($retval);
      }
      else
      {
        ++$i;
        $mail->addStringAttachment($retval, 'file'.$i, 'base64', 'text/'.$type);
      }
    }

    // preparing email to be sent
    $mail->prepare();

    // send e-mail
    if (sfConfig::get($config_prefix.'deliver'))
    {
      $mail->send();
    }

    return $mail->getRawHeader().$mail->getRawBody();
  }
}
