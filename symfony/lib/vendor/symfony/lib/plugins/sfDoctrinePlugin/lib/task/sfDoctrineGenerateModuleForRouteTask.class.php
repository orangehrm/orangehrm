<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfDoctrineBaseTask.class.php');

/**
 * Generates a Doctrine module for a route definition.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineGenerateModuleForRouteTask.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfDoctrineGenerateModuleForRouteTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('route', sfCommandArgument::REQUIRED, 'The route name'),
    ));

    $this->addOptions(array(
      new sfCommandOption('theme', null, sfCommandOption::PARAMETER_REQUIRED, 'The theme name', 'default'),
      new sfCommandOption('non-verbose-templates', null, sfCommandOption::PARAMETER_NONE, 'Generate non verbose templates'),
      new sfCommandOption('singular', null, sfCommandOption::PARAMETER_REQUIRED, 'The singular name', null),
      new sfCommandOption('plural', null, sfCommandOption::PARAMETER_REQUIRED, 'The plural name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('actions-base-class', null, sfCommandOption::PARAMETER_REQUIRED, 'The base class for the actions', 'sfActions'),
    ));

    $this->namespace = 'doctrine';
    $this->name = 'generate-module-for-route';
    $this->briefDescription = 'Generates a Doctrine module for a route definition';

    $this->detailedDescription = <<<EOF
The [doctrine:generate-module-for-route|INFO] task generates a Doctrine module for a route definition:

  [./symfony doctrine:generate-module-for-route frontend article|INFO]

The task creates a module in the [%frontend%|COMMENT] application for the
[%article%|COMMENT] route definition found in [routing.yml|COMMENT].
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // get configuration for the given route
    $config = new sfRoutingConfigHandler();
    $routes = $config->evaluate($this->configuration->getConfigPaths('config/routing.yml'));

    if (!isset($routes[$arguments['route']]))
    {
      throw new sfCommandException(sprintf('The route "%s" does not exist.', $arguments['route']));
    }

    $routeOptions = $routes[$arguments['route']]->getOptions();

    if (!$routes[$arguments['route']] instanceof sfDoctrineRouteCollection)
    {
      throw new sfCommandException(sprintf('The route "%s" is not a Doctrine collection route.', $arguments['route']));
    }

    $module = $routeOptions['module'];
    $model = $routeOptions['model'];

    // execute the doctrine:generate-module task
    $task = new sfDoctrineGenerateModuleTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->setConfiguration($this->configuration);

    $this->logSection('app', sprintf('Generating module "%s" for model "%s"', $module, $model));

    return $task->run(array($arguments['application'], $module, $model), array(
      'theme'                 => $options['theme'],
      'route-prefix'          => $routeOptions['name'],
      'with-doctrine-route'   => true,
      'with-show'             => $routeOptions['with_show'],
      'non-verbose-templates' => $options['non-verbose-templates'],
      'singular'              => $options['singular'],
      'plural'                => $options['plural'],
      'actions-base-class'    => $options['actions-base-class'],
    ));
  }
}