<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr <sean@code-box.org>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfBasicSecurityFilter checks security by calling the getCredential() method
 * of the action. Once the credential has been acquired, sfBasicSecurityFilter
 * verifies the user has the same credential by calling the hasCredential()
 * method of SecurityUser.
 *
 * @package    symfony
 * @subpackage filter
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfBasicSecurityFilter.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfBasicSecurityFilter extends sfFilter
{
  /**
   * Executes this filter.
   *
   * @param sfFilterChain $filterChain A sfFilterChain instance
   */
  public function execute($filterChain)
  {
    // disable security on login and secure actions
    if (
      (sfConfig::get('sf_login_module') == $this->context->getModuleName()) && (sfConfig::get('sf_login_action') == $this->context->getActionName())
      ||
      (sfConfig::get('sf_secure_module') == $this->context->getModuleName()) && (sfConfig::get('sf_secure_action') == $this->context->getActionName())
    )
    {
      $filterChain->execute();

      return;
    }

    // NOTE: the nice thing about the Action class is that getCredential()
    //       is vague enough to describe any level of security and can be
    //       used to retrieve such data and should never have to be altered
    if (!$this->context->getUser()->isAuthenticated())
    {
      if (sfConfig::get('sf_logging_enabled'))
      {
        $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('Action "%s/%s" requires authentication, forwarding to "%s/%s"', $this->context->getModuleName(), $this->context->getActionName(), sfConfig::get('sf_login_module'), sfConfig::get('sf_login_action')))));
      }
     
      // the user is not authenticated
      $this->forwardToLoginAction();
    }

    // the user is authenticated
    $credential = $this->getUserCredential();
    if (null !== $credential && !$this->context->getUser()->hasCredential($credential))
    { 
      if (sfConfig::get('sf_logging_enabled'))
      {
        $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('Action "%s/%s" requires credentials "%s", forwarding to "%s/%s"', $this->context->getModuleName(), $this->context->getActionName(), sfYaml::dump($credential, 0), sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action')))));
      }
    
      // the user doesn't have access
      $this->forwardToSecureAction();
    }

    // the user has access, continue
    $filterChain->execute();
  }

  /**
   * Forwards the current request to the secure action.
   *
   * @throws sfStopException
   */
  protected function forwardToSecureAction()
  {    
    $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

    throw new sfStopException();
  }

  /**
   * Forwards the current request to the login action.
   *
   * @throws sfStopException
   */
  protected function forwardToLoginAction()
  {
    $this->context->getController()->forward(sfConfig::get('sf_login_module'), sfConfig::get('sf_login_action'));

    throw new sfStopException();
  }

  /**
   * Returns the credential required for this action.
   *
   * @return mixed The credential required for this action
   */
  protected function getUserCredential()
  {
    return $this->context->getController()->getActionStack()->getLastEntry()->getActionInstance()->getCredential();
  }
}
