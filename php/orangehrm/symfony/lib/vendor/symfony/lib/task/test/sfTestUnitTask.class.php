<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Launches unit tests.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTestUnitTask.class.php 29415 2010-05-12 06:24:54Z fabien $
 */
class sfTestUnitTask extends sfTestBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('name', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY, 'The test name'),
    ));

    $this->addOptions(array(
      new sfCommandOption('xml', null, sfCommandOption::PARAMETER_REQUIRED, 'The file name for the JUnit compatible XML log file'),
    ));

    $this->namespace = 'test';
    $this->name = 'unit';
    $this->briefDescription = 'Launches unit tests';

    $this->detailedDescription = <<<EOF
The [test:unit|INFO] task launches unit tests:

  [./symfony test:unit|INFO]

The task launches all tests found in [test/unit|COMMENT].

If some tests fail, you can use the [--trace|COMMENT] option to have more
information about the failures:

  [./symfony test:unit -t|INFO]

You can launch unit tests for a specific name:

  [./symfony test:unit strtolower|INFO]

You can also launch unit tests for several names:

  [./symfony test:unit strtolower strtoupper|INFO]

The task can output a JUnit compatible XML log file with the [--xml|COMMENT]
options:

  [./symfony test:unit --xml=log.xml|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (count($arguments['name']))
    {
      $files = array();

      foreach ($arguments['name'] as $name)
      {
        $finder = sfFinder::type('file')->follow_link()->name(basename($name).'Test.php');
        $files = array_merge($files, $finder->in(sfConfig::get('sf_test_dir').'/unit/'.dirname($name)));
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
        $this->logSection('test', 'no tests found', null, 'ERROR');
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
      $h->base_dir = sfConfig::get('sf_test_dir').'/unit';

      // filter and register unit tests
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