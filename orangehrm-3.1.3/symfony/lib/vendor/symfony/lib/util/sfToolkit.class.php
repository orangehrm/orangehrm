<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr <sean@code-box.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfToolkit provides basic utility methods.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfToolkit.class.php 29525 2010-05-19 13:01:43Z fabien $
 */
class sfToolkit
{
  /**
   * Extract the class or interface name from filename.
   *
   * @param  string $filename  A filename.
   *
   * @return string A class or interface name, if one can be extracted, otherwise null.
   */
  public static function extractClassName($filename)
  {
    $retval = null;

    if (self::isPathAbsolute($filename))
    {
      $filename = basename($filename);
    }

    $pattern = '/(.*?)\.(class|interface)\.php/i';

    if (preg_match($pattern, $filename, $match))
    {
      $retval = $match[1];
    }

    return $retval;
  }

  /**
   * Clear all files in a given directory.
   *
   * @param string $directory  An absolute filesystem path to a directory.
   */
  public static function clearDirectory($directory)
  {
    if (!is_dir($directory))
    {
      return;
    }

    // open a file point to the cache dir
    $fp = opendir($directory);

    // ignore names
    $ignore = array('.', '..', 'CVS', '.svn');

    while (($file = readdir($fp)) !== false)
    {
      if (!in_array($file, $ignore))
      {
        if (is_link($directory.'/'.$file))
        {
          // delete symlink
          unlink($directory.'/'.$file);
        }
        else if (is_dir($directory.'/'.$file))
        {
          // recurse through directory
          self::clearDirectory($directory.'/'.$file);

          // delete the directory
          rmdir($directory.'/'.$file);
        }
        else
        {
          // delete the file
          unlink($directory.'/'.$file);
        }
      }
    }

    // close file pointer
    closedir($fp);
  }

  /**
   * Clear all files and directories corresponding to a glob pattern.
   *
   * @param string $pattern  An absolute filesystem pattern.
   */
  public static function clearGlob($pattern)
  {
    if (false === $files = glob($pattern))
    {
      return;
    }

    // order is important when removing directories
    sort($files);

    foreach ($files as $file)
    {
      if (is_dir($file))
      {
        // delete directory
        self::clearDirectory($file);
      }
      else
      {
        // delete file
        unlink($file);
      }
    }
  }

  /**
   * Determine if a filesystem path is absolute.
   *
   * @param  path $path  A filesystem path.
   *
   * @return bool true, if the path is absolute, otherwise false.
   */
  public static function isPathAbsolute($path)
  {
    if ($path[0] == '/' || $path[0] == '\\' ||
        (strlen($path) > 3 && ctype_alpha($path[0]) &&
         $path[1] == ':' &&
         ($path[2] == '\\' || $path[2] == '/')
        )
       )
    {
      return true;
    }

    return false;
  }

  /**
   * Strips comments from php source code
   *
   * @param  string $source  PHP source code.
   *
   * @return string Comment free source code.
   */
  public static function stripComments($source)
  {
    if (!function_exists('token_get_all'))
    {
      return $source;
    }

    $ignore = array(T_COMMENT => true, T_DOC_COMMENT => true);
    $output = '';

    foreach (token_get_all($source) as $token)
    {
      // array
      if (isset($token[1]))
      {
        // no action on comments
        if (!isset($ignore[$token[0]]))
        {
          // anything else -> output "as is"
          $output .= $token[1];
        }
      }
      else
      {
        // simple 1-character token
        $output .= $token;
      }
    }

    return $output;
  }

  /**
   * Strip slashes recursively from array
   *
   * @param  array $value  the value to strip
   *
   * @return array clean value with slashes stripped
   */
  public static function stripslashesDeep($value)
  {
    return is_array($value) ? array_map(array('sfToolkit', 'stripslashesDeep'), $value) : stripslashes($value);
  }

