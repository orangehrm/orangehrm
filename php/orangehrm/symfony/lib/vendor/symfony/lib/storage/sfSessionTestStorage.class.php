<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfSessionTestStorage is a fake sfSessionStorage implementation to allow easy testing.
 *
 * @package    symfony
 * @subpackage storage
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfSessionTestStorage.class.php 11002 2008-08-20 16:34:30Z fabien $
 */
class sfSessionTestStorage extends sfStorage
{
  protected
    $sessionId   = null,
    $sessionData = array();

  /**
   * Available options:
   *
   *  * session_path: The path to store the session files
   *  * session_id:   The session identifier
   *
   * @param array $options  An associative array of options
   *
   * @see sfStorage
   */
  public function initialize($options = null)
  {
    if (!isset($options['session_path']))
    {
      throw new InvalidArgumentException('The "session_path" option is mandatory for the sfSessionTestStorage class.');
    }

    $options = array_merge(array(
      'session_id'   => null,
    ), $options);

    // initialize parent
    parent::initialize($options);

    $this->sessionId = !is_null($this->options['session_id']) ? $this->options['session_id'] : (array_key_exists('session_id', $_SERVER) ? $_SERVER['session_id'] : null);

    if ($this->sessionId)
    {
      // we read session data from temp file
      $file = $this->options['session_path'].DIRECTORY_SEPARATOR.$this->sessionId.'.session';
      $this->sessionData = file_exists($file) ? unserialize(file_get_contents($file)) : array();
    }
    else
    {
      $this->sessionId   = md5(uniqid(rand(), true));
      $this->sessionData = array();
    }
  }

  /**
   * Gets session id for the current session storage instance.
   *
   * @return string Session id
   */
  public function getSessionId()
  {
    return $this->sessionId;
  }

  /**
   * Reads data from this storage.
   *
   * The preferred format for a key is directory style so naming conflicts can be avoided.
   *
   * @param  string $key  A unique key identifying your data
   *
   * @return mixed Data associated with the key
   */
  public function read($key)
  {
    $retval = null;

    if (isset($this->sessionData[$key]))
    {
      $retval = $this->sessionData[$key];
    }

    return $retval;
  }

  /**
   * Removes data from this storage.
   *
   * The preferred format for a key is directory style so naming conflicts can be avoided.
   *
   * @param  string $key  A unique key identifying your data
   *
   * @return mixed Data associated with the key
   */
  public function remove($key)
  {
    $retval = null;

    if (isset($this->sessionData[$key]))
    {
      $retval = $this->sessionData[$key];
      unset($this->sessionData[$key]);
    }

    return $retval;
  }

  /**
   * Writes data to this storage.
   *
   * The preferred format for a key is directory style so naming conflicts can be avoided
   *
   * @param string $key   A unique key identifying your data
   * @param mixed  $data  Data associated with your key
   *
   */
  public function write($key, $data)
  {
    $this->sessionData[$key] = $data;
  }

  /**
   * Clears all test sessions.
   */
  public function clear()
  {
    sfToolkit::clearDirectory($this->options['session_path']);
  }

  /**
   * Regenerates id that represents this storage.
   *
   * @param  boolean $destroy Destroy session when regenerating?
   *
   * @return boolean True if session regenerated, false if error
   *
   */
  public function regenerate($destroy = false)
  {
    return true;
  }

  /**
   * Executes the shutdown procedure.
   *
   */
  public function shutdown()
  {
    if ($this->sessionId)
    {
      $current_umask = umask(0000);
      if (!is_dir($this->options['session_path']))
      {
        mkdir($this->options['session_path'], 0777, true);
      }
      umask($current_umask);
      file_put_contents($this->options['session_path'].DIRECTORY_SEPARATOR.$this->sessionId.'.session', serialize($this->sessionData));
      $this->sessionId   = '';
      $this->sessionData = array();
    }
  }
}
