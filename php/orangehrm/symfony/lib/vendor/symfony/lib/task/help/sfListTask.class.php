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
 * @version    SVN: $Id: sfListTask.class.php 21428 2009-08-25 11:24:13Z fabien $
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

    $this->addOptions(array(
      new sfCommandOption('xml', null, sfCommandOption::PARAMETER_NONE, 'To output help as XML'),
    ));

    $this->briefDescription = 'Lists tasks';

    $this->detailedDescription = <<<EOF
The [list|INFO] task lists all tasks:

  [./symfony list|INFO]

You can also display the tasks for a specific namespace:

  [./symfony list test|INFO]

You can also output the information as XML by using the [--xml|COMMENT] option:

  [./symfony list --xml|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
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

    if ($options['xml'])
    {
      $this->outputAsXml($arguments['namespace'], $tasks);
    }
    else
    {
      $this->outputAsText($arguments['namespace'], $tasks);
    }
  }

  protected function outputAsText($namespace, $tasks)
  {
    $this->commandApplication->help();
    $this->log('');

    $width = 0;
    foreach ($tasks as $name => $task)
    {
      $width = strlen($task->getName()) > $width ? strlen($task->getName()) : $width;
    }
    $width += strlen($this->formatter->format('  ', 'INFO'));

    $messages = array();
    if ($namespace)
    {
      $messages[] = $this->formatter->format(sprintf("Available tasks for the \"%s\" namespace:", $namespace), 'COMMENT');
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
      if (!$namespace && $currentNamespace != $task->getNamespace())
      {
        $currentNamespace = $task->getNamespace();
        $messages[] = $this->formatter->format($task->getNamespace(), 'COMMENT');
      }

      $aliases = $task->getAliases() ? $this->formatter->format(' ('.implode(', ', $task->getAliases()).')', 'COMMENT') : '';

      $messages[] = sprintf("  %-${width}s %s%s", $this->formatter->format(':'.$task->getName(), 'INFO'), $task->getBriefDescription(), $aliases);
    }

    $this->log($messages);
  }

  protected function outputAsXml($namespace, $tasks)
  {
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;
    $dom->appendChild($symfonyXML = $dom->createElement('symfony'));

    $symfonyXML->appendChild($tasksXML = $dom->createElement('tasks'));

    if ($namespace)
    {
      $tasksXML->setAttribute('namespace', $namespace);
    }
    else
    {
      $symfonyXML->appendChild($namespacesXML = $dom->createElement('namespaces'));
    }

    // display tasks
    ksort($tasks);
    $currentNamespace = 'foobar';
    $namespaceArrayXML = array();
    foreach ($tasks as $name => $task)
    {
      if (!$namespace && $currentNamespace != $task->getNamespace())
      {
        $currentNamespace = $task->getNamespace();
        $namespacesXML->appendChild($namespaceArrayXML[$task->getNamespace()] = $dom->createElement('namespace'));

        $namespaceArrayXML[$task->getNamespace()]->setAttribute('id', $task->getNamespace() ? $task->getNamespace() : '_global');
      }

      if (!$namespace)
      {
        $namespaceArrayXML[$task->getNamespace()]->appendChild($taskXML = $dom->createElement('task'));
        $taskXML->appendChild($dom->createTextNode($task->getName()));
      }

      $taskXML = new DOMDocument('1.0', 'UTF-8');
      $taskXML->formatOutput = true;
      $taskXML->loadXML($task->asXml());
      $node = $taskXML->getElementsByTagName('task')->item(0);
      $node = $dom->importNode($node, true);

      $tasksXML->appendChild($node);
    }

    echo $dom->saveXml();
  }
}
