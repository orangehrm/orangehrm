<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebugPanelLogs adds a panel to the web debug toolbar with log messages.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebDebugPanelLogs.class.php 22182 2009-09-19 18:51:53Z Kris.Wallsmith $
 */
class sfWebDebugPanelLogs extends sfWebDebugPanel
{
  public function getTitle()
  {
    return '<img src="'.$this->webDebug->getOption('image_root_path').'/log.png" alt="Log" /> logs';
  }

  public function getPanelTitle()
  {
    return 'Logs';
  }

  public function getPanelContent()
  {
    $event = $this->webDebug->getEventDispatcher()->filter(new sfEvent($this, 'debug.web.filter_logs'), $this->webDebug->getLogger()->getLogs());
    $logs = $event->getReturnValue();

    $html = '<table class="sfWebDebugLogs">
      <tr>
        <th>#</th>
        <th>type</th>
        <th>message</th>
      </tr>'."\n";
    $line_nb = 0;
    foreach ($logs as $log)
    {
      $priority = $this->webDebug->getPriority($log['priority']);

      // increase status
      if ($log['priority'] < $this->getStatus())
      {
        $this->setStatus($log['priority']);
      }

      ++$line_nb;
      $html .= sprintf("<tr class='sfWebDebugLogLine sfWebDebug%s %s'><td class=\"sfWebDebugLogNumber\">%s</td><td class=\"sfWebDebugLogType\">%s&nbsp;%s</td><td>%s %s</td></tr>\n",
        ucfirst($priority),
        $log['type'],
        $line_nb,
        '<img src="'.$this->webDebug->getOption('image_root_path').'/'.$priority.'.png" alt="'.ucfirst($priority).'"/>',
        class_exists($log['type'], false) ? $this->formatFileLink($log['type']) : $log['type'],
        $this->formatLogLine($log['message']),
        $this->getToggleableDebugStack($log['debug_backtrace'])
      );
    }
    $html .= '</table>';

    $types = array();
    foreach ($this->webDebug->getLogger()->getTypes() as $type)
    {
      $types[] = '<a href="#" onclick="sfWebDebugToggleMessages(\''.$type.'\'); return false;">'.$type.'</a>';
    }

    return '
      <ul id="sfWebDebugLogMenu">
        <li><a href="#" onclick="sfWebDebugToggleAllLogLines(true, \'sfWebDebugLogLine\'); return false;">[all]</a></li>
        <li><a href="#" onclick="sfWebDebugToggleAllLogLines(false, \'sfWebDebugLogLine\'); return false;">[none]</a></li>
        <li><a href="#" onclick="sfWebDebugShowOnlyLogLines(\'info\'); return false;"><img src="'.$this->webDebug->getOption('image_root_path').'/info.png" alt="Show only infos" /></a></li>
        <li><a href="#" onclick="sfWebDebugShowOnlyLogLines(\'warning\'); return false;"><img src="'.$this->webDebug->getOption('image_root_path').'/warning.png" alt="Show only warnings" /></a></li>
        <li><a href="#" onclick="sfWebDebugShowOnlyLogLines(\'error\'); return false;"><img src="'.$this->webDebug->getOption('image_root_path').'/error.png" alt="Show only errors" /></a></li>
        <li>'.implode("</li>\n<li>", $types).'</li>
      </ul>
      <div id="sfWebDebugLogLines">'.$html.'</div>
    ';
  }

  /**
   * Formats a log line.
   *
   * @param string $logLine The log line to format
   *
   * @return string The formatted log lin
   */
  protected function formatLogLine($logLine)
  {
    static $constants;

    if (!$constants)
    {
      foreach (array('sf_app_dir', 'sf_root_dir', 'sf_symfony_lib_dir') as $constant)
      {
        $constants[realpath(sfConfig::get($constant)).DIRECTORY_SEPARATOR] = $constant.DIRECTORY_SEPARATOR;
      }
    }

    // escape HTML
    $logLine = htmlspecialchars($logLine, ENT_QUOTES, sfConfig::get('sf_charset'));

    // replace constants value with constant name
    $logLine = str_replace(array_keys($constants), array_values($constants), $logLine);

    $logLine = sfToolkit::pregtr($logLine, array('/&quot;(.+?)&quot;/s' => '"<span class="sfWebDebugLogInfo">\\1</span>"',
                                                   '/^(.+?)\(\)\:/S'      => '<span class="sfWebDebugLogInfo">\\1()</span>:',
                                                   '/line (\d+)$/'        => 'line <span class="sfWebDebugLogInfo">\\1</span>'));

    // special formatting for SQL lines
    $logLine = $this->formatSql($logLine);

    // remove username/password from DSN
    if (strpos($logLine, 'DSN') !== false)
    {
      $logLine = preg_replace("/=&gt;\s+'?[^'\s,]+'?/", "=&gt; '****'", $logLine);
    }

    return $logLine;
  }
}
