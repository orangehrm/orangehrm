<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
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
 * @version    SVN: $Id: sfTestUnitTask.class.php 18136 2009-05-11 11:57:32Z fabien $
 */
class sfTestUnitTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('name', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY, 'The test name'),
    ));

    $this->aliases = array('test-unit');
    $this->namespace = 'test';
    $this->name = 'unit';
    $this->briefDescription = 'Launches unit tests';

    $this->detailedDescription = <<<EOF
The [test:unit|INFO] task launches unit tests:

  [./symfony test:unit|INFO]

The task launches all tests found in [test/unit|COMMENT].

You can launch unit tests for a specific name:

  [./symfony test:unit strtolower|INFO]

You can also launch unit tests for several names:

  [./symfony test:unit strtolower strtoupper|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (count($arguments['name']))
    {
      foreach ($arguments['name'] as $name)
      {
        $files = sfFinder::type('file')->follow_link()->name(basename($name).'Test.php')->in(sfConfig::get('sf_test_dir').DIRECTORY_SEPARATOR.'unit'.DIRECTORY_SEPARATOR.dirname($name));
        foreach ($files as $file)
        {
          include($file);
        }
      }
    }
    else
    {
      require_once(sfConfig::get('sf_symfony_lib_dir').'/vendor/lime/lime.php');

      $h = new lime_harness(new lime_output_color());
      $h->base_dir = sfConfig::get('sf_test_dir').'/unit';

      // register unit tests
      $finder = sfFinder::type('file')->follow_link()->name('*Test.php');
      $h->register($finder->in($h->base_dir));

      return $h->run() ? 0 : 1;
    }
  }
}
