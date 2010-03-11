<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebugPanelPropel adds a panel to the web debug toolbar with Propel information.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebDebugPanelPropel.class.php 17296 2009-04-14 15:27:30Z fabien $
 */
class sfWebDebugPanelPropel extends sfWebDebugPanel
{
  /**
   * Constructor.
   *
   * @param sfWebDebug $webDebug The web debut toolbar instance
   */
  public function __construct(sfWebDebug $webDebug)
  {
    parent::__construct($webDebug);

    $this->webDebug->getEventDispatcher()->connect('debug.web.filter_logs', array($this, 'filterLogs'));
  }

  public function getTitle()
  {
    if ($sqlLogs = $this->getSqlLogs())
    {
      return '<img src="'.$this->webDebug->getOption('image_root_path').'/database.png" alt="SQL queries" /> '.count($sqlLogs);
    }
  }

  public function getPanelTitle()
  {
    return 'SQL queries';
  }

  public function getPanelContent()
  {
    $logs = array();
    foreach ($this->getSqlLogs() as $log)
    {
      $logs[] = htmlspecialchars($log, ENT_QUOTES, sfConfig::get('sf_charset'));
    }

    return '
      <div id="sfWebDebugDatabaseLogs">
      <ol><li>'.implode("</li>\n<li>", $logs).'</li></ol>
      </div>
    ';
  }

  public function filterLogs(sfEvent $event, $logs)
  {
    $newLogs = array();
    foreach ($logs as $log)
    {
      if ('sfPropelLogger' != $log['type'])
      {
        $newLogs[] = $log;
      }
    }

    return $newLogs;
  }

  static public function listenToAddPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('db', new self($event->getSubject()));
  }

  protected function getSqlLogs()
  {
    $logs = array();
    $bindings = array();
    $i = 0;
    foreach ($this->webDebug->getLogger()->getLogs() as $log)
    {
      if ('sfPropelLogger' != $log['type'])
      {
        continue;
      }

      if (preg_match('/^(?:prepare|exec|query): (.*)$/s', $log['message'], $match))
      {
        $logs[$i++] = $match[1];
        $bindings[$i - 1] = array();
      }
      else if (preg_match('/Binding (.*) at position (.+?) w\//', $log['message'], $match))
      {
        $bindings[$i - 1][$match[2]] = $match[1];
      }
    }

    foreach ($logs as $i => $log)
    {
      if (count($bindings[$i]))
      {
        $bindings[$i] = array_reverse($bindings[$i]);
        foreach ($bindings[$i] as $search => $replace)
        {
          $logs[$i] = str_replace($search, $replace, $logs[$i]);
        }
      }
    }

    return $logs;
  }
}
