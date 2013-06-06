<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base test task.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTestBaseTask.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class sfTestBaseTask extends sfBaseTask
{
  /**
   * Filters tests through the "task.test.filter_test_files" event.
   * 
   * @param  array $tests     An array of absolute test file paths
   * @param  array $arguments Current task arguments
   * @param  array $options   Current task options
   * 
   * @return array The filtered array of test files
   */
  protected function filterTestFiles($tests, $arguments, $options)
  {
    $event = new sfEvent($this, 'task.test.filter_test_files', array('arguments' => $arguments, 'options' => $options));

    $this->dispatcher->filter($event, $tests);

    return $event->getReturnValue();
  }
}