  // code from php at moechofe dot com (array_merge comment on php.net)
  /*
   * array arrayDeepMerge ( array array1 [, array array2 [, array ...]] )
   *
   * Like array_merge
   *
   *  arrayDeepMerge() merges the elements of one or more arrays together so
   * that the values of one are appended to the end of the previous one. It
   * returns the resulting array.
   *  If the input arrays have the same string keys, then the later value for
   * that key will overwrite the previous one. If, however, the arrays contain
   * numeric keys, the later value will not overwrite the original value, but
   * will be appended.
   *  If only one array is given and the array is numerically indexed, the keys
   * get reindexed in a continuous way.
   *
   * Different from array_merge
   *  If string keys have arrays for values, these arrays will merge recursively.
   */
  public static function arrayDeepMerge()
  {
    switch (func_num_args())
    {
      case 0:
        return false;
      case 1:
        return func_get_arg(0);
      case 2:
        $args = func_get_args();
        $args[2] = array();
        if (is_array($args[0]) && is_array($args[1]))
        {
          foreach (array_unique(array_merge(array_keys($args[0]),array_keys($args[1]))) as $key)
          {
            $isKey0 = array_key_exists($key, $args[0]);
            $isKey1 = array_key_exists($key, $args[1]);
            if ($isKey0 && $isKey1 && is_array($args[0][$key]) && is_array($args[1][$key]))
            {
              $args[2][$key] = self::arrayDeepMerge($args[0][$key], $args[1][$key]);
            }
            else if ($isKey0 && $isKey1)
            {
              $args[2][$key] = $args[1][$key];
            }
            else if (!$isKey1)
            {
              $args[2][$key] = $args[0][$key];
            }
            else if (!$isKey0)
            {
              $args[2][$key] = $args[1][$key];
            }
          }
          return $args[2];
        }
        else
        {
          return $args[1];
        }
      default :
        $args = func_get_args();
        $args[1] = sfToolkit::arrayDeepMerge($args[0], $args[1]);
        array_shift($args);
        return call_user_func_array(array('sfToolkit', 'arrayDeepMerge'), $args);
        break;
    }
  }

