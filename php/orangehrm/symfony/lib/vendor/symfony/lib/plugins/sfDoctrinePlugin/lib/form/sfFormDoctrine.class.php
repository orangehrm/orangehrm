<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfFormDoctrine is the base class for forms based on Doctrine objects.
 *
 * This class extends BaseForm, a class generated automatically with each new project.
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfFormDoctrine.class.php 32740 2011-07-09 09:24:03Z fabien $
 */
abstract class sfFormDoctrine extends sfFormObject
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
      $this->isNew = !$this->getObject()->exists();
    }

    parent::__construct(array(), $options, $CSRFSecret);

    $this->updateDefaultsFromObject();
  }

  /**
   * @return Doctrine_Connection
   * @see sfFormObject
   */
  public function getConnection()
  {
    return Doctrine_Manager::getInstance()->getConnectionForComponent($this->getModelName());
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
      $i18nObject = $this->getObject()->Translation[$culture];
      $i18n = new $class($i18nObject);

      if (false === $i18nObject->exists())
      {
        unset($i18n[$this->getI18nModelPrimaryKeyName()], $i18n[$this->getI18nModelI18nField()]);
      }

      $this->embedForm($culture, $i18n, $decorator);
    }
  }

  /**
   * Embed a Doctrine_Collection relationship in to a form
   *
   *     [php]
   *     $userForm = new UserForm($user);
   *     $userForm->embedRelation('Groups AS groups');
   *
   * @param  string $relationName  The name of the relation and an optional alias
   * @param  string $formClass     The name of the form class to use
   * @param  array  $formArguments Arguments to pass to the constructor (related object will be shifted onto the front)
   * @param string  $innerDecorator A HTML decorator for each embedded form
   * @param string  $decorator      A HTML decorator for the main embedded form
   *
   * @throws InvalidArgumentException If the relationship is not a collection
   */
  public function embedRelation($relationName, $formClass = null, $formArgs = array(), $innerDecorator = null, $decorator = null)
  {
    if (false !== $pos = stripos($relationName, ' as '))
    {
      $fieldName = substr($relationName, $pos + 4);
      $relationName = substr($relationName, 0, $pos);
    }
    else
    {
      $fieldName = $relationName;
    }

    $relation = $this->getObject()->getTable()->getRelation($relationName);

    $r = new ReflectionClass(null === $formClass ? $relation->getClass().'Form' : $formClass);

    if (Doctrine_Relation::ONE == $relation->getType())
    {
      $this->embedForm($fieldName, $r->newInstanceArgs(array_merge(array($this->getObject()->$relationName), $formArgs)), $decorator);
    }
    else
    {
      $subForm = new sfForm();

      foreach ($this->getObject()->$relationName as $index => $childObject)
      {
        $form = $r->newInstanceArgs(array_merge(array($childObject), $formArgs));

        $subForm->embedForm($index, $form, $innerDecorator);
        $subForm->getWidgetSchema()->setLabel($index, (string) $childObject);
      }

      $this->embedForm($fieldName, $subForm, $decorator);
    }
  }

  /**
   * @see sfFormObject
   */
  protected function doUpdateObject($values)
  {
    $this->getObject()->fromArray($values);
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
      $method = sprintf('update%sColumn', $this->camelize($field));

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
    return $this->getObject()->getTable()->hasTemplate('Doctrine_Template_I18n');
  }

  /**
   * Returns the name of the i18n model.
   *
   * @return string The name of the i18n model
   */
  public function getI18nModelName()
  {
    return $this->getObject()->getTable()->getTemplate('Doctrine_Template_I18n')->getI18n()->getOption('className');
  }

  /**
   * Returns the name of the i18n form class.
   *
   * @return string The name of the i18n form class
   */
  public function getI18nFormClass()
  {
    return $this->getI18nModelName().'Form';
  }

  /**
   * Returns the primary key name of the i18n model.
   *
   * @return string The primary key name of the i18n model
   */
  public function getI18nModelPrimaryKeyName()
  {
    $primaryKey = $this->getObject()->getTable()->getIdentifier();

    if (is_array($primaryKey))
    {
      throw new sfException(sprintf('The model "%s" has composite primary keys and cannot be used with i18n..', $this->getModelName()));
    }

    return $primaryKey;
  }

  /**
   * Returns the i18nField name of the i18n model.
   *
   * @return string The i18nField name of the i18n model
   */
  public function getI18nModelI18nField()
  {
    return $this->getObject()->getTable()->getTemplate('Doctrine_Template_I18n')->getI18n()->getOption('i18nField');
  }

  /**
   * Updates the default values of the form with the current values of the current object.
   */
  protected function updateDefaultsFromObject()
  {
    $defaults = $this->getDefaults();

    // update defaults for the main object
    if ($this->isNew())
    {
      $defaults = $defaults + $this->getObject()->toArray(false);
    }
    else
    {
      $defaults = $this->getObject()->toArray(false) + $defaults;
    }

    foreach ($this->embeddedForms as $name => $form)
    {
      if ($form instanceof sfFormDoctrine)
      {
        $form->updateDefaultsFromObject();
        $defaults[$name] = $form->getDefaults();
      }
    }

    $this->setDefaults($defaults);
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
      // this is needed if the form is embedded, in which case
      // the parent form has already changed the value of the field
      $oldValues = $this->getObject()->getModified(true, false);

      return isset($oldValues[$field]) ? $oldValues[$field] : $this->object->$field;
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

    $directory = $this->validatorSchema[$field]->getOption('path');
    if ($directory && is_file($file = $directory.'/'.$this->getObject()->$field))
    {
      unlink($file);
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

    $method = sprintf('generate%sFilename', $this->camelize($field));

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
    else if (method_exists($this->getObject(), $method = sprintf('generate%sFilename', $field)))
    {
      // this non-camelized method name has been deprecated
      return $file->save($this->getObject()->$method($file));
    }
    else
    {
      return $file->save();
    }
  }

  /**
   * Used in generated forms when models use inheritance.
   */
  protected function setupInheritance()
  {
  }

  /**
   * Returns the name of the related model.
   * 
   * @param string $alias A relation alias
   * 
   * @return string
   * 
   * @throws InvalidArgumentException If no relation with the supplied alias exists on the current model
   */
  protected function getRelatedModelName($alias)
  {
    $table = Doctrine_Core::getTable($this->getModelName());

    if (!$table->hasRelation($alias))
    {
      throw new InvalidArgumentException(sprintf('The "%s" model has no "%s" relation.', $this->getModelName(), $alias));
    }

    $relation = $table->getRelation($alias);

    return $relation['class'];
  }
}
