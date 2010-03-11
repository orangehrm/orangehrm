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
 * @version    SVN: $Id: sfWebDebugPanelDoctrine.class.php 11205 2008-08-27 16:24:17Z fabien $
 */
class sfWebDebugPanelDoctrine extends sfWebDebugPanel
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

  /**
   * Get the title/icon for the panel
   *
   * @return string $html
   */
  public function getTitle()
  {
    if ($sqlLogs = $this->getSqlLogs())
    {
      return '<img src="'.$this->webDebug->getOption('image_root_path').'/database.png" alt="SQL queries" /> '.count($sqlLogs);
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
      <ol><li>'.implode("</li>\n<li>", $this->getSqlLogs()).'</li></ol>
      </div>
    ';
  }
  
  /**
   * Filter the logs to only include the entries from sfDoctrineLogger
   *
   * @param sfEvent $event
   * @param array $Logs
   * @return array $newLogs
   */
  public function filterLogs(sfEvent $event, $newSqlogs)
  {
    $newLogs = array();
    foreach ($newSqlogs as $newSqlog)
    {
      if ('sfDoctrineLogger' != $newSqlog['type'])
      {
        $newLogs[] = $newSqlog;
      }
    }

    return $newLogs;
  }

  /**
   * Hook to allow the loading of the Doctrine webdebug toolbar with the rest of the panels
   *
   * @param sfEvent $event 
   * @return void
   */
  static public function listenToAddPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('db', new self($event->getSubject()));
  }

  /**
   * Build the sql logs and return them as an array
   *
   * @return array $newSqlogs
   */
  protected function getSqlLogs()
  {
    $logs = array();
    $bindings = array();
    $i = 0;
    foreach ($this->webDebug->getLogger()->getLogs() as $log)
    {
      if ('sfDoctrineLogger' != $log['type'])
      {
        continue;
      }

      if (preg_match('/^.*?(\b(?:SELECT|INSERT|UPDATE|DELETE)\b.*)$/', $log['message'], $match))
      {
        $logs[$i++] = self::formatSql($match[1]);
        $bindings[$i - 1] = array();
      }
      else if (preg_match('/Binding (.*) at position (.+?) w\//', $log['message'], $match))
      {
        $bindings[$i - 1][] = $match[2].' = '.$match[1];
      }
    }

    foreach ($logs as $i => $log)
    {
      if (count($bindings[$i]))
      {
        $logs[$i] .= sprintf(' (%s)', implode(', ', $bindings[$i]));
      }
    }

    return $logs;
  }

  /**
   * Format a SQL with some colors on SQL keywords to make it more readable
   *
   * @param  string $sql    SQL string to format
   * @return string $newSql The new formatted SQL string
   */
  static protected function formatSql($sql)
  {
    $color = "#990099";
    $newSql = $sql;
    $newSql = str_replace("SELECT ", "<span style=\"color: $color;\"><b>SELECT </b></span>  ",$newSql);
    $newSql = str_replace("FROM ", "<span style=\"color: $color;\"><b>FROM </b></span>",$newSql);
    $newSql = str_replace(" LEFT JOIN ", "<span style=\"color: $color;\"><b> LEFT JOIN </b></span>",$newSql);
    $newSql = str_replace(" INNER JOIN ", "<span style=\"color: $color;\"><b> INNER JOIN </b></span>",$newSql);
    $newSql = str_replace(" WHERE ", "<span style=\"color: $color;\"><b> WHERE </b></span>",$newSql);
    $newSql = str_replace(" GROUP BY ", "<span style=\"color: $color;\"><b> GROUP BY </b></span>",$newSql);
    $newSql = str_replace(" HAVING ", "<span style=\"color: $color;\"><b> HAVING </b></span>",$newSql);
    $newSql = str_replace(" AS ", "<span style=\"color: $color;\"><b> AS </b></span>  ",$newSql);
    $newSql = str_replace(" ON ", "<span style=\"color: $color;\"><b> ON </b></span>",$newSql);
    $newSql = str_replace(" ORDER BY ", "<span style=\"color: $color;\"><b> ORDER BY </b></span>",$newSql);
    $newSql = str_replace(" LIMIT ", "<span style=\"color: $color;\"><b> LIMIT </b></span>",$newSql);
    $newSql = str_replace(" OFFSET ", "<span style=\"color: $color;\"><b> OFFSET </b></span>",$newSql);

    return $newSql;
  }
}