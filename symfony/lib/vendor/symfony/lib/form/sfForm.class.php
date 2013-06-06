<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfForm represents a form.
 *
 * A form is composed of a validator schema and a widget form schema.
 *
 * sfForm also takes care of CSRF protection by default.
 *
 * A CSRF secret can be any random string. If set to false, it disables the
 * CSRF protection, and if set to null, it forces the form to use the global
 * CSRF secret. If the global CSRF secret is also null, then a random one
 * is generated on the fly.
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfForm.class.php 29678 2010-05-30 14:38:42Z Kris.Wallsmith $
 */
class sfForm implements ArrayAccess, Iterator, Countable
{
  protected static
    $CSRFSecret        = false,
    $CSRFFieldName     = '_csrf_token',
    $toStringException = null;

  protected
    $widgetSchema    = null,
    $validatorSchema = null,
    $errorSchema     = null,
    $formFieldSchema = null,
    $formFields      = array(),
    $isBound         = false,
    $taintedValues   = array(),
    $taintedFiles    = array(),
    $values          = null,
    $defaults        = array(),
    $fieldNames      = array(),
    $options         = array(),
    $count           = 0,
    $localCSRFSecret = null,
    $embeddedForms   = array();

  /**
   * Constructor.
   *
   * @param array  $defaults    An array of field default values
   * @param array  $options     An array of options
   * @param string $CSRFSecret  A CSRF secret
   */
  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
  {
    $this->setDefaults($defaults);
    $this->options = $options;
    $this->localCSRFSecret = $CSRFSecret;

    $this->validatorSchema = new sfValidatorSchema();
    $this->widgetSchema    = new sfWidgetFormSchema();
    $this->errorSchema     = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setup();
    $this->configure();

    $this->addCSRFProtection($this->localCSRFSecret);
    $this->resetFormFields();
  }

  /**
   * Returns a string representation of the form.
   *
   * @return string A string representation of the form
   *
   * @see render()
   */
  public function __toString()
  {
    try
    {
      return $this->render();
    }
    catch (Exception $e)
    {
      self::setToStringException($e);

      // we return a simple Exception message in case the form framework is used out of symfony.
      return 'Exception: '.$e->getMessage();
    }
  }

  /**
   * Configures the current form.
   */
  public function configure()
  {
  }

  /**
   * Setups the current form.
   *
   * This method is overridden by generator.
   *
   * If you want to do something at initialization, you have to override the configure() method.
   *
   * @see configure()
   */
  public function setup()
  {
  }

  /**
   * Renders the widget schema associated with this form.
   *
   * @param  array  $attributes  An array of HTML attributes
   *
   * @return string The rendered widget schema
   */
  public function render($attributes = array())
  {
    return $this->getFormFieldSchema()->render($attributes);
  }

  /**
   * Renders the widget schema using a specific form formatter
   *
   * @param  string  $formatterName  The form formatter name
   * @param  array   $attributes     An array of HTML attributes
   *
   * @return string The rendered widget schema
   */
  public function renderUsing($formatterName, $attributes = array())
  {
    $currentFormatterName = $this->widgetSchema->getFormFormatterName();

    $this->widgetSchema->setFormFormatterName($formatterName);

    $output = $this->render($attributes);

    $this->widgetSchema->setFormFormatterName($currentFormatterName);

    return $output;
  }

  /**
   * Renders hidden form fields.
   *
   * @param boolean $recursive False will prevent hidden fields from embedded forms from rendering
   *
   * @return string
   * 
   * @see sfFormFieldSchema
   */
  public function renderHiddenFields($recursive = true)
  {
    return $this->getFormFieldSchema()->renderHiddenFields($recursive);
  }

  /**
   * Renders global errors associated with this form.
   *
   * @return string The rendered global errors
   */
  public function renderGlobalErrors()
  {
    return $this->widgetSchema->getFormFormatter()->formatErrorsForRow($this->getGlobalErrors());
  }

