<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfClassManipulator manipulates class code.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfClassManipulator.class.php 25063 2009-12-08 06:02:07Z Kris.Wallsmith $
 */
class sfClassManipulator
{
  static protected $signatureTokens = array(
    T_FINAL,
    T_ABSTRACT,
    T_STATIC,
    T_PUBLIC,
    T_PROTECTED,
    T_PRIVATE,
    T_FUNCTION,
  );

  protected $code = '', $file = false;

  /**
   * Constructor.
   *
   * @param string $code The code to manipulate
   */
  public function __construct($code)
  {
    $this->code = $code;
  }

  /**
   * Creates a manipulator object from a file.
   *
   * @param string $file A file name
   *
   * @return sfClassManipulator A sfClassManipulator instance
   */
  static public function fromFile($file)
  {
    $manipulator = new self(file_get_contents($file));
    $manipulator->setFile($file);

    return $manipulator;
  }

  /**
   * Saves the code back to the associated file.
   *
   * This only works if you have bound the instance with a file with the setFile() method.
   *
   * @throw LogicException if no file is associated with the instance
   */
  public function save()
  {
    if (!$this->file)
    {
      throw new LogicException('Unable to save the code as no file has been provided.');
    }

    file_put_contents($this->file, $this->code);
  }

  /**
   * Gets the modified code.
   *
   * @return string The modified code
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * Gets the associated file.
   *
   * @return string The associated file
   */
  public function getFile()
  {
    return $this->file;
  }

  /**
   * Sets the file associated with this instance.
   *
   * @param string A file name
   */
  public function setFile($file)
  {
    $this->file = $file;
  }

  /**
   * Wraps an existing method with some code.
   *
   * @param string $method     The method name to change
   * @param string $topCode    The code to add at the top of the method
   * @param string $bottomCode The code to add at the bottom of the method
   */
  public function wrapMethod($method, $topCode = '', $bottomCode = '')
  {
    $code = '';
    $insideSetup = -1;
    $parens = 0;
    foreach (token_get_all($this->code) as $token)
    {
      if (isset($token[1]))
      {
        if (-1 == $insideSetup && T_FUNCTION == $token[0])
        {
          $insideSetup = 0;
        }
        elseif (0 == $insideSetup && T_STRING == $token[0])
        {
          $insideSetup = $method == $token[1] ? 1 : -1;
        }

        $code .= $token[1];
      }
      else
      {
        if (1 == $insideSetup && '{' == $token)
        {
          if (!$parens)
          {
            $code .= $topCode ? $token.PHP_EOL.'    '.$topCode : $token;
          }
          else
          {
            $code .= $token;
          }

          ++$parens;
        }
        elseif (1 == $insideSetup && '}' == $token)
        {
          --$parens;

          if (!$parens)
          {
            $insideSetup = -1;

            $code .= $bottomCode ? '  '.$bottomCode.PHP_EOL.'  '.$token : $token;
          }
          else
          {
            $code .= $token;
          }
        }
        else
        {
          $code .= $token;
        }
      }
    }

    return $this->code = $code;
  }

  /**
   * Filters each line of the given method through a callable.
   * 
   * @param string $method   The method name
   * @param mixed  $callable A PHP callable that accepts and returns one line of PHP code
   */
  public function filterMethod($method, $callable)
  {
    $line = '';
    $code = '';
    $insideSetup = -1;
    $parens = 0;
    $break = false;

    $tokens = token_get_all($this->code);
    for ($i = 0; $i < count($tokens); $i++)
    {
      $token = $tokens[$i];

      if (is_array($token))
      {
        $line .= $token[1];

        if (-1 == $insideSetup && T_FUNCTION == $token[0])
        {
          $insideSetup = 0;
        }
        elseif (0 == $insideSetup && T_STRING == $token[0])
        {
          $insideSetup = $method == $token[1] ? 1 : -1;
        }
      }
      else
      {
        if (1 == $insideSetup && '{' == $token)
        {
          ++$parens;
        }
        elseif (1 == $insideSetup && '}' == $token)
        {
          --$parens;

          if (!$parens)
          {
            $break = true;
          }
        }

        $line .= $token;
      }

      $lines = preg_split('/(\r?\n)/', $line, null, PREG_SPLIT_DELIM_CAPTURE);
      if (count($lines) > 1 || $break)
      {
        $line = $break ? '' : array_pop($lines);
        foreach (array_chunk($lines, 2) as $chunk)
        {
          list($l, $eol) = array_pad($chunk, 2, '');

          if (1 == $insideSetup)
          {
            list($before, $setup) = $this->splitSetup($l);
            $code .= $before;
            $code .= call_user_func($callable, $setup.$eol);
          }
          else
          {
            $code .= $l.$eol;
          }
        }
      }

      if ($break)
      {
        $insideSetup = -1;
        $break = false;
      }
    }

    if ($line)
    {
      $code .= $line;
    }

    return $this->code = $code;
  }

  protected function splitSetup($line)
  {
    $before = '';
    $setup = '';

    if ($line)
    {
      if (false === stripos($line, '<?php'))
      {
        // add a function so we can accurately slice
        $tokens = token_get_all('<?php function'.$line);
        $tokens = array_slice($tokens, 2);
      }
      else
      {
        $tokens = token_get_all($line);
      }

      // we're in reverse
      $inSignature = false;
      while ($token = array_pop($tokens))
      {
        $value = $this->getTokenValue($token);
        if (is_array($token) && in_array($token[0], self::$signatureTokens))
        {
          $inSignature = true;
        }
        elseif ($inSignature && !preg_match('/\s+/', $value))
        {
          // clean up
          preg_match('/^\s*/', $setup, $match);
          $before = implode('', array_map(array($this, 'getTokenValue'), $tokens)).$value.$match[0];
          $setup = substr($setup, strlen($match[0]));

          return array($before, $setup);
        }

        $setup = $value.$setup;
      }
    }

    return array($before, $setup);
  }

  /**
   * Returns a token's string value.
   *
   * @param array|string $token
   *
   * @return string
   */
  protected function getTokenValue($token)
  {
    return is_array($token) ? $token[1] : $token;
  }
}
