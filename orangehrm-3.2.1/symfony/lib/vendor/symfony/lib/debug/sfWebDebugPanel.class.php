<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebugPanel represents a web debug panel.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebDebugPanel.class.php 27284 2010-01-28 18:34:57Z Kris.Wallsmith $
 */
abstract class sfWebDebugPanel
{
  protected
    $webDebug = null,
    $status   = sfLogger::INFO;

  /**
   * Constructor.
   *
   * @param sfWebDebug $webDebug The web debug toolbar instance
   */
  public function __construct(sfWebDebug $webDebug)
  {
    $this->webDebug = $webDebug;
  }

  /**
   * Gets the link URL for the link.
   *
   * @return string The URL link
   */
  public function getTitleUrl()
  {
  }

  /**
   * Gets the text for the link.
   *
   * @return string The link text
   */
  abstract public function getTitle();

  /**
   * Gets the title of the panel.
   *
   * @return string The panel title
   */
  abstract public function getPanelTitle();

  /**
   * Gets the panel HTML content.
   *
   * @return string The panel HTML content
   */
  abstract public function getPanelContent();

  /**
   * Returns the current status.
   * 
   * @return integer A {@link sfLogger} priority constant
   */
  public function getStatus()
  {
    return $this->status;
  }

  /**
   * Sets the current panel's status.
   * 
   * @param integer $status A {@link sfLogger} priority constant
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }

  /**
   * Returns a toggler element.
   * 
   * @param  string $element The value of an element's DOM id attribute
   * @param  string $title   A title attribute
   * 
   * @return string
   */
  public function getToggler($element, $title = 'Toggle details')
  {
    return '<a href="#" onclick="sfWebDebugToggle(\''.$element.'\'); return false;" title="'.$title.'"><img src="'.$this->webDebug->getOption('image_root_path').'/toggle.gif" alt="'.$title.'"/></a>';
  }

  /**
   * Returns a toggleable presentation of a debug stack.
   * 
   * @param  array $debugStack
   * 
   * @return string
   */
  public function getToggleableDebugStack($debugStack)
  {
    static $i = 1;

    if (!$debugStack)
    {
      return '';
    }

    $element = get_class($this).'Debug'.$i++;
    $keys = array_reverse(array_keys($debugStack));

    $html  = $this->getToggler($element, 'Toggle debug stack');
    $html .= '<div class="sfWebDebugDebugInfo" id="'.$element.'" style="display:none">';
    foreach ($debugStack as $j => $trace)
    {
      $file = isset($trace['file']) ? $trace['file'] : null;
      $line = isset($trace['line']) ? $trace['line'] : null;

      $isProjectFile = $file && 0 === strpos($file, sfConfig::get('sf_root_dir')) && !preg_match('/(cache|plugins|vendor)/', $file);

      $html .= sprintf('<span%s>#%s &raquo; ', $isProjectFile ? ' class="sfWebDebugHighlight"' : '', $keys[$j] + 1);

      if (isset($trace['function']))
      {
        $html .= sprintf('in <span class="sfWebDebugLogInfo">%s%s%s()</span> ',
          isset($trace['class']) ? $trace['class'] : '',
          isset($trace['type']) ? $trace['type'] : '',
          $trace['function']
        );
      }

      $html .= sprintf('from %s line %s', $this->formatFileLink($file, $line), $line);
      $html .= '</span><br/>';
    }
    $html .= "</div>\n";

    return $html;
  }

  /**
   * Formats a file link.
   * 
   * @param  string  $file A file path or class name
   * @param  integer $line
   * @param  string  $text Text to use for the link
   * 
   * @return string
   */
  public function formatFileLink($file, $line = null, $text = null)
  {
    // this method is called a lot so we avoid calling class_exists()
    if ($file && !sfToolkit::isPathAbsolute($file))
    {
      if (null === $text)
      {
        $text = $file;
      }

      // translate class to file name
      $r = new ReflectionClass($file);
      $file = $r->getFileName();
    }

    $shortFile = sfDebug::shortenFilePath($file);

    if ($linkFormat = sfConfig::get('sf_file_link_format', ini_get('xdebug.file_link_format')))
    {
      // return a link
      return sprintf(
        '<a href="%s" class="sfWebDebugFileLink" title="%s">%s</a>',
        htmlspecialchars(strtr($linkFormat, array('%f' => $file, '%l' => $line)), ENT_QUOTES, sfConfig::get('sf_charset')),
        htmlspecialchars($shortFile, ENT_QUOTES, sfConfig::get('sf_charset')),
        null === $text ? $shortFile : $text);
    }
    else if (null === $text)
    {
      // return the shortened file path
      return $shortFile;
    }
    else
    {
      // return the provided text with the shortened file path as a tooltip
      return sprintf('<span title="%s">%s</span>', $shortFile, $text);
    }
  }

  /**
   * Format a SQL string with some colors on SQL keywords to make it more readable.
   *
   * @param  string $sql    SQL string to format
   * 
   * @return string $newSql The new formatted SQL string
   */
  public function formatSql($sql)
  {
    return preg_replace('/\b(UPDATE|SET|SELECT|FROM|AS|LIMIT|ASC|COUNT|DESC|WHERE|LEFT JOIN|INNER JOIN|RIGHT JOIN|ORDER BY|GROUP BY|IN|LIKE|DISTINCT|DELETE|INSERT|INTO|VALUES)\b/', '<span class="sfWebDebugLogInfo">\\1</span>', $sql);
  }
}
