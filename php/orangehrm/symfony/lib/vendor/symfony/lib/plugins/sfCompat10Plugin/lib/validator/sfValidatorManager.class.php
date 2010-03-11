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
 * sfValidatorManager provides management for request parameters and their
 * associated validators.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfValidatorManager.class.php 7792 2008-03-09 22:06:59Z fabien $
 */
class sfValidatorManager
{
  protected
    $groups  = array(),
    $names   = array(),
    $request = null;

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct($context)
  {
    $this->initialize($context);
  }

  /**
   * Initializes this validator manager.
   *
   * @param sfContext A sfContext instance
   */
  public function initialize($context)
  {
    $this->request = $context->getRequest();
  }

  /**
   * Clears this validator manager so it can be reused.
   */
  public function clear()
  {
    $this->groups = null;
    $this->groups = array();
    $this->names  = null;
    $this->names  = array();
  }

  /**
   * Executes all validators and determine the validation status.
   *
   * @return bool true, if validation completed successfully, otherwise false
   */
  public function execute()
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array('Validation execution')));
    }

    $retval = true;

    // loop through the names and start our validation
    // if 1 or more groups exist, we'll have to do a second pass
    $pass = 1;

    while (true)
    {
      foreach ($this->names as $name => &$data)
      {
        if (isset($data['_is_parent']))
        {
          // this is a parent
          foreach ($data as $subname => &$subdata)
          {
            if ($subname == '_is_parent')
            {
              // this isn't an actual index, but more of a flag
              continue;
            }

            if ($subdata['validation_status'] == true && !$this->validate($subname, $subdata, $name))
            {
              // validation failed
              $retval = false;
            }
          }
        }
        else
        {
          // single parameter
          if ($data['validation_status'] == true && !$this->validate($name, $data, null))
          {
            // validation failed
            $retval = false;
          }
        }
      }

      if (count($this->groups) == 0 || $pass == 2)
      {
        break;
      }

      // increase our pass indicator
      ++$pass;
    }

    return $retval;
  }

  /**
   * Registers a file or parameter.
   *
   * @param string  A file or parameter name
   * @param bool    The required status
   * @param string  A required error message
   * @param string  A group name
   * @param string  A parent array
   */
  public function registerName($name, $required = true, $message = 'Required', $parent = null, $group = null, $isFile = false)
  {
    // create the entry
    $entry                      = array();
    $entry['group']             = null;
    $entry['is_file']           = $isFile;
    $entry['required']          = $required;
    $entry['required_msg']      = $message;
    $entry['validation_status'] = true;
    $entry['validators']        = array();

    if ($parent != null)
    {
      // this parameter has a parent array
      if (!isset($this->names[$parent]))
      {
        // create the parent array
        $this->names[$parent] = array('_is_parent' => true);
      }

      // register this parameter
      $this->names[$parent][$name] =& $entry;
    }
    else
    {
      // no parent

      // register this parameter
      $this->names[$name] =& $entry;
    }

    if ($group != null)
    {
      // set group
      if (!isset($this->groups[$group]))
      {
        // create our group
        $this->groups[$group] = array('_force' => false);
      }

      // add this file/parameter name to the group
      $this->groups[$group][] = $name;

      // add a reference back to the group array to the file/param array
      $entry['group'] =& $this->groups[$group];
    }
  }

  /**
   * Registers a validator for a file or parameter.
   *
   * @param string    A file or parameter name
   * @param Validator A validator implementation instance
   * @param string    A parent array name
   */
  public function registerValidator($name, $validator, $parent = null)
  {
    if ($parent != null)
    {
      // this parameter has a parent
      $this->names[$parent][$name]['validators'][] = $validator;
    }
    else
    {
      // no parent
      $this->names[$name]['validators'][] = $validator;
    }
  }

  /**
   * Validates a file or parameter.
   *
   * @param string A file or parameter name
   * @param array  Data associated with the file or parameter
   * @param string A parent name
   *
   * @return bool true, if validation completes successfully, otherwise false
   */
  protected function validate(&$name, &$data, $parent)
  {
    // get defaults
    $error     = null;
    $errorName = null;
    $force     = null !== $data['group'] ? $data['group']['_force'] : false;
    $retval    = true;
    $value     = null;

    // get our parameter value
    if ($parent == null)
    {
      // normal file/parameter
      $errorName = $name;

      if ($data['is_file'])
      {
        // file
        $value = $this->request->getFile($name);
      }
      else
      {
        // parameter
        $value = $this->request->getParameterHolder()->get($name);
      }
    }
    else
    {
      // we have a parent
      $errorName = $parent.'{'.$name.'}';

      if ($data['is_file'])
      {
        // file
        $parent = $this->request->getFile($parent.'['.$name.']');

        if ($parent != null)
        {
          $value = $parent;
        }
      }
      else
      {
        // parameter
        $parent = $this->request->getParameterHolder()->get($parent);

        if ($parent != null && isset($parent[$name]))
        {
          $value = $parent[$name];
        }
      }
    }

    // now for the dirty work
    if (
      ($data['is_file'] && !$value['name'])
      ||
      (!$data['is_file'] && (is_array($value) ? sfToolkit::isArrayValuesEmpty($value) : ($value === null || strlen($value) == 0)))
    )
    {
      if ($data['required'] || $force)
      {
        // it's empty!
        $error  = $data['required_msg'];
        $retval = false;
      }
      else
      {
        // we don't have to validate it
        $retval = true;
      }
    }
    else
    {
      // time for the fun
      $error = null;

      // get group force status
      if ($data['group'] != null)
      {
        // we set this because we do have a value for a parameter in this group
        $data['group']['_force'] = true;
      }

      if (count($data['validators']) > 0)
      {
        // loop through our validators
        foreach ($data['validators'] as $validator)
        {
          if (!$validator->execute($value, $error))
          {
            $retval = false;

            break;
          }
        }
      }
    }

    if (!$retval)
    {
      // set validation status
      $data['validation_status'] = false;

      // set the request error
      $this->request->setError($errorName, $error);
    }

    return $retval;
  }
}
