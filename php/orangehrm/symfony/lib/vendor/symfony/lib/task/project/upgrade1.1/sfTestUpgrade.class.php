<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrade tests.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTestUpgrade.class.php 7962 2008-03-19 10:21:16Z fabien $
 */
class sfTestUpgrade extends sfUpgrade
{
  public function upgrade()
  {
    // upgrade test bootstrap files
    $unit = sfConfig::get('sf_test_dir').'/bootstrap/unit.php';
    if (file_exists($unit))
    {
      $content = file_get_contents($unit);
      if (false !== strpos($content, 'SF_ROOT_DIR') || false !== strpos($content, 'lib/ProjectConfiguration'))
      {
        $this->logSection('test', sprintf('Migrating %s', $unit));
        file_put_contents($unit, file_get_contents(dirname(__FILE__).'/../../generator/skeleton/project/test/bootstrap/unit.php'));
      }
    }

    $functional = sfConfig::get('sf_test_dir').'/bootstrap/functional.php';
    if (file_exists($functional))
    {
      $content = file_get_contents($functional);
      if (false !== strpos($content, 'SF_ROOT_DIR') || false !== strpos($content, 'new $class'))
      {
        $this->logSection('test', sprintf('Migrating %s', $functional));
        file_put_contents($functional, file_get_contents(dirname(__FILE__).'/../../generator/skeleton/project/test/bootstrap/functional.php'));
      }
    }
  }
}
