<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
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
 * @version    SVN: $Id: sfTestFunctionalTask.class.php 18136 2009-05-11 11:57:32Z fabien $
 */
class sfTestFunctionalTask extends sfBaseTask
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

    $this->aliases = array('test-functional');
    $this->namespace = 'test';
    $this->name = 'functional';
    $this->briefDescription = 'Launches functional tests';

    $this->detailedDescription = <<<EOF
The [test:functional|INFO] task launches functional tests for a
given application:

  [./symfony test:functional frontend|INFO]

The task launches all tests found in [test/functional/%application%|COMMENT].

You can launch all functional tests for a specific controller by
giving a controller name:

  [./symfony test:functional frontend article|INFO]

You can also launch all functional tests for several controllers:

  [./symfony test:functional frontend article comment|INFO]
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
      foreach ($arguments['controller'] as $controller)
      {
        $files = sfFinder::type('file')->follow_link()->name(basename($controller).'Test.php')->in(sfConfig::get('sf_test_dir').DIRECTORY_SEPARATOR.'functional'.DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR.dirname($controller));
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
      $h->base_dir = sfConfig::get('sf_test_dir').'/functional/'.$app;

      // register functional tests
      $finder = sfFinder::type('file')->follow_link()->name('*Test.php');
      $h->register($finder->in($h->base_dir));

      return $h->run() ? 0 : 1;
    }
  }
}
