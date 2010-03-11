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
 * sfExecutionFilter is the last filter registered for each filter chain. This
 * filter does all action and view execution.
 *
 * @package    symfony
 * @subpackage filter
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfValidationExecutionFilter.class.php 11254 2008-08-30 12:20:19Z fabien $
 */
class sfValidationExecutionFilter extends sfFilter
{
  /**
   * Executes this filter.
   *
   * @param sfFilterChain The filter chain
   *
   * @throws <b>sfInitializeException</b> If an error occurs during view initialization.
   * @throws <b>sfViewException</b>       If an error occurs while executing the view.
   */
  public function execute($filterChain)
  {
    // get the current action instance
    $actionInstance = $this->context->getController()->getActionStack()->getLastEntry()->getActionInstance();

    // validate and execute the action
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $timer = sfTimerManager::getTimer(sprintf('Action "%s/%s"', $actionInstance->getModuleName(), $actionInstance->getActionName()));
    }

    $viewName = $this->handleAction($filterChain, $actionInstance);

    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $timer->addTime();
    }

    // execute and render the view
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $timer = sfTimerManager::getTimer(sprintf('View "%s" for "%s/%s"', $viewName, $actionInstance->getModuleName(), $actionInstance->getActionName()));
    }

    $this->handleView($filterChain, $actionInstance, $viewName);

    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $timer->addTime();
    }

    // execute the filter chain (needed if fill-in filter is activated by the validation system)
    $filterChain->execute();
  }

  /*
   * Handles the action.
   *
   * @param  sfFilterChain The current filter chain
   * @param  sfAction      An sfAction instance
   *
   * @return string        The view type
   */
  protected function handleAction($filterChain, $actionInstance)
  {
    $uri = $this->context->getRouting()->getCurrentInternalUri();

    if (sfConfig::get('sf_cache') && !is_null($uri) && $this->context->getViewCacheManager()->hasActionCache($uri))
    {
      // action in cache, so go to the view
      return sfView::SUCCESS;
    }

    return $this->validateAction($filterChain, $actionInstance) ? $this->executeAction($actionInstance) : $this->handleErrorAction($actionInstance);
  }

  /**
   * Validates an sfAction instance.
   *
   * @param  sfAction An sfAction instance
   *
   * @return boolean  True if the action is validated, false otherwise
   */
  protected function validateAction($filterChain, $actionInstance)
  {
    $moduleName = $actionInstance->getModuleName();
    $actionName = $actionInstance->getActionName();

    // set default validated status
    $validated = true;

    // the case of the first letter of the action is insignificant
    // get the current action validation configuration
    $validationConfigWithFirstLetterLower = strtolower(substr($actionName, 0, 1)).substr($actionName, 1).'.yml';
    $validationConfigWithFirstLetterUpper = ucfirst($actionName).'.yml';

    // determine $validateFile by testing both the uppercase and lowercase
    // types of validation configurations.
    $validateFile = null;
    if (!is_null($testValidateFile = $this->context->getConfigCache()->checkConfig('modules/'.$moduleName.'/validate/'.$validationConfigWithFirstLetterLower, true)))
    {
      $validateFile = $testValidateFile;
    }
    else if (!is_null($testValidateFile = $this->context->getConfigCache()->checkConfig('modules/'.$moduleName.'/validate/'.$validationConfigWithFirstLetterUpper, true)))
    {
      $validateFile = $testValidateFile;
    }

    // load validation configuration
    // do NOT use require_once
    if (!is_null($validateFile))
    {
      // create validator manager
      $validatorManager = new sfValidatorManager($this->context);

      require($validateFile);

      // process validators
      $validated = $validatorManager->execute();
    }

    // process manual validation
    $validateToRun = 'validate'.ucfirst($actionName);
    $manualValidated = method_exists($actionInstance, $validateToRun) ? $actionInstance->$validateToRun() : $actionInstance->validate();

    // action is validated if:
    // - all validation methods (manual and automatic) return true
    // - or automatic validation returns false but errors have been 'removed' by manual validation
    $validated = ($manualValidated && $validated) || ($manualValidated && !$validated && !$this->context->getRequest()->hasErrors());

    // register fill-in filter
    if (null !== ($parameters = $this->context->getRequest()->getAttribute('symfony.fillin')))
    {
      $this->registerFillInFilter($filterChain, $parameters);
    }

    if (!$validated && sfConfig::get('sf_logging_enabled'))
    {
      $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array('Action validation failed')));
    }

    return $validated;
  }

  /**
   * Executes the execute method of an action.
   *
   * @param  sfAction An sfAction instance
   *
   * @return string   The view type
   */
  protected function executeAction($actionInstance)
  {
    // execute the action
    $actionInstance->preExecute();
    $viewName = $actionInstance->execute($this->context->getRequest());
    $actionInstance->postExecute();

    return $viewName ? $viewName : sfView::SUCCESS;
  }

  /**
   * Executes the handleError method of an action.
   *
   * @param  sfAction An sfAction instance
   *
   * @return string   The view type
   */
  protected function handleErrorAction($actionInstance)
  {
    // validation failed
    $handleErrorToRun = 'handleError'.ucfirst($actionInstance->getActionName());
    $viewName = method_exists($actionInstance, $handleErrorToRun) ? $actionInstance->$handleErrorToRun() : $actionInstance->handleError();

    return $viewName ? $viewName : sfView::ERROR;
  }

  /**
   * Handles the view.
   *
   * @param  sfFilterChain The current filter chain
   * @param sfAction       An sfAction instance
   * @param string         The view name
   */
  protected function handleView($filterChain, $actionInstance, $viewName)
  {
    switch ($viewName)
    {
      case sfView::HEADER_ONLY:
        $this->context->getResponse()->setHeaderOnly(true);
        return;
      case sfView::NONE:
        return;
    }

    $this->executeView($actionInstance->getModuleName(), $actionInstance->getActionName(), $viewName, $actionInstance->getVarHolder()->getAll());
  }

  /**
   * Executes and renders the view.
   *
   * The behavior of this method depends on the controller render mode:
   *
   *   - sfView::NONE: Nothing happens.
   *   - sfView::RENDER_CLIENT: View data populates the response content.
   *   - sfView::RENDER_DATA: View data populates the data presentation variable.
   *
   * @param  string The module name
   * @param  string The action name
   * @param  string The view name
   * @param  array  An array of view attributes
   *
   * @return string The view data
   */
  protected function executeView($moduleName, $actionName, $viewName, $viewAttributes)
  {
    $controller = $this->context->getController();

    // get the view instance
    $view = $controller->getView($moduleName, $actionName, $viewName);

    // execute the view
    $view->execute();

    // pass attributes to the view
    $view->getAttributeHolder()->add($viewAttributes);

    // render the view
    switch ($controller->getRenderMode())
    {
      case sfView::RENDER_NONE:
        break;

      case sfView::RENDER_CLIENT:
        $viewData = $view->render();
        $this->context->getResponse()->setContent($viewData);
        break;

      case sfView::RENDER_VAR:
        $viewData = $view->render();
        $controller->getActionStack()->getLastEntry()->setPresentation($viewData);
        break;
    }
  }

  /**
   * Registers the fill in filter in the filter chain.
   *
   * @param sfFilterChain A sfFilterChain implementation instance
   * @param array         An array of parameters to pass to the fill in filter.
   */
  protected function registerFillInFilter($filterChain, $parameters)
  {
    // automatically register the fill in filter if it is not already loaded in the chain
    if (isset($parameters['enabled']) && $parameters['enabled'] && !$filterChain->hasFilter('sfFillInFormFilter'))
    {
      // register the fill in form filter
      $fillInFormFilter = new sfFillInFormFilter($this->context, isset($parameters['param']) ? $parameters['param'] : array());
      $filterChain->register($fillInFormFilter);
    }
  }
}
