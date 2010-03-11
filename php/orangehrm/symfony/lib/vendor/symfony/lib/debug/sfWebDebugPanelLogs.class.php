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
 * @version    SVN: $Id: sfWebDebugPanelLogs.class.php 12982 2008-11-13 17:25:10Z hartym $
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

      if (strpos($type = $log['type'], 'sf') === 0)
      {
        $type = substr($type, 2);
      }

      // xdebug information
      $debug_info = '';
      if (count($log['debug_stack']))
      {
        $debug_info .= '&nbsp;<a href="#" onclick="sfWebDebugToggle(\'debug_'.$line_nb.'\'); return false;"><img src="'.$this->webDebug->getOption('image_root_path').'/toggle.gif" alt="Toggle XDebug details" /></a><div class="sfWebDebugDebugInfo" id="debug_'.$line_nb.'" style="display:none">';
        foreach ($log['debug_stack'] as $i => $logLine)
        {
          $debug_info .= '#'.$i.' &raquo; '.$this->formatLogLine($logLine).'<br/>';
        }
        $debug_info .= "</div>\n";
      }

      ++$line_nb;
      $html .= sprintf("<tr class='sfWebDebugLogLine sfWebDebug%s %s'><td class=\"sfWebDebugLogNumber\">%s</td><td class=\"sfWebDebugLogType\">%s&nbsp;%s</td><td>%s%s</td></tr>\n",
        ucfirst($priority),
        $log['type'],
        $line_nb,
        '<img src="'.$this->webDebug->getOption('image_root_path').'/'.$priority.'.png" alt="'.ucfirst($priority).'"/>',
        $type,
        $this->formatLogLine($log['message']),
        $debug_info
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
    $logLine = preg_replace('/\b(SELECT|FROM|AS|LIMIT|ASC|COUNT|DESC|WHERE|LEFT JOIN|INNER JOIN|RIGHT JOIN|ORDER BY|GROUP BY|IN|LIKE|DISTINCT|DELETE|INSERT|INTO|VALUES)\b/', '<span class="sfWebDebugLogInfo">\\1</span>', $logLine);

    // remove username/password from DSN
    if (strpos($logLine, 'DSN') !== false)
    {
      $logLine = preg_replace("/=&gt;\s+'?[^'\s,]+'?/", "=&gt; '****'", $logLine);
    }

    return $logLine;
  }
}
