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
 * Widget representing leave duration for a single day
 * Includes a duration drop down (full day, half day, specify time) with related
 * input fields
 */
class ohrmWidgetFormLeaveDuration extends sfWidgetForm {
    
    const HALF_DAY_AM = 'AM';
    const HALF_DAY_PM = 'PM';
    
    private $workShiftService;

    public function getWorkShiftService() {
        if (is_null($this->workShiftService)) {
            $this->workShiftService = new WorkShiftService();
            $this->workShiftService->setWorkShiftDao(new WorkShiftDao());
        }
        return $this->workShiftService;
    }
    
    /**
     * Configures the current widget.
     *
     * Available options:
     *
     *  * enable_full_day:   Display the "Full Day" option
     *  * enable_half_day:   Display the "Half Day" option
     *  * enable_specify_time:   Display the "Specify Time" option
     *  * default_duration: Default selection of Duration drop down. (default: full_day),
     *                 Available options (full_day, half_day, specify_time)
     *  * template:    The template to use to render the widget
     *                 Available placeholders: %duration%, %full_day_content%, %half_day_content%,
     *                 %specify_time_content%
     *  * full_day_template:    The template to use to render full_day_content
     *                 Available placeholders: %full_day_content_id%
     *  * half_day_template:    The template to use to render half_day_content
     *                 Available placeholders: %half_day_content_id%, %am_pm%
     *  * specify_time_content:    The template to use to render specify_time_content
     *                 Available placeholders: %specify_time_content_id%, %time_range%
     *
     * @param array $options     An array of options
     * @param array $attributes  An array of default HTML attributes
     *
     * @see sfWidgetForm
     */
    protected function configure($options = array(), $attributes = array()) {
        $this->addOption('enable_full_day', true);
        $this->addOption('enable_half_day', true);
        $this->addOption('enable_specify_time', true);
        $this->addOption('default_duration', 'full_day');
        
        $this->addOption('template', '%duration% %full_day_content% %half_day_content% %specify_time_content%');
        $this->addOption('full_day_template', '');
        $this->addOption('half_day_template', '<span id=%half_day_content_id% %display%>%am_pm%</span>');
        $this->addOption('specify_time_content', '<span id=%specify_time_content_id% %display%>%time_range%</span>');
    }
    
    public function render($name, $value = null, $attributes = array(), $errors = array()) {
        
        if (empty($value) || !is_array($value)) {
            $value = array();
        }
        
        $defaultDuration = $this->getOption('default_duration');
        if (!isset($value['duration'])) {
            $value['duration'] = $defaultDuration;
        }
        
        if (!isset($value['ampm'])) {
            $value['ampm'] = self::HALF_DAY_AM;
        }
        
        if (!isset($value['time'])) {
            $value['time'] = $this->getDefaultTimeRangeValues();
        }
        
        $durationChoices = array();
        
        if ($this->getOption('enable_full_day')) {
            $durationChoices['full_day'] = __('Full Day');
        }
        
        if ($this->getOption('enable_half_day')) {
            $durationChoices['half_day'] = __('Half Day');
        }

        if ($this->getOption('enable_specify_time')) {
            $durationChoices['specify_time'] = __('Specify Time');
        }
        
        
        $hideCss = 'style="display:none"';
        
        $durationWidget = new sfWidgetFormSelect(array('choices' => $durationChoices), 
                array('class' => 'leave_duration_dropdown'));
        $halfDayWidget = new sfWidgetFormSelect(array('choices' => array(self::HALF_DAY_AM => __('Morning'), self::HALF_DAY_PM => __('Afternoon'))), 
                array('class' => 'leave_duration_ampm'));        
            
        // if 
        $timeWidget = new ohrmWidgetFormTimeRange(array(
                    'from_time' => new ohrmWidgetTimeDropDown(),
                    'to_time' => new ohrmWidgetTimeDropDown())
            );
        
        // IDs
        $fullDayContentId = $this->generateId($name . '[full_day_content]');
        $halfDayContentId = $this->generateId($name . '[half_day_content]');
        $specifyTimeContentId = $this->generateId($name . '[specify_time_content]');
        
        $fullDayContent = strtr($this->getOption('full_day_template'), array(
            '%full_day_content_id%' => $fullDayContentId,
            '%display%' => ($defaultDuration == 'full_day') ? '' : $hideCss
        ));
        
        $halfDayContent = strtr($this->getOption('half_day_template'), array(
            '%half_day_content_id%' => $halfDayContentId,
            '%am_pm%' => $halfDayWidget->render($name . '[ampm]', $value['ampm']),
            '%display%' => ($defaultDuration == 'half_day') ? '' : $hideCss
        ));
        
        $specifyTimeContent = strtr($this->getOption('specify_time_content'), array(
            '%specify_time_content_id%' => $specifyTimeContentId,
            '%time_range%' => $timeWidget->render($name . '[time]', $value['time']),
            '%display%' => ($defaultDuration == 'specify_time') ? '' : $hideCss
        ));
        
        $html = strtr($this->getOption('template'), array(
            '%duration%' => $durationWidget->render($name . '[duration]', $value['duration']),
            '%full_day_content%' => $fullDayContent,
            '%half_day_content%' => $halfDayContent,
            '%specify_time_content%' => $specifyTimeContent
        ));
        
$javaScript = <<<EOF
<script type="text/javascript">

    $(document).ready(function(){
        orangehrm.widgets.formLeaveDuration.handleDurationChange($("#%duration_id%").val(), '%full_day_content_id%', '%half_day_content_id%', '%specify_time_content_id%');
        $("#%duration_id%").change(function() {
            orangehrm.widgets.formLeaveDuration.handleDurationChange($(this).val(), '%full_day_content_id%', '%half_day_content_id%', '%specify_time_content_id%');
        });    
    });

</script>
EOF;
        
        $javaScript = strtr($javaScript, array(
            '%duration_id%' => $this->generateId($name . '[duration]'),
            '%full_day_content_id%' => $fullDayContentId,
            '%half_day_content_id%' => $halfDayContentId,
            '%specify_time_content_id%' => $specifyTimeContentId
        ));
        
        return $html . $javaScript;
    }
    
    protected function getDefaultTimeRangeValues() {
        $default = $this->getWorkShiftService()->getWorkShiftDefaultStartAndEndTime();
        return array('from' => $default['start_time'], 'to' => $default['end_time']);
    }
    public function getJavaScripts() {
        
        $javaScripts = parent::getJavaScripts();
        $javaScripts[] = plugin_web_path('orangehrmLeavePlugin', 'js/ohrmWidgetFormLeaveDuration.js');

        return $javaScripts;
        
    }
    
    public function getStylesheets() {
        $css = parent::getStylesheets();
        $css[plugin_web_path('orangehrmLeavePlugin', 'css/ohrmWidgetFormLeaveDuration.css')] = 'all';
        
        return $css;
    }    
}