  /**
   * Returns true if the form has some global errors.
   *
   * @return Boolean true if the form has some global errors, false otherwise
   */
  public function hasGlobalErrors()
  {
    return (Boolean) count($this->getGlobalErrors());
  }

  /**
   * Gets the global errors associated with the form.
   *
   * @return array An array of global errors
   */
  public function getGlobalErrors()
  {
    return $this->widgetSchema->getGlobalErrors($this->getErrorSchema());
  }

  /**
   * Binds the form with input values.
   *
   * It triggers the validator schema validation.
   *
   * @param array $taintedValues  An array of input values
   * @param array $taintedFiles   An array of uploaded files (in the $_FILES or $_GET format)
   */
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    $this->taintedValues = $taintedValues;
    $this->taintedFiles  = $taintedFiles;
    $this->isBound = true;
    $this->resetFormFields();

    if (null === $this->taintedValues)
    {
      $this->taintedValues = array();
    }

    if (null === $this->taintedFiles)
    {
      if ($this->isMultipart())
      {
        throw new InvalidArgumentException('This form is multipart, which means you need to supply a files array as the bind() method second argument.');
      }

      $this->taintedFiles = array();
    }

    try
    {
      $this->doBind(self::deepArrayUnion($this->taintedValues, self::convertFileInformation($this->taintedFiles)));
      $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

      // remove CSRF token
      unset($this->values[self::$CSRFFieldName]);
    }
    catch (sfValidatorErrorSchema $e)
    {
      $this->values = array();
      $this->errorSchema = $e;
    }
  }

  /**
   * Cleans and binds values to the current form.
   *
   * @param array $values A merged array of values and files
   */
  protected function doBind(array $values)
  {
    $this->values = $this->validatorSchema->clean($values);
  }

  /**
   * Returns true if the form is bound to input values.
   *
   * @return Boolean true if the form is bound to input values, false otherwise
   */
  public function isBound()
  {
    return $this->isBound;
  }

  /**
   * Returns the submitted tainted values.
   *
   * @return array An array of tainted values
   */
  public function getTaintedValues()
  {
    if (!$this->isBound)
    {
      return array();
    }

    return $this->taintedValues;
  }

  /**
   * Returns true if the form is valid.
   *
   * It returns false if the form is not bound.
   *
   * @return Boolean true if the form is valid, false otherwise
   */
  public function isValid()
  {
    if (!$this->isBound)
    {
      return false;
    }

    return 0 == count($this->errorSchema);
  }

  /**
   * Returns true if the form has some errors.
   *
   * It returns false if the form is not bound.
   *
   * @return Boolean true if the form has no errors, false otherwise
   */
  public function hasErrors()
  {
    if (!$this->isBound)
    {
      return false;
    }

    return count($this->errorSchema) > 0;
  }

  /**
   * Returns the array of cleaned values.
   *
   * If the form is not bound, it returns an empty array.
   *
   * @return array An array of cleaned values
   */
  public function getValues()
  {
    return $this->isBound ? $this->values : array();
  }

  /**
   * Returns a cleaned value by field name.
   *
   * If the form is not bound, it will return null.
   *
   * @param  string  $field  The name of the value required
   * @return string  The cleaned value
   */
  public function getValue($field)
  {
    return ($this->isBound && isset($this->values[$field])) ? $this->values[$field] : null;
  }

  /**
   * Returns the array name under which user data can retrieved.
   *
   * If the user data is not stored under an array, it returns false.
   *
   * @return string|boolean The name or false if the name format is not an array format
   */
  public function getName()
  {
    if ('[%s]' != substr($nameFormat = $this->widgetSchema->getNameFormat(), -4))
    {
      return false;
    }

    return str_replace('[%s]', '', $nameFormat);
  }

  /**
   * Gets the error schema associated with the form.
   *
   * @return sfValidatorErrorSchema A sfValidatorErrorSchema instance
   */
  public function getErrorSchema()
  {
    return $this->errorSchema;
  }

  /**
   * Embeds a sfForm into the current form.
   *
   * @param string $name       The field name
   * @param sfForm $form       A sfForm instance
   * @param string $decorator  A HTML decorator for the embedded form
   */
  public function embedForm($name, sfForm $form, $decorator = null)
  {
    $name = (string) $name;
    if (true === $this->isBound() || true === $form->isBound())
    {
      throw new LogicException('A bound form cannot be embedded');
    }

    $this->embeddedForms[$name] = $form;

    $form = clone $form;
    unset($form[self::$CSRFFieldName]);

    $widgetSchema = $form->getWidgetSchema();

    $this->setDefault($name, $form->getDefaults());

    $decorator = null === $decorator ? $widgetSchema->getFormFormatter()->getDecoratorFormat() : $decorator;

    $this->widgetSchema[$name] = new sfWidgetFormSchemaDecorator($widgetSchema, $decorator);
    $this->validatorSchema[$name] = $form->getValidatorSchema();

    $this->resetFormFields();
  }

  /**
   * Embeds a sfForm into the current form n times.
   *
   * @param string  $name             The field name
   * @param sfForm  $form             A sfForm instance
   * @param integer $n                The number of times to embed the form
   * @param string  $decorator        A HTML decorator for the main form around embedded forms
   * @param string  $innerDecorator   A HTML decorator for each embedded form
   * @param array   $options          Options for schema
   * @param array   $attributes       Attributes for schema
   * @param array   $labels           Labels for schema
   */
  public function embedFormForEach($name, sfForm $form, $n, $decorator = null, $innerDecorator = null, $options = array(), $attributes = array(), $labels = array())
  {
    if (true === $this->isBound() || true === $form->isBound())
    {
      throw new LogicException('A bound form cannot be embedded');
    }

    $this->embeddedForms[$name] = new sfForm();

    $form = clone $form;
    unset($form[self::$CSRFFieldName]);

    $widgetSchema = $form->getWidgetSchema();

    // generate default values
    $defaults = array();
    for ($i = 0; $i < $n; $i++)
    {
      $defaults[$i] = $form->getDefaults();

      $this->embeddedForms[$name]->embedForm($i, $form);
    }

    $this->setDefault($name, $defaults);

    $decorator = null === $decorator ? $widgetSchema->getFormFormatter()->getDecoratorFormat() : $decorator;
    $innerDecorator = null === $innerDecorator ? $widgetSchema->getFormFormatter()->getDecoratorFormat() : $innerDecorator;

    $this->widgetSchema[$name] = new sfWidgetFormSchemaDecorator(new sfWidgetFormSchemaForEach(new sfWidgetFormSchemaDecorator($widgetSchema, $innerDecorator), $n, $options, $attributes), $decorator);
    $this->validatorSchema[$name] = new sfValidatorSchemaForEach($form->getValidatorSchema(), $n);

    // generate labels
    for ($i = 0; $i < $n; $i++)
    {
      if (!isset($labels[$i]))
      {
        $labels[$i] = sprintf('%s (%s)', $this->widgetSchema->getFormFormatter()->generateLabelName($name), $i);
      }
    }

    $this->widgetSchema[$name]->setLabels($labels);

    $this->resetFormFields();
  }

  /**
   * Gets the list of embedded forms.
   *
   * @return array An array of embedded forms
   */
  public function getEmbeddedForms()
  {
    return $this->embeddedForms;
  }

  /**
   * Returns an embedded form.
   *
   * @param  string $name The name used to embed the form
   *
   * @return sfForm
   * 
   * @throws InvalidArgumentException If there is no form embedded with the supplied name
   */
  public function getEmbeddedForm($name)
  {
    if (!isset($this->embeddedForms[$name]))
    {
      throw new InvalidArgumentException(sprintf('There is no embedded "%s" form.', $name));
    }

    return $this->embeddedForms[$name];
  }

  /**
   * Merges current form widget and validator schemas with the ones from the
   * sfForm object passed as parameter. Please note it also merge defaults.
   *
   * @param  sfForm   $form      The sfForm instance to merge with current form
   *
   * @throws LogicException      If one of the form has already been bound
   */
  public function mergeForm(sfForm $form)
  {
    if (true === $this->isBound() || true === $form->isBound())
    {
      throw new LogicException('A bound form cannot be merged');
    }

    $form = clone $form;
    unset($form[self::$CSRFFieldName]);

    $this->defaults = $form->getDefaults() + $this->defaults;

    foreach ($form->getWidgetSchema()->getPositions() as $field)
    {
      $this->widgetSchema[$field] = $form->getWidget($field);
    }

    foreach ($form->getValidatorSchema()->getFields() as $field => $validator)
    {
      $this->validatorSchema[$field] = $validator;
    }

    $this->getWidgetSchema()->setLabels($form->getWidgetSchema()->getLabels() + $this->getWidgetSchema()->getLabels());
    $this->getWidgetSchema()->setHelps($form->getWidgetSchema()->getHelps() + $this->getWidgetSchema()->getHelps());

    $this->mergePreValidator($form->getValidatorSchema()->getPreValidator());
    $this->mergePostValidator($form->getValidatorSchema()->getPostValidator());

    $this->resetFormFields();
  }

  /**
   * Merges a validator with the current pre validators.
   *
   * @param sfValidatorBase $validator A validator to be merged
   */
  public function mergePreValidator(sfValidatorBase $validator = null)
  {
    if (null === $validator)
    {
      return;
    }

    if (null === $this->validatorSchema->getPreValidator())
    {
      $this->validatorSchema->setPreValidator($validator);
    }
    else
    {
      $this->validatorSchema->setPreValidator(new sfValidatorAnd(array(
        $this->validatorSchema->getPreValidator(),
        $validator,
      )));
    }
  }

  /**
   * Merges a validator with the current post validators.
   *
   * @param sfValidatorBase $validator A validator to be merged
   */
  public function mergePostValidator(sfValidatorBase $validator = null)
  {
    if (null === $validator)
    {
      return;
    }

    if (null === $this->validatorSchema->getPostValidator())
    {
      $this->validatorSchema->setPostValidator($validator);
    }
    else
    {
      $this->validatorSchema->setPostValidator(new sfValidatorAnd(array(
        $this->validatorSchema->getPostValidator(),
        $validator,
      )));
    }
  }

  /**
   * Sets the validators associated with this form.
   *
   * @param array $validators An array of named validators
   *
   * @return sfForm The current form instance
   */
  public function setValidators(array $validators)
  {
    $this->setValidatorSchema(new sfValidatorSchema($validators));

    return $this;
  }

  /**
   * Set a validator for the given field name.
   *
   * @param string          $name      The field name
   * @param sfValidatorBase $validator The validator
   *
   * @return sfForm The current form instance
   */
  public function setValidator($name, sfValidatorBase $validator)
  {
    $this->validatorSchema[$name] = $validator;

    $this->resetFormFields();

    return $this;
  }

  /**
   * Gets a validator for the given field name.
   *
   * @param  string      $name      The field name
   *
   * @return sfValidatorBase $validator The validator
   */
  public function getValidator($name)
  {
    if (!isset($this->validatorSchema[$name]))
    {
      throw new InvalidArgumentException(sprintf('The validator "%s" does not exist.', $name));
    }

    return $this->validatorSchema[$name];
  }

  /**
   * Sets the validator schema associated with this form.
   *
   * @param sfValidatorSchema $validatorSchema A sfValidatorSchema instance
   *
   * @return sfForm The current form instance
   */
  public function setValidatorSchema(sfValidatorSchema $validatorSchema)
  {
    $this->validatorSchema = $validatorSchema;

    $this->resetFormFields();

    return $this;
  }

  /**
   * Gets the validator schema associated with this form.
   *
   * @return sfValidatorSchema A sfValidatorSchema instance
   */
  public function getValidatorSchema()
  {
    return $this->validatorSchema;
  }

  /**
   * Sets the widgets associated with this form.
   *
   * @param array $widgets An array of named widgets
   *
   * @return sfForm The current form instance
   */
  public function setWidgets(array $widgets)
  {
    $this->setWidgetSchema(new sfWidgetFormSchema($widgets));

    return $this;
  }

  /**
   * Set a widget for the given field name.
   *
   * @param string       $name   The field name
   * @param sfWidgetForm $widget The widget
   *
   * @return sfForm The current form instance
   */
  public function setWidget($name, sfWidgetForm $widget)
  {
    $this->widgetSchema[$name] = $widget;

    $this->resetFormFields();

    return $this;
  }

  /**
   * Gets a widget for the given field name.
   *
   * @param  string       $name      The field name
   *
   * @return sfWidgetForm $widget The widget
   */
  public function getWidget($name)
  {
    if (!isset($this->widgetSchema[$name]))
    {
      throw new InvalidArgumentException(sprintf('The widget "%s" does not exist.', $name));
    }

    return $this->widgetSchema[$name];
  }

  /**
   * Sets the widget schema associated with this form.
   *
   * @param sfWidgetFormSchema $widgetSchema A sfWidgetFormSchema instance
   *
   * @return sfForm The current form instance
   */
  public function setWidgetSchema(sfWidgetFormSchema $widgetSchema)
  {
    $this->widgetSchema = $widgetSchema;

    $this->resetFormFields();

    return $this;
  }

  /**
   * Gets the widget schema associated with this form.
   *
   * @return sfWidgetFormSchema A sfWidgetFormSchema instance
   */
  public function getWidgetSchema()
  {
    return $this->widgetSchema;
  }

  /**
   * Gets the stylesheet paths associated with the form.
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return $this->widgetSchema->getStylesheets();
  }

  /**
   * Gets the JavaScript paths associated with the form.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    return $this->widgetSchema->getJavaScripts();
  }

  /**
   * Returns the current form's options.
   *
   * @return array The current form's options
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Sets an option value.
   *
   * @param string $name  The option name
   * @param mixed  $value The default value
   *
   * @return sfForm The current form instance
   */
  public function setOption($name, $value)
  {
    $this->options[$name] = $value;

    return $this;
  }

  /**
   * Gets an option value.
   *
   * @param string $name    The option name
   * @param mixed  $default The default value (null by default)
   *
   * @param mixed  The default value
   */
  public function getOption($name, $default = null)
  {
    return isset($this->options[$name]) ? $this->options[$name] : $default;
  }

  /**
   * Sets a default value for a form field.
   *
   * @param string $name    The field name
   * @param mixed  $default The default value
   *
   * @return sfForm The current form instance
   */
  public function setDefault($name, $default)
  {
    $this->defaults[$name] = $default;

    $this->resetFormFields();

    return $this;
  }

  /**
   * Gets a default value for a form field.
   *
   * @param string $name The field name
   *
   * @param mixed  The default value
   */
  public function getDefault($name)
  {
    return isset($this->defaults[$name]) ? $this->defaults[$name] : null;
  }

  /**
   * Returns true if the form has a default value for a form field.
   *
   * @param string $name The field name
   *
   * @param Boolean true if the form has a default value for this field, false otherwise
   */
  public function hasDefault($name)
  {
    return array_key_exists($name, $this->defaults);
  }

  /**
   * Sets the default values for the form.
   *
   * The default values are only used if the form is not bound.
   *
   * @param array $defaults An array of default values
   *
   * @return sfForm The current form instance
   */
  public function setDefaults($defaults)
  {
    $this->defaults = null === $defaults ? array() : $defaults;

    if ($this->isCSRFProtected())
    {
      $this->setDefault(self::$CSRFFieldName, $this->getCSRFToken($this->localCSRFSecret ? $this->localCSRFSecret : self::$CSRFSecret));
    }

    $this->resetFormFields();

    return $this;
  }

  /**
   * Gets the default values for the form.
   *
   * @return array An array of default values
   */
  public function getDefaults()
  {
    return $this->defaults;
  }

  /**
   * Adds CSRF protection to the current form.
   *
   * @param string $secret The secret to use to compute the CSRF token
   *
   * @return sfForm The current form instance
   */
  public function addCSRFProtection($secret = null)
  {
    if (null === $secret)
    {
      $secret = $this->localCSRFSecret;
    }

    if (false === $secret || (null === $secret && false === self::$CSRFSecret))
    {
      return $this;
    }

    if (null === $secret)
    {
      if (null === self::$CSRFSecret)
      {
        self::$CSRFSecret = md5(__FILE__.php_uname());
      }

      $secret = self::$CSRFSecret;
    }

    $token = $this->getCSRFToken($secret);

    $this->validatorSchema[self::$CSRFFieldName] = new sfValidatorCSRFToken(array('token' => $token));
    $this->widgetSchema[self::$CSRFFieldName] = new sfWidgetFormInputHidden();
    $this->setDefault(self::$CSRFFieldName, $token);

    return $this;
  }

  /**
   * Returns a CSRF token, given a secret.
   *
   * If you want to change the algorithm used to compute the token, you
   * can override this method.
   *
   * @param  string $secret The secret string to use (null to use the current secret)
   *
   * @return string A token string
   */
  public function getCSRFToken($secret = null)
  {
    if (null === $secret)
    {
      $secret = $this->localCSRFSecret ? $this->localCSRFSecret : self::$CSRFSecret;
    }

    return md5($secret.session_id().get_class($this));
  }

  /**
   * @return true if this form is CSRF protected
   */
  public function isCSRFProtected()
  {
    return null !== $this->validatorSchema[self::$CSRFFieldName];
  }

  /**
   * Sets the CSRF field name.
   *
   * @param string $name The CSRF field name
   */
  static public function setCSRFFieldName($name)
  {
    self::$CSRFFieldName = $name;
  }

  /**
   * Gets the CSRF field name.
   *
   * @return string The CSRF field name
   */
  static public function getCSRFFieldName()
  {
    return self::$CSRFFieldName;
  }

  /**
   * Enables CSRF protection for this form.
   *
   * @param string $secret A secret to use when computing the CSRF token
   */
  public function enableLocalCSRFProtection($secret = null)
  {
    $this->localCSRFSecret = null === $secret ? true : $secret;
  }

  /**
   * Disables CSRF protection for this form.
   */
  public function disableLocalCSRFProtection()
  {
    $this->localCSRFSecret = false;
  }

  /**
   * Enables CSRF protection for all forms.
   *
   * The given secret will be used for all forms, except if you pass a secret in the constructor.
   * Even if a secret is automatically generated if you don't provide a secret, you're strongly advised
   * to provide one by yourself.
   *
   * @param string $secret A secret to use when computing the CSRF token
   */
  static public function enableCSRFProtection($secret = null)
  {
    self::$CSRFSecret = $secret;
  }

  /**
   * Disables CSRF protection for all forms.
   */
  static public function disableCSRFProtection()
  {
    self::$CSRFSecret = false;
  }

  /**
   * Returns true if the form is multipart.
   *
   * @return Boolean true if the form is multipart
   */
  public function isMultipart()
  {
    return $this->widgetSchema->needsMultipartForm();
  }

  /**
   * Renders the form tag.
   *
   * This methods only renders the opening form tag.
   * You need to close it after the form rendering.
   *
   * This method takes into account the multipart widgets
   * and converts PUT and DELETE methods to a hidden field
   * for later processing.
   *
   * @param  string $url         The URL for the action
   * @param  array  $attributes  An array of HTML attributes
   *
   * @return string An HTML representation of the opening form tag
   */
  public function renderFormTag($url, array $attributes = array())
  {
    $attributes['action'] = $url;
    $attributes['method'] = isset($attributes['method']) ? strtolower($attributes['method']) : 'post';
    if ($this->isMultipart())
    {
      $attributes['enctype'] = 'multipart/form-data';
    }

    $html = '';
    if (!in_array($attributes['method'], array('get', 'post')))
    {
      $html = $this->getWidgetSchema()->renderTag('input', array('type' => 'hidden', 'name' => 'sf_method', 'value' => $attributes['method'], 'id' => false));
      $attributes['method'] = 'post';
    }

    return sprintf('<form%s>', $this->getWidgetSchema()->attributesToHtml($attributes)).$html;
  }

  public function resetFormFields()
  {
    $this->formFields = array();
    $this->formFieldSchema = null;
  }

  /**
   * Returns true if the bound field exists (implements the ArrayAccess interface).
   *
   * @param  string $name The name of the bound field
   *
   * @return Boolean true if the widget exists, false otherwise
   */
  public function offsetExists($name)
  {
    return isset($this->widgetSchema[$name]);
  }

  /**
   * Returns the form field associated with the name (implements the ArrayAccess interface).
   *
   * @param  string $name  The offset of the value to get
   *
   * @return sfFormField   A form field instance
   */
  public function offsetGet($name)
  {
    if (!isset($this->formFields[$name]))
    {
      if (!$widget = $this->widgetSchema[$name])
      {
        throw new InvalidArgumentException(sprintf('Widget "%s" does not exist.', $name));
      }

      if ($this->isBound)
      {
        $value = isset($this->taintedValues[$name]) ? $this->taintedValues[$name] : null;
      }
      else if (isset($this->defaults[$name]))
      {
        $value = $this->defaults[$name];
      }
      else
      {
        $value = $widget instanceof sfWidgetFormSchema ? $widget->getDefaults() : $widget->getDefault();
      }

      $class = $widget instanceof sfWidgetFormSchema ? 'sfFormFieldSchema' : 'sfFormField';

      $this->formFields[$name] = new $class($widget, $this->getFormFieldSchema(), $name, $value, $this->errorSchema[$name]);
    }

    return $this->formFields[$name];
  }

  /**
   * Throws an exception saying that values cannot be set (implements the ArrayAccess interface).
   *
   * @param string $offset (ignored)
   * @param string $value (ignored)
   *
   * @throws <b>LogicException</b>
   */
  public function offsetSet($offset, $value)
  {
    throw new LogicException('Cannot update form fields.');
  }

  /**
   * Removes a field from the form.
   *
   * It removes the widget and the validator for the given field.
   *
   * @param string $offset The field name
   */
  public function offsetUnset($offset)
  {
    unset(
      $this->widgetSchema[$offset],
      $this->validatorSchema[$offset],
      $this->defaults[$offset],
      $this->taintedValues[$offset],
      $this->values[$offset],
      $this->embeddedForms[$offset]
    );

    $this->resetFormFields();
  }

  /**
   * Removes all visible fields from the form except the ones given as an argument.
   *
   * Hidden fields are not affected.
   *
   * @param array   $fields  An array of field names
   * @param Boolean $ordered Whether to use the array of field names to reorder the fields
   */
  public function useFields(array $fields = array(), $ordered = true)
  {
    $hidden = array();

    foreach ($this as $name => $field)
    {
      if ($field->isHidden())
      {
        $hidden[] = $name;
      }
      else if (!in_array($name, $fields))
      {
        unset($this[$name]);
      }
    }

    if ($ordered)
    {
      $this->widgetSchema->setPositions(array_merge($fields, $hidden));
    }
  }

  /**
   * Returns a form field for the main widget schema.
   *
   * @return sfFormFieldSchema A sfFormFieldSchema instance
   */
  public function getFormFieldSchema()
  {
    if (null === $this->formFieldSchema)
    {
      $values = $this->isBound ? $this->taintedValues : $this->defaults + $this->widgetSchema->getDefaults();

      $this->formFieldSchema = new sfFormFieldSchema($this->widgetSchema, null, null, $values, $this->errorSchema);
    }

    return $this->formFieldSchema;
  }

  /**
   * Resets the field names array to the beginning (implements the Iterator interface).
   */
  public function rewind()
  {
    $this->fieldNames = $this->widgetSchema->getPositions();

    reset($this->fieldNames);
    $this->count = count($this->fieldNames);
  }

  /**
   * Gets the key associated with the current form field (implements the Iterator interface).
   *
   * @return string The key
   */
  public function key()
  {
    return current($this->fieldNames);
  }

  /**
   * Returns the current form field (implements the Iterator interface).
   *
   * @return mixed The escaped value
   */
  public function current()
  {
    return $this[current($this->fieldNames)];
  }

  /**
   * Moves to the next form field (implements the Iterator interface).
   */
  public function next()
  {
    next($this->fieldNames);
    --$this->count;
  }

  /**
   * Returns true if the current form field is valid (implements the Iterator interface).
   *
   * @return boolean The validity of the current element; true if it is valid
   */
  public function valid()
  {
    return $this->count > 0;
  }

  /**
   * Returns the number of form fields (implements the Countable interface).
   *
   * @return integer The number of embedded form fields
   */
  public function count()
  {
    return count($this->getFormFieldSchema());
  }

  /**
   * Converts uploaded file array to a format following the $_GET and $POST naming convention.
   *
   * It's safe to pass an already converted array, in which case this method just returns the original array unmodified.
   *
   * @param  array $taintedFiles An array representing uploaded file information
   *
   * @return array An array of re-ordered uploaded file information
   */
  static public function convertFileInformation(array $taintedFiles)
  {
    $files = array();
    foreach ($taintedFiles as $key => $data)
    {
      $files[$key] = self::fixPhpFilesArray($data);
    }

    return $files;
  }

  static protected function fixPhpFilesArray($data)
  {
    $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');
    $keys = array_keys($data);
    sort($keys);

    if ($fileKeys != $keys || !isset($data['name']) || !is_array($data['name']))
    {
      return $data;
    }

    $files = $data;
    foreach ($fileKeys as $k)
    {
      unset($files[$k]);
    }
    foreach (array_keys($data['name']) as $key)
    {
      $files[$key] = self::fixPhpFilesArray(array(
        'error'    => $data['error'][$key],
        'name'     => $data['name'][$key],
        'type'     => $data['type'][$key],
        'tmp_name' => $data['tmp_name'][$key],
        'size'     => $data['size'][$key],
      ));
    }

    return $files;
  }

  /**
   * Returns true if a form thrown an exception in the __toString() method
   *
   * This is a hack needed because PHP does not allow to throw exceptions in __toString() magic method.
   *
   * @return boolean
   */
  static public function hasToStringException()
  {
    return null !== self::$toStringException;
  }

  /**
   * Gets the exception if one was thrown in the __toString() method.
   *
   * This is a hack needed because PHP does not allow to throw exceptions in __toString() magic method.
   *
   * @return Exception
   */
  static public function getToStringException()
  {
    return self::$toStringException;
  }

  /**
   * Sets an exception thrown by the __toString() method.
   *
   * This is a hack needed because PHP does not allow to throw exceptions in __toString() magic method.
   *
   * @param Exception $e The exception thrown by __toString()
   */
  static public function setToStringException(Exception $e)
  {
    if (null === self::$toStringException)
    {
      self::$toStringException = $e;
    }
  }

  public function __clone()
  {
    $this->widgetSchema    = clone $this->widgetSchema;
    $this->validatorSchema = clone $this->validatorSchema;

    // we rebind the cloned form because Exceptions are not clonable
    if ($this->isBound())
    {
      $this->bind($this->taintedValues, $this->taintedFiles);
    }
  }

  /**
   * Merges two arrays without reindexing numeric keys.
   *
   * @param array $array1 An array to merge
   * @param array $array2 An array to merge
   *
   * @return array The merged array
   */
  static protected function deepArrayUnion($array1, $array2)
  {
    foreach ($array2 as $key => $value)
    {
      if (is_array($value) && isset($array1[$key]) && is_array($array1[$key]))
      {
        $array1[$key] = self::deepArrayUnion($array1[$key], $value);
      }
      else
      {
        $array1[$key] = $value;
      }
    }

    return $array1;
  }
}
