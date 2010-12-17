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
 * @version    SVN: $Id: sfFormatter.class.php 30008 2010-06-28 09:48:15Z fabien $
 */
class sfFormatter
{
  protected
    $size = null;

  function __construct($maxLineSize = null)
  {
    if (null === $maxLineSize)
    {
      if (function_exists('shell_exec'))
      {
        // this is tricky because "tput cols 2>&1" is not accurate
        $maxLineSize = ctype_digit(trim(shell_exec('tput cols 2>&1'))) ? (integer) shell_exec('tput cols') : 78;
      }
      else
      {
        $maxLineSize = 78;
      }
    }

    $this->size = $maxLineSize;
  }

  /**
   * Sets a new style.
   *
   * @param string $name    The style name
   * @param array  $options An array of options
   */
  public function setStyle($name, $options = array())
  {
  }

  /**
   * Formats a text according to the given parameters.
   *
   * @param  string $text         The test to style
   * @param  mixed  $parameters   An array of parameters
   *
   * @return string The formatted text
   */
  public function format($text = '', $parameters = array())
  {
    return $text;
  }

  /**
   * Formats a message within a section.
   *
   * @param string  $section  The section name
   * @param string  $text     The text message
   * @param integer $size     The maximum size allowed for a line
   */
  public function formatSection($section, $text, $size = null)
  {
    if (!$size)
    {
      $size = $this->size;
    }

    $section = sprintf('>> %-9s ', $section);

    return $section.$this->excerpt($text, $size - strlen($section));
  }

  /**
   * Truncates a line.
   *
   * @param string  $text The text
   * @param integer $size The maximum size of the returned string
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
