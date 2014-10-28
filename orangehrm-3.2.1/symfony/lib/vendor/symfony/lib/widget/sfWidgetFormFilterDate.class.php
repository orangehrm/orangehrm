<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormFilterDate represents a date filter widget.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormFilterDate.class.php 30762 2010-08-25 12:33:33Z fabien $
 */
class sfWidgetFormFilterDate extends sfWidgetFormDateRange
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * with_empty:      Whether to add the empty checkbox (true by default)
   *  * empty_label:     The label to use when using an empty checkbox
   *  * template:        The template used for from date and to date
   *                     Available placeholders: %from_date%, %to_date%
   *  * filter_template: The template to use to render the widget
   *                     Available placeholders: %date_range%, %empty_checkbox%, %empty_label%
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('with_empty', true);
    $this->addOption('empty_label', 'is empty');
    $this->addOption('template', 'from %from_date%<br />to %to_date%');
    $this->addOption('filter_template', '%date_range%<br />%empty_checkbox% %empty_label%');
  }

  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The date displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $values = array_merge(array('is_empty' => ''), is_array($value) ? $value : array());

    return strtr($this->getOption('filter_template'), array(
      '%date_range%'     => parent::render($name, $value, $attributes, $errors),
      '%empty_checkbox%' => $this->getOption('with_empty') ? $this->renderTag('input', array('type' => 'checkbox', 'name' => $name.'[is_empty]', 'checked' => $values['is_empty'] ? 'checked' : '')) : '',
      '%empty_label%'    => $this->getOption('with_empty') ? $this->renderContentTag('label', $this->translate($this->getOption('empty_label')), array('for' => $this->generateId($name.'[is_empty]'))) : '',
    ));
  }
}
