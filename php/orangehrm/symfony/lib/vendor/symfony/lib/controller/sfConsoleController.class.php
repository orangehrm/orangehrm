<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    symfony
 * @subpackage controller
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfConsoleController.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class sfConsoleController extends sfController
{
  /**
   * Dispatches a request.
   *
   * @param string $moduleName A module name
   * @param string $actionName An action name
   * @param array  $parameters An associative array of parameters to be set
   */
  public function dispatch($moduleName, $actionName, $parameters = array())
  {
    try
    {
      // set parameters
      $this->context->getRequest()->getParameterHolder()->add($parameters);

      // make the first request
      $this->forward($moduleName, $actionName);
    }
    catch (sfException $e)
    {
      $e->printStackTrace();
    }
    catch (Exception $e)
    {
      sfException::createFromException($e)->printStackTrace();
    }
  }
}
