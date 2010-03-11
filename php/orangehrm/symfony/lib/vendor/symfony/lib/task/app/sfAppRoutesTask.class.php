<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Displays the current routes for an application.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfAppRoutesTask.class.php 12806 2008-11-09 08:07:57Z fabien $
 */
class sfAppRoutesTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('name', sfCommandArgument::OPTIONAL, 'A route name'),
    ));

    $this->namespace = 'app';
    $this->name = 'routes';
    $this->briefDescription = 'Displays current routes for an application';

    $this->detailedDescription = <<<EOF
The [app:routes|INFO] displays the current routes for a given application:

  [./symfony app:routes frontend|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // get routes
    $config = new sfRoutingConfigHandler();
    $routes = $config->evaluate($this->configuration->getConfigPaths('config/routing.yml'));

    $routing = new sfPatternRouting($this->dispatcher);
    $routing->setRoutes($routes);

    $this->dispatcher->notify(new sfEvent($routing, 'routing.load_configuration'));

    $this->routes = $routing->getRoutes();

    // display
    $arguments['name'] ? $this->outputRoute($arguments['application'], $arguments['name']) : $this->outputRoutes($arguments['application']);
  }

  protected function outputRoutes($application)
  {
    $this->logSection('app', sprintf('Current routes for application "%s"', $application));

    $maxName = 4;
    $maxMethod = 6;
    foreach ($this->routes as $name => $route)
    {
      $requirements = $route->getRequirements();
      $method = isset($requirements['sf_method']) ? strtoupper(is_array($requirements['sf_method']) ? implode(', ', $requirements['sf_method']) : $requirements['sf_method']) : 'ANY';

      if (strlen($name) > $maxName)
      {
        $maxName = strlen($name);
      }

      if (strlen($method) > $maxMethod)
      {
        $maxMethod = strlen($method);
      }
    }
    $format  = '%-'.$maxName.'s %-'.$maxMethod.'s %s';

    // displays the generated routes
    $format1  = '%-'.($maxName + 9).'s %-'.($maxMethod + 9).'s %s';
    $this->log(sprintf($format1, $this->formatter->format('Name', 'COMMENT'), $this->formatter->format('Method', 'COMMENT'), $this->formatter->format('Pattern', 'COMMENT')));
    foreach ($this->routes as $name => $route)
    {
      $requirements = $route->getRequirements();
      $method = isset($requirements['sf_method']) ? strtoupper(is_array($requirements['sf_method']) ? implode(', ', $requirements['sf_method']) : $requirements['sf_method']) : 'ANY';
      $this->log(sprintf($format, $name, $method, $route->getPattern()));
    }
  }

  protected function outputRoute($application, $name)
  {
    $this->logSection('app', sprintf('Route "%s" for application "%s"', $name, $application));

    if (!isset($this->routes[$name]))
    {
      throw new sfCommandException(sprintf('The route "%s" does not exist.', $name));
    }

    $route = $this->routes[$name];
    $this->log(sprintf('%s         %s', $this->formatter->format('Name', 'COMMENT'), $name));
    $this->log(sprintf('%s      %s', $this->formatter->format('Pattern', 'COMMENT'), $route->getPattern()));
    $this->log(sprintf('%s        %s', $this->formatter->format('Class', 'COMMENT'), get_class($route)));

    $defaults = '';
    $d = $route->getDefaults();
    ksort($d);
    foreach ($d as $name => $value)
    {
      $defaults .= ($defaults ? "\n".str_repeat(' ', 13) : '').$name.': '.$this->arrayToString($value);
    }
    $this->log(sprintf('%s     %s', $this->formatter->format('Defaults', 'COMMENT'), $defaults));

    $requirements = '';
    $r = $route->getRequirements();
    ksort($r);
    foreach ($r as $name => $value)
    {
      $requirements .= ($requirements ? "\n".str_repeat(' ', 13) : '').$name.': '.$this->arrayToString($value);
    }
    $this->log(sprintf('%s %s', $this->formatter->format('Requirements', 'COMMENT'), $requirements));

    $options = '';
    $o = $route->getOptions();
    ksort($o);
    foreach ($o as $name => $value)
    {
      $options .= ($options ? "\n".str_repeat(' ', 13) : '').$name.': '.$this->arrayToString($value);
    }
    $this->log(sprintf('%s      %s', $this->formatter->format('Options', 'COMMENT'), $options));
    $this->log(sprintf('%s        %s', $this->formatter->format('Regex', 'COMMENT'), preg_replace('/^             /', '', preg_replace('/^/m', '             ', $route->getRegex()))));

    $tokens = '';
    foreach ($route->getTokens() as $token)
    {
      if (!$tokens)
      {
        $tokens = $this->displayToken($token);
      }
      else
      {
        $tokens .= "\n".str_repeat(' ', 13).$this->displayToken($token);
      }
    }
    $this->log(sprintf('%s       %s', $this->formatter->format('Tokens', 'COMMENT'), $tokens));
  }

  protected function displayToken($token)
  {
    $type = array_shift($token);
    array_shift($token);

    return sprintf('%-10s %s', $type, $this->arrayToString($token));
  }

  protected function arrayToString($array)
  {
    return preg_replace("/\n\s*/s", '', var_export($array, true));
  }
}
