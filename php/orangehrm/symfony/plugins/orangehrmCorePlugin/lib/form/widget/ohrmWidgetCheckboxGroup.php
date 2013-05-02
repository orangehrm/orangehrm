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
 */

/**
 * Description of ohrmWidgetCheckboxGroup
 *
 */
class ohrmWidgetCheckboxGroup extends sfWidgetFormSelectCheckbox {

    protected $id;
    protected $allOptionId;

    protected function configure($options = array(), $attributes = array()) {
        parent::configure($options, $attributes);

        // option value for 'all' checkbox. Set to a valid option to enable the 'All'
        // checkbox.
        $this->addOption('show_all_option', false);

        $this->addOption('all_option_label', __('All'));

        $this->addOption('all_option_first', true);

        // Separator between label and input
        $this->addOption('label_separator', '&nbsp;');

        // Separator between inputs
        $this->addOption('separator', "\n");

        // Formatter class
        $this->addOption('formatter', array($this, 'formatter'));

        // Label first (if true)
        $this->addOption('label_first', true);

        // Container tag for one label : input pair
        $this->addOption('item_container', null);

        // Container tag for widget. Defaults to 'div' if not supplied or null.
        $this->addOption('widget_container', 'div');

        // Container tag for 
        $this->addOption('template', '%group% %options%');

        $this->addOption('class', 'checkbox_group');
    }

    /**
     * Renders the widget.
     *
     * @param  string $name        The element name
     * @param  string $value       The value selected in this widget
     * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
     * @param  array  $errors      An array of errors for the field
     *
     * @return string An HTML tag string
     *
     * @see sfWidgetForm
     */
    public function render($name, $value = null, $attributes = array(), $errors = array()) {

        if ('[]' != substr($name, -2)) {
            $name .= '[]';
        }

        if (isset($attributes['id'])) {
            $this->setId($attributes['id']);
        } else {
            $this->setId($this->generateId($name, 'checkboxgroup'));
        }

        $this->setAllCheckboxId($this->getId() . '_allcheck');

        $html = parent::render($name, $value, $attributes, $errors);

        // Add javascript (only if we have an 'All' checkbox
        if ($this->getOption('show_all_option')) {
            $allOptionId = $this->getAllCheckboxId();

            $template = <<< EOF
<script type="text/javascript">

$(document).ready(function() {

    $('#{all.checkbox.id}').click(function() {
       var checked = $(this).is(":checked");
       $('#{container.id} input[type="checkbox"]').attr('checked', checked);
    });
                                
    $('#{container.id} input[type="checkbox"]').click(function() {
        var notCheckedCount = $('#{container.id} input[name="{field.name}"]:not(:checked)').length;
        var check = (notCheckedCount == 0);

        $('#{all.checkbox.id}').attr('checked', check);
    });
});

 </script>
EOF;

            $templateVars = array(
                '{all.checkbox.id}' => $allOptionId,
                '{container.id}' => $this->getId(),
                '{field.name}' => $name
            );

            $javascript = strtr($template, $templateVars);
            $html .= $javascript;
        }

        return $html;
    }

    protected function formatChoices($name, $value, $choices, $attributes) {
        $inputs = array();
        $checkedCount = 0;

        foreach ($choices as $key => $option) {
            $baseAttributes = array(
                'name' => $name,
                'type' => 'checkbox',
                'value' => self::escapeOnce($key),
                'id' => $id = $this->generateId($name, self::escapeOnce($key)),
            );

            if ((is_array($value) && in_array(strval($key), $value)) || strval($key) == strval($value)) {
                $baseAttributes['checked'] = 'checked';
                $checkedCount++;
            }

            $inputs[$id] = array(
                'input' => $this->renderTag('input', array_merge($baseAttributes, $attributes)),
                'label' => $this->renderContentTag('label', self::escapeOnce($option), array('for' => $id)),
            );
        }

        $showAllOption = $this->getOption('show_all_option');

        // Add the "ALL" checkbox if requested.
        if ($showAllOption) {

            $allCheckboxId = $this->getAllCheckboxId();
            $allCheckboxLabel = self::escapeOnce($this->getOption('all_option_label'));

            $allCheckboxAttributes = array(
                'id' => $allCheckboxId,
                'type' => 'checkbox'
            );

            // If all checkboxes are checked
            if ($checkedCount == count($choices)) {
                $allCheckboxAttributes['checked'] = 'checked';
            }

            $allCheckbox = array(
                'input' => $this->renderTag('input', $allCheckboxAttributes),
                'label' => $this->renderContentTag('label', $allCheckboxLabel, array('for' => $allCheckboxId)),
            );

            $allOptionFirst = $this->getOption('all_option_first');
            
            if ($allOptionFirst) {
                $inputs = array($allCheckboxId => $allCheckbox) + $inputs;
            } else {
                $inputs[$allCheckboxId] = $allCheckbox;
            }
        }

        return call_user_func($this->getOption('formatter'), $this, $inputs);
    }

    public function formatter($widget, $inputs) {

        $rows = array();

        $labelFirst = $this->getOption('label_first');
        $itemContainer = $this->getOption('item_container');
        $widgetContainer = $this->getOption('widget_container');

        if (empty($widgetContainer)) {
            $widgetContainer = 'div';
        }

        foreach ($inputs as $input) {

            if ($labelFirst) {
                $row = $input['label'] . $this->getOption('label_separator') . $input['input'];
            } else {
                $row = $input['input'] . $this->getOption('label_separator') . $input['label'];
            }

            if (!empty($itemContainer)) {
                $rows[] = $this->renderContentTag($itemContainer, $row);
            } else {
                $rows[] = $row;
            }
        }

        $html = "";
        
        $class = $this->getOption('class');
        if ($labelFirst) {
            $class .= ' label_first';
        }

        if ($rows) {
            $html = $this->renderContentTag('div', implode($this->getOption('separator'), $rows), array('class' => $class,
                'id' => $widget->getId()));
        }

        return $html;
    }

    public function getStylesheets() {
        $styleSheets = parent::getStylesheets();
        $styleSheets[plugin_web_path('orangehrmCorePlugin', 'css/ohrmWidgetCheckboxGroup.css')] = 'all';

        return($styleSheets);
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getAllCheckboxId() {
        return $this->allCheckboxId;
    }

    public function setAllCheckboxId($id) {
        $this->allCheckboxId = $id;
    }

}

