<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Launches functional tests.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTestFunctionalTask.class.php 29415 2010-05-12 06:24:54Z fabien $
 */
class sfTestFunctionalTask extends sfTestBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('controller', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY, 'The controller name'),
    ));

    $this->addOptions(array(
      new sfCommandOption('xml', null, sfCommandOption::PARAMETER_REQUIRED, 'The file name for the JUnit compatible XML log file'),
    ));

    $this->namespace = 'test';
    $this->name = 'functional';
    $this->briefDescription = 'Launches functional tests';

    $this->detailedDescription = <<<EOF
The [test:functional|INFO] task launches functional tests for a
given application:

  [./symfony test:functional frontend|INFO]

The task launches all tests found in [test/functional/%application%|COMMENT].

If some tests fail, you can use the [--trace|COMMENT] option to have more
information about the failures:

  [./symfony test:functional frontend -t|INFO]

You can launch all functional tests for a specific controller by
giving a controller name:

  [./symfony test:functional frontend article|INFO]

You can also launch all functional tests for several controllers:

  [./symfony test:functional frontend article comment|INFO]

The task can output a JUnit compatible XML log file with the [--xml|COMMENT]
options:

  [./symfony test:functional --xml=log.xml|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $app = $arguments['application'];

    if (count($arguments['controller']))
    {
      $files = array();

      foreach ($arguments['controller'] as $controller)
      {
        $finder = sfFinder::type('file')->follow_link()->name(basename($controller).'Test.php');
        $files = array_merge($files, $finder->in(sfConfig::get('sf_test_dir').'/functional/'.$app.'/'.dirname($controller)));
      }

      if($allFiles = $this->filterTestFiles($files, $arguments, $options))
      {
        foreach ($allFiles as $file)
        {
          include($file);
        }
      }
      else
      {
        $this->logSection('functional', 'no controller found', null, 'ERROR');
      }
    }
    else
    {
      require_once dirname(__FILE__).'/sfLimeHarness.class.php';

      $h = new sfLimeHarness(array(
        'force_colors' => isset($options['color']) && $options['color'],
        'verbose'      => isset($options['trace']) && $options['trace'],
      ));
      $h->addPlugins(array_map(array($this->configuration, 'getPluginConfiguration'), $this->configuration->getPlugins()));
      $h->base_dir = sfConfig::get('sf_test_dir').'/functional/'.$app;

      // filter and register functional tests
      $finder = sfFinder::type('file')->follow_link()->name('*Test.php');
      $h->register($this->filterTestFiles($finder->in($h->base_dir), $arguments, $options));

      $ret = $h->run() ? 0 : 1;

      if ($options['xml'])
      {
        file_put_contents($options['xml'], $h->to_xml());
      }

      return $ret;
    }
  }
}