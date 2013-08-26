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
 * Widget representing a time range (start time and end time)
 * Optionally shows duration
 */
class ohrmWidgetFormTimeRange extends sfWidgetForm {

    /**
     * Configures the current widget.
     *
     * Available options:
     *
     *  * from_time:   The from date widget (required)
     *  * to_time:     The to date widget (required)
     *  * show_duration: Show time duration (optional) - default true
     *  * from_label: Label for from widget
     *  * to_label: Label for to widget
     *  * duration_label: Label for duration widget
     * 
     *  * from_label_template: The template used to render label for from time widget
     *                 Available placeholders: %for_id%, %label%
     *  * to_label_template: The template used to render label for to time widget
     *                 Available placeholders: %for_id%, %label%
     *  * duration_label_template: The template used to render label for the duration
     *                 Available placeholders: %label%
     *  * template:    The template to use to render the widget
     *                 Available placeholders: %from_time%, %to_time%, %duration%,
     *                 %from_label%, %to_label%, %duration_label%
     *
     * @param array $options     An array of options
     * @param array $attributes  An array of default HTML attributes
     *
     * @see sfWidgetForm
     */
    protected function configure($options = array(), $attributes = array()) {
        $this->addRequiredOption('from_time');
        $this->addRequiredOption('to_time');
        $this->addOption('show_duration', true);

        $this->addOption('from_label', __('From'));
        $this->addOption('to_label', __('To'));
        $this->addOption('duration_label', __('Duration'));        

        $this->addOption('from_label_template', "<label for='%for_id%' class='time_range_label'>%label%</label>");
        $this->addOption('to_label_template', "<label for='%for_id%' class='time_range_label'>%label%</label>");
        $this->addOption('duration_label_template', "<label class='time_range_label'>%label%</label>");
        $this->addOption('duration_template', "<input disabled='disabled' type='text' class='time_range_duration' value='%duration%'/>");

        $this->addOption('template', '%from_label% %from_time% %to_label% %to_time% %duration_label% %duration%');
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

        $fromWidget = $this->getOption('from_time');
        $fromId = $fromWidget->getAttribute('id');
        if (empty($fromId)) {
            $fromId = $this->generateId($name . '_from');
            $fromWidget->setAttribute('id', $fromId);
        }        
        
        $toWidget = $this->getOption('to_time');
        $toId = $toWidget->getAttribute('id');
        if (empty($toId)) {
            $toId = $this->generateId($name . '_to');
            $toWidget->setAttribute('id', $toId);
        }          
        
        $fromLabelHtml = '';
        $fromLabel = $this->getOption('from_label');

        if (!empty($fromLabel)) {
            $fromLabelHtml = strtr($this->getOption('from_label_template'), array(
                '%for_id%' => $fromId,
                '%label%' => $this->translate($fromLabel)
                    ));
        }

        $toLabel = $this->getOption('to_label');
        $toLabelHtml = '';
        if (!empty($toLabel)) {
            $toLabelHtml = strtr($this->getOption('to_label_template'), array(
                '%for_id%' => $toId,
                '%label%' => $this->translate($toLabel)
                    ));
        }
        
        $durationLabel = $this->getOption('duration_label');
        $durationLabelHtml = '';

        if (!empty($durationLabel)) {
            $durationLabelHtml = strtr($this->getOption('duration_label_template'), array(
                        '%label%' => $this->translate($durationLabel)
                    ));
        }        
        
        $from = $value['from'];
        $to = $value['to'];                
        
        $duration = $this->getTimeDifference($from, $to);
        
        $durationHtml = strtr($this->getOption('duration_template'), array(
                        '%duration%' => $this->translate($duration)
                    ));

        return strtr($this->translate($this->getOption('template')), array(
                    '%from_label%' => $fromLabelHtml,
                    '%to_label%' => $toLabelHtml,
                    '%duration_label%' => $durationLabelHtml,
                    '%duration%' => $durationHtml,
                    '%from_time%' => $fromWidget->render($name . '[from]', $value['from']),
                    '%to_time%' => $toWidget->render($name . '[to]', $value['to']),
                ));
    }

    /**
     * Gets the stylesheet paths associated with the widget.
     *
     * @return array An array of stylesheet paths
     */
    public function getStylesheets() {
        return array_unique(array_merge($this->getOption('from_time')->getStylesheets(), $this->getOption('to_time')->getStylesheets()));
    }

    /**
     * Gets the JavaScript paths associated with the widget.
     *
     * @return array An array of JavaScript paths
     */
    public function getJavaScripts() {
        return array_unique(array_merge($this->getOption('from_time')->getJavaScripts(), $this->getOption('to_time')->getJavaScripts()));
    }
    
    public function getTimeDifference($fromTime, $toTime) {
        $difference = '';
        
        if (!empty($fromTime) && !empty($toTime)) {
            list($fromHours, $fromMinutes) = explode(':', $fromTime);
            list($toHours, $toMinutes) = explode(':', $toTime);
            
            if (is_numeric($fromHours) && is_numeric($fromMinutes) && is_numeric($toHours) && is_numeric($toMinutes)) {
                $fromMinutes = intval($fromMinutes) + 60 * intval($fromHours);
                $toMinutes = intval($toMinutes) + 60 * intval($toHours);
                
                $diffMinutes = $toMinutes - $fromMinutes;
                $diffHours = round($diffMinutes / 60, 2);
                
                $difference = number_format($diffHours, 2);
            }
        }
        
        return $difference;
    }

}
