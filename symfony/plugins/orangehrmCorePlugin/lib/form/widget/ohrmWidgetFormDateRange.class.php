<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

/**
 * Description of ohrmWidgetFormDateRange
 */
class ohrmWidgetFormDateRange extends sfWidgetFormDateRange {

  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * from_label:  The label for the from date widget
   *  * to_label:    The label for the to date widget
   * 
   *  * from_label_template: The template used to render label for from date widget
   *                 Available placeholders: %from_id%, %from_label%
   *  * to_label_template: The template used to render label for to date widget
   *                 Available placeholders: %to_id%, %to_label%
   * 
   *  * template:    The template to use to render the widget
   *                 Available placeholders: %from_date%, %to_date% %from_label% %to_label%
   *
   *  also see options in sfWidgetFormDateRange
   * 
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see ohrmWidgetFormDateRange
   */
  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);
    
    $this->addOption('from_label', '');
    $this->addOption('to_label', 'to');

    $this->addOption('from_label_template', "<label for='%from_id%' class='date_range_label'>%from_label%</label>");
    $this->addOption('to_label_template', "<label>&nbsp;</label><label for='%to_id%' class='date_range_label'>%to_label%</label>");
    $this->addOption('use_separate_containers', true);
    $this->addOption('container_separator', '</li><li>');
    $this->addOption('template', '%from_label% %from_date% %container_separator%%to_label% %to_date%');
    

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
  public function render($name, $value = null, $attributes = array(), $errors = array()) {
    $value = array_merge(array('from' => '', 'to' => ''), is_array($value) ? $value : array());

    $fromWidget = $this->getOption('from_date');
    $fromId = $fromWidget->getAttribute('id');
    if (empty($fromId)) {
        $fromId = $this->generateId($name . '_from');
        $fromWidget->setAttribute('id', $fromId);
    }
    $toWidget = $this->getOption('to_date');                
    $toId = $toWidget->getAttribute('id');   
    if (empty($toId)) {
        $toId = $this->generateId($name . '_to');
        $toWidget->setAttribute('id', $toId);
    }    
    
    $fromLabelHtml = '';
    $fromLabel = $this->getOption('from_label');
    if (!empty($fromLabel)) {
        
       
        $fromLabelHtml = strtr($this->getOption('from_label_template'), array(
            '%from_id%' => $fromId,
            '%from_label%' => $this->translate($fromLabel)
        ));
    }
    
    $toLabel = $this->getOption('to_label');
    $toLabelHtml = '';
    if (!empty($toLabel)) {

        $toLabelHtml = strtr($this->getOption('to_label_template'), array(
            '%to_id%' => $toId,
            '%to_label%' => $this->translate($toLabel)
        ));

    }    
    
    $containerSeparator = '';
    if ($this->getOption('use_separate_containers')) {
        $containerSeparator = $this->getOption('container_separator');
    }

    return strtr($this->translate($this->getOption('template')), array(
      '%from_label%' => $fromLabelHtml,
      '%to_label%' => $toLabelHtml,
      '%from_date%' => $fromWidget->render($name.'[from]', $value['from']),
      '%to_date%' => $toWidget->render($name.'[to]', $value['to']),
      '%container_separator%' => $containerSeparator,
    ));
  }  
}
