<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormDate represents a date widget.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormDate.class.php 30762 2010-08-25 12:33:33Z fabien $
 */
class sfWidgetFormDate extends sfWidgetForm
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * format:       The date format string (%month%/%day%/%year% by default)
   *  * years:        An array of years for the year select tag (optional)
   *                  Be careful that the keys must be the years, and the values what will be displayed to the user
   *  * months:       An array of months for the month select tag (optional)
   *  * days:         An array of days for the day select tag (optional)
   *  * can_be_empty: Whether the widget accept an empty value (true by default)
   *  * empty_values: An array of values to use for the empty value (empty string for year, month, and day by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('format', '%month%/%day%/%year%');
    $this->addOption('days', parent::generateTwoCharsRange(1, 31));
    $this->addOption('months', parent::generateTwoCharsRange(1, 12));
    $years = range(date('Y') - 5, date('Y') + 5);
    $this->addOption('years', array_combine($years, $years));

    $this->addOption('can_be_empty', true);
    $this->addOption('empty_values', array('year' => '', 'month' => '', 'day' => ''));
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
    // convert value to an array
    $default = array('year' => null, 'month' => null, 'day' => null);
    if (is_array($value))
    {
      $value = array_merge($default, $value);
    }
    else
    {
      $value = (string) $value == (string) (integer) $value ? (integer) $value : strtotime($value);
      if (false === $value)
      {
        $value = $default;
      }
      else
      {
        $value = array('year' => date('Y', $value), 'month' => date('n', $value), 'day' => date('j', $value));
      }
    }

    $date = array();
    $emptyValues = $this->getOption('empty_values');

    $date['%day%'] = $this->renderDayWidget($name.'[day]', $value['day'], array('choices' => $this->getOption('can_be_empty') ? array('' => $emptyValues['day']) + $this->getOption('days') : $this->getOption('days'), 'id_format' => $this->getOption('id_format')), array_merge($this->attributes, $attributes));
    $date['%month%'] = $this->renderMonthWidget($name.'[month]', $value['month'], array('choices' => $this->getOption('can_be_empty') ? array('' => $emptyValues['month']) + $this->getOption('months') : $this->getOption('months'), 'id_format' => $this->getOption('id_format')), array_merge($this->attributes, $attributes));
    $date['%year%'] = $this->renderYearWidget($name.'[year]', $value['year'], array('choices' => $this->getOption('can_be_empty') ? array('' => $emptyValues['year']) + $this->getOption('years') : $this->getOption('years'), 'id_format' => $this->getOption('id_format')), array_merge($this->attributes, $attributes));

    return strtr($this->getOption('format'), $date);
  }

  /**
   * @param string $name
   * @param string $value
   * @param array $options
   * @param array $attributes
   * @return string rendered widget
   */
  protected function renderDayWidget($name, $value, $options, $attributes)
  {
    $widget = new sfWidgetFormSelect($options, $attributes);
    return $widget->render($name, $value);
  }

  /**
   * @param string $name
   * @param string $value
   * @param array $options
   * @param array $attributes
   * @return string rendered widget
   */
  protected function renderMonthWidget($name, $value, $options, $attributes)
  {
    $widget = new sfWidgetFormSelect($options, $attributes);
    return $widget->render($name, $value);
  }

  /**
   * @param string $name
   * @param string $value
   * @param array $options
   * @param array $attributes
   * @return string rendered widget
   */
  protected function renderYearWidget($name, $value, $options, $attributes)
  {
    $widget = new sfWidgetFormSelect($options, $attributes);
    return $widget->render($name, $value);
  }
}
