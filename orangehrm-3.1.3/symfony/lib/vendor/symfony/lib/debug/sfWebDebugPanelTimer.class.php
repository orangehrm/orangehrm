<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebugPanelTimer adds a panel to the web debug toolbar with timer information.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebDebugPanelTimer.class.php 22955 2009-10-12 16:44:07Z Kris.Wallsmith $
 */
class sfWebDebugPanelTimer extends sfWebDebugPanel
{
  static protected
    $startTime = null;

  /**
   * Constructor.
   *
   * @param sfWebDebug $webDebug The web debug toolbar instance
   */
  public function __construct(sfWebDebug $webDebug)
  {
    parent::__construct($webDebug);

    $this->webDebug->getEventDispatcher()->connect('debug.web.filter_logs', array($this, 'filterLogs'));
  }

  public function getTitle()
  {
    return '<img src="'.$this->webDebug->getOption('image_root_path').'/time.png" alt="Time" /> '.$this->getTotalTime().' ms';
  }

  public function getPanelTitle()
  {
    return 'Timers';
  }

  public function getPanelContent()
  {
    if (sfTimerManager::getTimers())
    {
      $totalTime = $this->getTotalTime();
      $panel = '<table class="sfWebDebugLogs" style="width: 300px"><tr><th>type</th><th>calls</th><th>time (ms)</th><th>time (%)</th></tr>';
      foreach (sfTimerManager::getTimers() as $name => $timer)
      {
        $panel .= sprintf('<tr><td class="sfWebDebugLogType">%s</td><td class="sfWebDebugLogNumber" style="text-align: right">%d</td><td style="text-align: right">%.2f</td><td style="text-align: right">%d</td></tr>', $name, $timer->getCalls(), $timer->getElapsedTime() * 1000, $totalTime ? ($timer->getElapsedTime() * 1000 * 100 / $totalTime) : 'N/A');
      }
      $panel .= '</table>';

      return $panel;
    }
  }

  public function filterLogs(sfEvent $event, $logs)
  {
    $newLogs = array();
    foreach ($logs as $log)
    {
      if ('sfWebDebugLogger' != $log['type'])
      {
        $newLogs[] = $log;
      }
    }

    return $newLogs;
  }

  static public function startTime()
  {
    self::$startTime = microtime(true);
  }

  static public function isStarted()
  {
    return null !== self::$startTime;
  }

  protected function getTotalTime()
  {
    return null !== self::$startTime ? sprintf('%.0f', (microtime(true) - self::$startTime) * 1000) : 0;
  }
}
