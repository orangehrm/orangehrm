<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base class for forms that deal with a single object.
 * 
 * @package    symfony
 * @subpackage form
 * @author     Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version    SVN: $Id: sfFormObject.class.php 33250 2011-12-12 16:02:15Z fabien $
 */
abstract class sfFormObject extends BaseForm
{
  protected
    $isNew  = true,
    $object = null;

  /**
   * Returns the current model name.
   * 
   * @return string
   */
  abstract public function getModelName();

  /**
   * Returns the default connection for the current model.
   *
   * @return mixed A database connection
   */
  abstract public function getConnection();

  /**
   * Updates the values of the object with the cleaned up values.
   *
   * If you want to add some logic before updating or update other associated
   * objects, this is the method to override.
   *
   * @param array $values An array of values
   */
  abstract protected function doUpdateObject($values);

  /**
   * Processes cleaned up values.
   *
   * @param  array $values An array of values
   * 
   * @return array An array of cleaned up values
   */
  abstract public function processValues($values);

  /**
   * Returns true if the current form embeds a new object.
   *
   * @return Boolean true if the current form embeds a new object, false otherwise
   */
  public function isNew()
  {
    return $this->isNew;
  }

  /**
   * Returns the current object for this form.
   *
   * @return mixed The current object
   */
  public function getObject()
  {
    return $this->object;
  }

  /**
   * Binds the current form and saves the object to the database in one step.
   *
   * @param  array An array of tainted values to use to bind the form
   * @param  array An array of uploaded files (in the $_FILES or $_GET format)
   * @param  mixed An optional connection object
   *
   * @return Boolean true if the form is valid, false otherwise
   */
  public function bindAndSave($taintedValues, $taintedFiles = null, $con = null)
  {
    $this->bind($taintedValues, $taintedFiles);
    if ($this->isValid())
    {
      $this->save($con);

      return true;
    }

    return false;
  }


  /**
   * Saves the current object to the database.
   *
   * The object saving is done in a transaction and handled by the doSave() method.
   *
   * @param mixed $con An optional connection object
   *
   * @return mixed The current saved object
   *
   * @see doSave()
   * 
   * @throws sfValidatorError If the form is not valid
   */
  public function save($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    try
    {
      $con->beginTransaction();

      $this->doSave($con);

      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();

      throw $e;
    }

    return $this->getObject();
  }

  /**
   * Updates and saves the current object.
   *
   * If you want to add some logic before saving or save other associated
   * objects, this is the method to override.
   *
   * @param mixed $con An optional connection object
   */
  protected function doSave($con = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $this->updateObject();

    $this->getObject()->save($con);

    // embedded forms
    $this->saveEmbeddedForms($con);
  }

  /**
   * Updates the values of the object with the cleaned up values.
   *
   * @param  array $values An array of values
   *
   * @return mixed The current updated object
   */
  public function updateObject($values = null)
  {
    if (null === $values)
    {
      $values = $this->values;
    }

    $values = $this->processValues($values);

    $this->doUpdateObject($values);

    // embedded forms
    $this->updateObjectEmbeddedForms($values);

    return $this->getObject();
  }

  /**
   * Updates the values of the objects in embedded forms.
   *
   * @param array $values An array of values
   * @param array $forms  An array of forms
   */
  public function updateObjectEmbeddedForms($values, $forms = null)
  {
    if (null === $forms)
    {
      $forms = $this->embeddedForms;
    }

    foreach ($forms as $name => $form)
    {
      if (!isset($values[$name]) || !is_array($values[$name]))
      {
        continue;
      }

      if ($form instanceof sfFormObject)
      {
        $form->updateObject($values[$name]);
      }
      else
      {
        $this->updateObjectEmbeddedForms($values[$name], $form->getEmbeddedForms());
      }
    }
  }

  /**
   * Saves embedded form objects.
   *
   * @param mixed $con   An optional connection object
   * @param array $forms An array of forms
   */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    if (null === $forms)
    {
      $forms = $this->embeddedForms;
    }

    foreach ($forms as $form)
    {
      if ($form instanceof sfFormObject)
      {
        $form->getObject()->save($con);
        $form->saveEmbeddedForms($con);
      }
      else
      {
        $this->saveEmbeddedForms($con, $form->getEmbeddedForms());
      }
    }
  }

  /**
   * Renders a form tag suitable for the related object.
   *
   * The method is automatically guessed based on the Doctrine object:
   *
   *  * if the object is new, the method is POST
   *  * if the object already exists, the method is PUT
   *
   * @param  string $url         The URL for the action
   * @param  array  $attributes  An array of HTML attributes
   *
   * @return string An HTML representation of the opening form tag
   *
   * @see sfForm
   */
  public function renderFormTag($url, array $attributes = array())
  {
    if (!isset($attributes['method']))
    {
      $attributes['method'] = $this->isNew() ? 'post' : 'put';
    }

    return parent::renderFormTag($url, $attributes);
  }

  protected function camelize($text)
  {
    return preg_replace(array('#/(.?)#e', '/(^|_|-)+(.)/e'), array("'::'.strtoupper('\\1')", "strtoupper('\\2')"), $text);
  }
}
