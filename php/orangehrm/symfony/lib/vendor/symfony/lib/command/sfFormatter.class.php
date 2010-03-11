<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfFormatter provides methods to format text to be displayed on a console.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFormatter.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class sfFormatter
{
  protected
    $size = 65;

  function __construct($maxLineSize = 65)
  {
    $this->size = $maxLineSize;
  }

  /**
   * Formats a text according to the given parameters.
   *
   * @param string $text       The test to style
   * @param mixed  $parameters An array of parameters
   * @param stream $stream     A stream (default to STDOUT)
   *
   * @return string The formatted text
   */
  public function format($text = '', $parameters = array(), $stream = STDOUT)
  {
    return $text;
  }

  /**
   * Formats a message within a section.
   *
   * @param string  $section The section name
   * @param string  $text    The text message
   * @param integer $size    The maximum size allowed for a line (65 by default)
   */
  public function formatSection($section, $text, $size = null)
  {
    return sprintf(">> %-9s %s", $section, $this->excerpt($text, $size));
  }

  /**
   * Truncates a line.
   *
   * @param string  $text The text
   * @param integer $size The maximum size of the returned string (65 by default)
   *
   * @return string The truncated string
   */
  public function excerpt($text, $size = null)
  {
    if (!$size)
    {
      $size = $this->size;
    }

    if (strlen($text) < $size)
    {
      return $text;
    }

    $subsize = floor(($size - 3) / 2);

    return substr($text, 0, $subsize).'...'.substr($text, -$subsize);
  }

  /**
   * Sets the maximum line size.
   *
   * @param integer $size The maximum line size for a message
   */
  public function setMaxLineSize($size)
  {
    $this->size = $size;
  }
}
