<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfFormPropel is the base class for forms based on Propel objects.
 *
 * This class extends BaseForm, a class generated automatically with each new project.
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFormPropel.class.php 27915 2010-02-11 18:12:56Z Kris.Wallsmith $
 */
abstract class sfFormPropel extends sfFormObject
{
  /**
   * Constructor.
   *
   * @param mixed  A object used to initialize default values
   * @param array  An array of options
   * @param string A CSRF secret (false to disable CSRF protection, null to use the global CSRF secret)
   *
   * @see sfForm
   */
  public function __construct($object = null, $options = array(), $CSRFSecret = null)
  {
    $class = $this->getModelName();
    if (!$object)
    {
      $this->object = new $class();
    }
    else
    {
      if (!$object instanceof $class)
      {
        throw new sfException(sprintf('The "%s" form only accepts a "%s" object.', get_class($this), $class));
      }

      $this->object = $object;
      $this->isNew = $this->getObject()->isNew();
    }

    parent::__construct(array(), $options, $CSRFSecret);

    $this->updateDefaultsFromObject();
  }

  /**
   * @return PropelPDO
   * @see sfFormObject
   */
  public function getConnection()
  {
    return Propel::getConnection(constant(constant(get_class($this->getObject()).'::PEER').'::DATABASE_NAME'));
  }

  /**
   * Embeds i18n objects into the current form.
   *
   * @param array   $cultures   An array of cultures
   * @param string  $decorator  A HTML decorator for the embedded form
   */
  public function embedI18n($cultures, $decorator = null)
  {
    if (!$this->isI18n())
    {
      throw new sfException(sprintf('The model "%s" is not internationalized.', $this->getModelName()));
    }

    $class = $this->getI18nFormClass();
    foreach ($cultures as $culture)
    {
      $method = sprintf('getCurrent%s', $this->getI18nModelName($culture));
      $i18nObject = $this->getObject()->$method($culture);
      $i18n = new $class($i18nObject);
      
      if ($i18nObject->isNew())
      {
        unset($i18n['id'], $i18n['culture']);
      }

      $this->embedForm($culture, $i18n, $decorator);
    }
  }

  /**
   * @see sfFormObject
   */
  protected function doUpdateObject($values)
  {
    $this->getObject()->fromArray($values, BasePeer::TYPE_FIELDNAME);
  }

  /**
   * Processes cleaned up values with user defined methods.
   *
   * To process a value before it is used by the updateObject() method,
   * you need to define an updateXXXColumn() method where XXX is the PHP name
   * of the column.
   *
   * The method must return the processed value or false to remove the value
   * from the array of cleaned up values.
   *
   * @see sfFormObject
   */
  public function processValues($values)
  {
    // see if the user has overridden some column setter
    $valuesToProcess = $values;
    foreach ($valuesToProcess as $field => $value)
    {
      try
      {
        $method = sprintf('update%sColumn', call_user_func(array(constant(get_class($this->getObject()).'::PEER'), 'translateFieldName'), $field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME));
      }
      catch (Exception $e)
      {
        // not a "real" column of this object
        if (!method_exists($this, $method = sprintf('update%sColumn', self::camelize($field))))
        {
          continue;
        }
      }

      if (method_exists($this, $method))
      {
        if (false === $ret = $this->$method($value))
        {
          unset($values[$field]);
        }
        else
        {
          $values[$field] = $ret;
        }
      }
      else
      {
        // save files
        if ($this->validatorSchema[$field] instanceof sfValidatorFile)
        {
          $values[$field] = $this->processUploadedFile($field, null, $valuesToProcess);
        }
      }
    }

    return $values;
  }

  /**
   * Returns true if the current form has some associated i18n objects.
   *
   * @return Boolean true if the current form has some associated i18n objects, false otherwise
   */
  public function isI18n()
  {
    return null !== $this->getI18nFormClass();
  }

  /**
   * Returns the name of the i18n model.
   *
   * @return string The name of the i18n model
   */
  public function getI18nModelName()
  {
    return null;
  }

  /**
   * Returns the name of the i18n form class.
   *
   * @return string The name of the i18n form class
   */
  public function getI18nFormClass()
  {
    return null;
  }

  /**
   * Updates the default values of the form with the current values of the current object.
   */
  protected function updateDefaultsFromObject()
  {
    // update defaults for the main object
    if ($this->isNew())
    {
      $this->setDefaults($this->getDefaults() + $this->getObject()->toArray(BasePeer::TYPE_FIELDNAME));
    }
    else
    {
      $this->setDefaults($this->getObject()->toArray(BasePeer::TYPE_FIELDNAME) + $this->getDefaults());
    }
  }

  /**
   * Saves the uploaded file for the given field.
   *
   * @param  string $field The field name
   * @param  string $filename The file name of the file to save
   * @param  array  $values An array of values
   *
   * @return string The filename used to save the file
   */
  protected function processUploadedFile($field, $filename = null, $values = null)
  {
    if (!$this->validatorSchema[$field] instanceof sfValidatorFile)
    {
      throw new LogicException(sprintf('You cannot save the current file for field "%s" as the field is not a file.', $field));
    }

    if (null === $values)
    {
      $values = $this->values;
    }

    if (isset($values[$field.'_delete']) && $values[$field.'_delete'])
    {
      $this->removeFile($field);

      return '';
    }

    if (!$values[$field])
    {
      $column = call_user_func(array(constant(get_class($this->getObject()).'::PEER'), 'translateFieldName'), $field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
      $getter = 'get'.$column;

      return $this->getObject()->$getter();
    }

    // we need the base directory
    if (!$this->validatorSchema[$field]->getOption('path'))
    {
      return $values[$field];
    }

    $this->removeFile($field);

    return $this->saveFile($field, $filename, $values[$field]);
  }

  /**
   * Removes the current file for the field.
   *
   * @param string $field The field name
   */
  protected function removeFile($field)
  {
    if (!$this->validatorSchema[$field] instanceof sfValidatorFile)
    {
      throw new LogicException(sprintf('You cannot remove the current file for field "%s" as the field is not a file.', $field));
    }

    $column = call_user_func(array(constant(get_class($this->getObject()).'::PEER'), 'translateFieldName'), $field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
    $getter = 'get'.$column;

    if (($directory = $this->validatorSchema[$field]->getOption('path')) && is_file($directory.DIRECTORY_SEPARATOR.$this->getObject()->$getter()))
    {
      unlink($directory.DIRECTORY_SEPARATOR.$this->getObject()->$getter());
    }
  }

  /**
   * Saves the current file for the field.
   *
   * @param  string          $field    The field name
   * @param  string          $filename The file name of the file to save
   * @param  sfValidatedFile $file     The validated file to save
   *
   * @return string The filename used to save the file
   */
  protected function saveFile($field, $filename = null, sfValidatedFile $file = null)
  {
    if (!$this->validatorSchema[$field] instanceof sfValidatorFile)
    {
      throw new LogicException(sprintf('You cannot save the current file for field "%s" as the field is not a file.', $field));
    }

    if (null === $file)
    {
      $file = $this->getValue($field);
    }

    $column = call_user_func(array(constant(get_class($this->getObject()).'::PEER'), 'translateFieldName'), $field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
    $method = sprintf('generate%sFilename', $column);

    if (null !== $filename)
    {
      return $file->save($filename);
    }
    else if (method_exists($this, $method))
    {
      return $file->save($this->$method($file));
    }
    else if (method_exists($this->getObject(), $method))
    {
      return $file->save($this->getObject()->$method($file));
    }
    else
    {
      return $file->save();
    }
  }
}