  /**
   * Converts string to array
   *
   * @param  string $string  the value to convert to array
   *
   * @return array
   */
  public static function stringToArray($string)
  {
    preg_match_all('/
      \s*(\w+)              # key                               \\1
      \s*=\s*               # =
      (\'|")?               # values may be included in \' or " \\2
      (.*?)                 # value                             \\3
      (?(2) \\2)            # matching \' or " if needed        \\4
      \s*(?:
        (?=\w+\s*=) | \s*$  # followed by another key= or the end of the string
      )
    /x', $string, $matches, PREG_SET_ORDER);

    $attributes = array();
    foreach ($matches as $val)
    {
      $attributes[$val[1]] = self::literalize($val[3]);
    }

    return $attributes;
  }

  /**
   * Finds the type of the passed value, returns the value as the new type.
   *
   * @param  string $value
   * @param  bool   $quoted  Quote?
   *
   * @return mixed
   */
  public static function literalize($value, $quoted = false)
  {
    // lowercase our value for comparison
    $value  = trim($value);
    $lvalue = strtolower($value);

    if (in_array($lvalue, array('null', '~', '')))
    {
      $value = null;
    }
    else if (in_array($lvalue, array('true', 'on', '+', 'yes')))
    {
      $value = true;
    }
    else if (in_array($lvalue, array('false', 'off', '-', 'no')))
    {
      $value = false;
    }
    else if (ctype_digit($value))
    {
      $value = (int) $value;
    }
    else if (is_numeric($value))
    {
      $value = (float) $value;
    }
    else
    {
      $value = self::replaceConstants($value);
      if ($quoted)
      {
        $value = '\''.str_replace('\'', '\\\'', $value).'\'';
      }
    }

    return $value;
  }

  /**
   * Replaces constant identifiers in a scalar value.
   *
   * @param  string $value  the value to perform the replacement on
   *
   * @return string the value with substitutions made
   */
  public static function replaceConstants($value)
  {
    return is_string($value) ? preg_replace_callback('/%(.+?)%/', create_function('$v', 'return sfConfig::has(strtolower($v[1])) ? sfConfig::get(strtolower($v[1])) : "%{$v[1]}%";'), $value) : $value;
  }

  /**
   * Returns subject replaced with regular expression matchs
   *
   * @param mixed $search        subject to search
   * @param array $replacePairs  array of search => replace pairs
   */
  public static function pregtr($search, $replacePairs)
  {
    return preg_replace(array_keys($replacePairs), array_values($replacePairs), $search);
  }

  /**
   * Checks if array values are empty
   *
   * @param  array $array  the array to check
   * @return boolean true if empty, otherwise false
   */
  public static function isArrayValuesEmpty($array)
  {
    static $isEmpty = true;
    foreach ($array as $value)
    {
      $isEmpty = (is_array($value)) ? self::isArrayValuesEmpty($value) : (strlen($value) == 0);
      if (!$isEmpty)
      {
        break;
      }
    }

    return $isEmpty;
  }

  /**
   * Checks if a string is an utf8.
   *
   * Yi Stone Li<yili@yahoo-inc.com>
   * Copyright (c) 2007 Yahoo! Inc. All rights reserved.
   * Licensed under the BSD open source license
   *
   * @param string
   *
   * @return bool true if $string is valid UTF-8 and false otherwise.
   */
  public static function isUTF8($string)
  {
    for ($idx = 0, $strlen = strlen($string); $idx < $strlen; $idx++)
    {
      $byte = ord($string[$idx]);

      if ($byte & 0x80)
      {
        if (($byte & 0xE0) == 0xC0)
        {
          // 2 byte char
          $bytes_remaining = 1;
        }
        else if (($byte & 0xF0) == 0xE0)
        {
          // 3 byte char
          $bytes_remaining = 2;
        }
        else if (($byte & 0xF8) == 0xF0)
        {
          // 4 byte char
          $bytes_remaining = 3;
        }
        else
        {
          return false;
        }

        if ($idx + $bytes_remaining >= $strlen)
        {
          return false;
        }

        while ($bytes_remaining--)
        {
          if ((ord($string[++$idx]) & 0xC0) != 0x80)
          {
            return false;
          }
        }
      }
    }

    return true;
  }

  /**
   * Returns an array value for a path.
   *
   * @param array  $values   The values to search
   * @param string $name     The token name
   * @param array  $default  Default if not found
   *
   * @return array
   */
  public static function getArrayValueForPath($values, $name, $default = null)
  {
    if (false === $offset = strpos($name, '['))
    {
      return isset($values[$name]) ? $values[$name] : $default;
    }

    if (!isset($values[substr($name, 0, $offset)]))
    {
      return $default;
    }

    $array = $values[substr($name, 0, $offset)];

    while (false !== $pos = strpos($name, '[', $offset))
    {
      $end = strpos($name, ']', $pos);
      if ($end == $pos + 1)
      {
        // reached a []
        if (!is_array($array))
        {
          return $default;
        }
        break;
      }
      else if (!isset($array[substr($name, $pos + 1, $end - $pos - 1)]))
      {
        return $default;
      }
      else if (is_array($array))
      {
        $array = $array[substr($name, $pos + 1, $end - $pos - 1)];
        $offset = $end;
      }
      else
      {
        return $default;
      }
    }

    return $array;
  }

  /**
   * Get path to php cli.
   *
   * @throws sfException If no php cli found
   * @return string
   */
  public static function getPhpCli()
  {
    $path = getenv('PATH') ? getenv('PATH') : getenv('Path');
    $suffixes = DIRECTORY_SEPARATOR == '\\' ? (getenv('PATHEXT') ? explode(PATH_SEPARATOR, getenv('PATHEXT')) : array('.exe', '.bat', '.cmd', '.com')) : array('');
    foreach (array('php5', 'php') as $phpCli)
    {
      foreach ($suffixes as $suffix)
      {
        foreach (explode(PATH_SEPARATOR, $path) as $dir)
        {
          if (is_file($file = $dir.DIRECTORY_SEPARATOR.$phpCli.$suffix) && is_executable($file))
          {
            return $file;
          }
        }
      }
    }

    throw new sfException('Unable to find PHP executable.');
  }

  /**
   * Converts strings to UTF-8 via iconv. NB, the result may not by UTF-8 if the conversion failed.
   *
   * This file comes from Prado (BSD License)
   *
   * @param  string $string string to convert to UTF-8
   * @param  string $from   current encoding
   *
   * @return string UTF-8 encoded string, original string if iconv failed.
   */
  static public function I18N_toUTF8($string, $from)
  {
    $from = strtoupper($from);
    if ($from != 'UTF-8')
    {
      $s = iconv($from,'UTF-8',$string);  // to UTF-8

      return $s !== false ? $s : $string; // it could return false
    }

    return $string;
  }

  /**
   * Converts UTF-8 strings to a different encoding. NB. The result may not have been encoded if iconv fails.
   *
   * This file comes from Prado (BSD License)
   *
   * @param  string $string  the UTF-8 string for conversion
   * @param  string $to      new encoding
   *
   * @return string encoded string.
   */
  static public function I18N_toEncoding($string, $to)
  {
    $to = strtoupper($to);
    if ($to != 'UTF-8')
    {
      $s = iconv('UTF-8', $to, $string);

      return $s !== false ? $s : $string;
    }

    return $string;
  }

  /**
   * Adds a path to the PHP include_path setting.
   *
   * @param   mixed  $path     Single string path or an array of paths
   * @param   string $position Either 'front' or 'back'
   *
   * @return  string The old include path
   */
  static public function addIncludePath($path, $position = 'front')
  {
    if (is_array($path))
    {
      foreach ('front' == $position ? array_reverse($path) : $path as $p)
      {
        self::addIncludePath($p, $position);
      }

      return;
    }

    $paths = explode(PATH_SEPARATOR, get_include_path());

    // remove what's already in the include_path
    if (false !== $key = array_search(realpath($path), array_map('realpath', $paths)))
    {
      unset($paths[$key]);
    }

    switch ($position)
    {
      case 'front':
        array_unshift($paths, $path);
        break;
      case 'back':
        $paths[] = $path;
        break;
      default:
        throw new InvalidArgumentException(sprintf('Unrecognized position: "%s"', $position));
    }

    return set_include_path(join(PATH_SEPARATOR, $paths));
  }
}
