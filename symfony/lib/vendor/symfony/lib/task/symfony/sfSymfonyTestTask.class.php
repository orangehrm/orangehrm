<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Launches the symfony test suite.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfSymfonyTestTask.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfSymfonyTestTask extends sfTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('update-autoloader', 'u', sfCommandOption::PARAMETER_NONE, 'Update the sfCoreAutoload class'),
      new sfCommandOption('only-failed', 'f', sfCommandOption::PARAMETER_NONE, 'Only run tests that failed last time'),
      new sfCommandOption('xml', null, sfCommandOption::PARAMETER_REQUIRED, 'The file name for the JUnit compatible XML log file'),
      new sfCommandOption('rebuild-all', null, sfCommandOption::PARAMETER_NONE, 'Rebuild all generated fixture files'),
    ));

    $this->namespace = 'symfony';
    $this->name = 'test';
    $this->briefDescription = 'Launches the symfony test suite';

    $this->detailedDescription = <<<EOF
The [test:all|INFO] task launches the symfony test suite:

  [./symfony symfony:test|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    require_once(dirname(__FILE__).'/../../vendor/lime/lime.php');
    require_once(dirname(__FILE__).'/lime_symfony.php');

    // cleanup
    require_once(dirname(__FILE__).'/../../util/sfToolkit.class.php');
    if ($files = glob(sys_get_temp_dir().DIRECTORY_SEPARATOR.'/sf_autoload_unit_*'))
    {
      foreach ($files as $file)
      {
        unlink($file);
      }
    }

    // update sfCoreAutoload
    if ($options['update-autoloader'])
    {
      require_once(dirname(__FILE__).'/../../autoload/sfCoreAutoload.class.php');
      sfCoreAutoload::make();
    }

    $status = false;
    $statusFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.sprintf('/.test_symfony_%s_status', md5(dirname(__FILE__)));
    if ($options['only-failed'])
    {
      if (file_exists($statusFile))
      {
        $status = unserialize(file_get_contents($statusFile));
      }
    }

    $h = new lime_symfony(array('force_colors' => $options['color'], 'verbose' => $options['trace']));
    $h->base_dir = realpath(dirname(__FILE__).'/../../../test');

    // remove generated files
    if ($options['rebuild-all'])
    {
      $finder = sfFinder::type('dir')->name(array('base', 'om', 'map'));
      foreach ($finder->in(glob($h->base_dir.'/../lib/plugins/*/test/functional/fixtures/lib')) as $dir)
      {
        sfToolkit::clearDirectory($dir);
      }
    }

    if ($status)
    {
      foreach ($status as $file)
      {
        $h->register($file);
      }
    }
    else
    {
      $h->register(sfFinder::type('file')->prune('fixtures')->name('*Test.php')->in(array_merge(
        // unit tests
        array($h->base_dir.'/unit'),
        glob($h->base_dir.'/../lib/plugins/*/test/unit'),

        // functional tests
        array($h->base_dir.'/functional'),
        glob($h->base_dir.'/../lib/plugins/*/test/functional'),

        // other tests
        array($h->base_dir.'/other')
      )));
    }

    $ret = $h->run() ? 0 : 1;

    file_put_contents($statusFile, serialize($h->get_failed_files()));

    if ($options['xml'])
    {
      file_put_contents($options['xml'], $h->to_xml());
    }

    return $ret;
  }
}
