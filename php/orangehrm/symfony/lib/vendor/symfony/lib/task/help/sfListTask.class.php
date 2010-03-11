<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Lists tasks.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfListTask.class.php 11505 2008-09-13 09:22:23Z fabien $
 */
class sfListTask extends sfCommandApplicationTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('namespace', sfCommandArgument::OPTIONAL, 'The namespace name'),
    ));

    $this->briefDescription = 'Lists tasks';

    $this->detailedDescription = <<<EOF
The [list|INFO] task lists all tasks:

  [./symfony list|INFO]

You can also display the tasks for a specific namespace:

  [./symfony list test|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->commandApplication->help();
    $this->log('');

    $tasks = array();
    foreach ($this->commandApplication->getTasks() as $name => $task)
    {
      if ($arguments['namespace'] && $arguments['namespace'] != $task->getNamespace())
      {
        continue;
      }

      if ($name != $task->getFullName())
      {
        // it is an alias
        continue;
      }

      if (!$task->getNamespace())
      {
        $name = '_default:'.$name;
      }

      $tasks[$name] = $task;
    }

    $width = 0;
    foreach ($tasks as $name => $task)
    {
      $width = strlen($task->getName()) > $width ? strlen($task->getName()) : $width;
    }
    $width += strlen($this->formatter->format('  ', 'INFO'));

    $messages = array();
    if ($arguments['namespace'])
    {
      $messages[] = $this->formatter->format(sprintf("Available tasks for the \"%s\" namespace:", $arguments['namespace']), 'COMMENT');
    }
    else
    {
      $messages[] = $this->formatter->format('Available tasks:', 'COMMENT');
    }

    // display tasks
    ksort($tasks);
    $currentNamespace = '';
    foreach ($tasks as $name => $task)
    {
      if (!$arguments['namespace'] && $currentNamespace != $task->getNamespace())
      {
        $currentNamespace = $task->getNamespace();
        $messages[] = $this->formatter->format($task->getNamespace(), 'COMMENT');
      }

      $aliases = $task->getAliases() ? $this->formatter->format(' ('.implode(', ', $task->getAliases()).')', 'COMMENT') : '';

      $messages[] = sprintf("  %-${width}s %s%s", $this->formatter->format(':'.$task->getName(), 'INFO'), $task->getBriefDescription(), $aliases);
    }

    $this->log($messages);
  }
}
