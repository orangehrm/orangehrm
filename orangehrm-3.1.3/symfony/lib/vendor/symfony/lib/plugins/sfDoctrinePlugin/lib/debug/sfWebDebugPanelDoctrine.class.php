<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebugPanelDoctrine adds a panel to the web debug toolbar with Doctrine information.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfWebDebugPanelDoctrine.class.php 28999 2010-04-06 17:42:14Z Kris.Wallsmith $
 */
class sfWebDebugPanelDoctrine extends sfWebDebugPanel
{
  /**
   * Get the title/icon for the panel
   *
   * @return string $html
   */
  public function getTitle()
  {
    if ($events = $this->getDoctrineEvents())
    {
      return '<img src="'.$this->webDebug->getOption('image_root_path').'/database.png" alt="SQL queries" /> '.count($events);
    }
  }

  /**
   * Get the verbal title of the panel
   *
   * @return string $title
   */
  public function getPanelTitle()
  {
    return 'SQL queries';
  }

  /**
   * Get the html content of the panel
   *
   * @return string $html
   */
  public function getPanelContent()
  {
    return '
      <div id="sfWebDebugDatabaseLogs">
        <h3>Doctrine Version: '.Doctrine_Core::VERSION.'</h3>
        <ol>'.implode("\n", $this->getSqlLogs()).'</ol>
      </div>
    ';
  }

  /**
   * Listens to debug.web.load_panels and adds this panel.
   */
  static public function listenToAddPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('db', new self($event->getSubject()));
  }

  /**
   * Returns an array of Doctrine query events.
   * 
   * @return array
   */
  protected function getDoctrineEvents()
  {
    $databaseManager = sfContext::getInstance()->getDatabaseManager();

    $events = array();
    if ($databaseManager)
    {
      foreach ($databaseManager->getNames() as $name)
      {
        $database = $databaseManager->getDatabase($name);
        if ($database instanceof sfDoctrineDatabase && $profiler = $database->getProfiler())
        {
          foreach ($profiler->getQueryExecutionEvents() as $event)
          {
            $events[$event->getSequence()] = $event;
          }
        }
      }
    }

    // sequence events
    ksort($events);

    return $events;
  }

  /**
   * Builds the sql logs and returns them as an array.
   *
   * @return array
   */
  protected function getSqlLogs()
  {
    $logs = $this->webDebug->getLogger()->getLogs();

    $html = array();
    foreach ($this->getDoctrineEvents() as $i => $event)
    {
      $conn = $event->getInvoker() instanceof Doctrine_Connection ? $event->getInvoker() : $event->getInvoker()->getConnection();
      $params = sfDoctrineConnectionProfiler::fixParams($event->getParams());
      $query = $this->formatSql(htmlspecialchars($event->getQuery(), ENT_QUOTES, sfConfig::get('sf_charset')));

      // interpolate parameters
      foreach ($params as $param)
      {
        $param = htmlspecialchars($param, ENT_QUOTES, sfConfig::get('sf_charset'));
        $query = join(var_export(is_scalar($param) ? $param : (string) $param, true), explode('?', $query, 2));
      }

      // slow query
      if ($event->slowQuery && $this->getStatus() > sfLogger::NOTICE)
      {
        $this->setStatus(sfLogger::NOTICE);
      }

      // backtrace
      $backtrace = null;
      foreach ($logs as $i => $log)
      {
        if (!isset($log['debug_backtrace']) || !count($log['debug_backtrace']))
        {
          // backtrace disabled
          break;
        }

        if (false !== strpos($log['message'], $event->getQuery()))
        {
          // assume queries are being requested in order
          unset($logs[$i]);
          $backtrace = '&nbsp;'.$this->getToggleableDebugStack($log['debug_backtrace']);
          break;
        }
      }

      $html[] = sprintf('
        <li%s>
          <p class="sfWebDebugDatabaseQuery">%s</p>
          <div class="sfWebDebugDatabaseLogInfo">%ss, "%s" connection%s</div>
        </li>',
        $event->slowQuery ? ' class="sfWebDebugWarning"' : '',
        $query,
        number_format($event->getElapsedSecs(), 2),
        $conn->getName(),
        $backtrace
      );
    }

    return $html;
  }
}
