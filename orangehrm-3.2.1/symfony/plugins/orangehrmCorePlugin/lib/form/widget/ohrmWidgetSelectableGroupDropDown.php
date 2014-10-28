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
 * Select dropdown with selectable group headings.
 */
class ohrmWidgetSelectableGroupDropDown extends sfWidgetFormSelect {

    /**
     * Constructor. Add 'class' selectableGroupWidget.
     * 
     * @see sfFormWidget
     */
    public function __construct($options = array(), $attributes = array()) {
        $class = 'selectableGroupWidget';

        if (isset($attributes['class'])) {
            $attributes['class'] .= ' ' . $class;
        } else {
            $attributes['class'] = $class;
        }

        parent::__construct($options, $attributes);
    }

    /**
     *
     * Available options:
     *
     *  * choices:  An array of possible choices (required). Pass 2D array in
     *              the following format to get group headings:
     *  <pre>
     *     array('Sri Lanka' => array('KDY'=>'Kandy', 'CBO'=>'Colombo', 'MT'=>'Matara'),
     *           'Japan' => array('TK'=>'Tokyo', 'YK'=>'Yokohama'),
     *           'MO' => 'Moskow')
     *  </pre>
     *  * show_all_option: Add an All option 
     *  * all_option_label: Label for All option. Defaults to "All"
     *  * show_select_option: Add a -- Select -- option
     *  * select_option_label: Label for Select option. Defaults to "-- Select --"
     *  * select_option_value: Value for Select option. Defaults to -1.
     *  * indent: Should sub items be indented (true)
     *  * indent_string: String to use for indentation (defaults to &nbsp;&nbsp;)
     *
     * @param array $options     An array of options
     * @param array $attributes  An array of default HTML attributes
     *
     * @see sfWidgetFormChoiceBase
     */
    protected function configure($options = array(), $attributes = array()) {

        parent::configure($options, $attributes);

        $this->addOption('show_all_option', true);
        $this->addOption('all_option_label', __('All'));

        $this->addOption('show_select_option', false);
        $this->addOption('select_option_label', '-- ' . __('Select') . ' --');
        $this->addOption('select_option_value', -1);

        $this->addOption('indent', true);
        $this->addOption('indent_string', "&nbsp;&nbsp;");
    }

    /**
     * Returns an array of option tags for the given choices
     *
     * @param  string $value    The selected value
     * @param  array  $choices  An array of choices
     *
     * @return array  An array of option tags
     */
    protected function getOptionsForSelect($value, $choices, $level = 0) {
        $mainAttributes = $this->attributes;
        $this->attributes = array();

        $indent = $this->getOption('indent');
        $indentWith = $this->getOption('indent_string');

        if (!is_array($value)) {
            $value = array($value);
        }

        $value = array_map('strval', array_values($value));
        $value_set = array_flip($value);
        $options = array();

        if ($level == 0) {
            if ($this->getOption('show_select_option')) {
                $selectLabel = self::escapeOnce($this->getOption('select_option_label'));
                $selectValue = self::escapeOnce($this->getOption('select_option_value'));
                $options[] = $this->renderContentTag('option', $selectLabel, array('value' => $selectValue));
            }
            if ($this->getOption('show_all_option')) {

                $allLabel = self::escapeOnce($this->getOption('all_option_label'));
                $allValue = ($this->getOption('all_option_value')) ? $this->getOption('all_option_value') : implode(',', $this->getAllKeys($choices));
                $allAttributes = array('value' => $allValue);

                if (isset($value_set[strval($allValue)])) {
                    $allAttributes['selected'] = 'selected';
                }
                $options[] = $this->renderContentTag('option', $allLabel, $allAttributes);
                $level++;
            }
        }
// TODO: Use CSS for indenting instead of using indent with.
// Then we can only indent the drop down and have select not indented.

        foreach ($choices as $key => $option) {
            $attributes = array();

            if (is_array($option) && !empty($option)) {
                $subOptions = $this->getOptionsForSelect($value, $option, $level + 1);

                $groupValue = implode(',', array_keys($option)) . ",-1";

                $attributes['class'] = 'optiongroup';
                $attributes['value'] = $groupValue;

                if (isset($value_set[$groupValue])) {
                    $attributes['selected'] = 'selected';
                }

                $label = self::escapeOnce($key);
                if ($indent && $level > 0) {
                    $label = str_repeat($indentWith, $level) . $label;
                }
                $options[] = $this->renderContentTag('option', $label, $attributes);

                $options = array_merge($options, $subOptions);
            } else {
                $attributes['value'] = self::escapeOnce($key);
                if (isset($value_set[strval($key)])) {
                    $attributes['selected'] = 'selected';
                }
                $label = self::escapeOnce($option);

                if ($indent && $level > 0) {
                    $label = str_repeat($indentWith, $level) . $label;
                }

                $options[] = $this->renderContentTag('option', $label, $attributes);
            }
        }

        $this->attributes = $mainAttributes;

        return $options;
    }

    public function getAllKeys($choices) {
        $keys = array();

        foreach ($choices as $key => $value) {
            if (is_array($value)) {
                $keys = array_merge($keys, $this->getAllKeys($value));
            } else {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    public function getStylesheets() {
        $styleSheets = parent::getStylesheets();
        $styleSheets[plugin_web_path('orangehrmCorePlugin', 'css/ohrmWidgetSelectableGroupDropDown.css')] = 'all';

        return($styleSheets);
    }

}

