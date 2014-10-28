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
class ohrmReportWidgetJoinedDate extends sfWidgetForm implements ohrmEnhancedEmbeddableWidget {

    private $whereClauseCondition;
    private $id;
    private $conditionMap = array(1 => '>', 2 => '<', 3 => 'BETWEEN');

    public function configure($options = array(), $attributes = array()) {

        $this->id = $attributes['id'];

        $choices = array(
            '' => '-- ' . __('Select') . ' --',
            '1' => __('Joined After'),
            '2' => __('Joined Before'),
            '3' => __('Joined in Between')
        );

        $this->addOption($this->id . '_' . 'comparision', new sfWidgetFormChoice(array('choices' => $choices)));
        $this->addOption($this->id . '_' . 'from', new ohrmWidgetDatePicker(array(), array('id' => $this->id . '_' . 'from')));
        $this->addOption($this->id . '_' . 'to', new ohrmWidgetDatePicker(array(), array('id' => $this->id . '_' . 'to')));

        $this->addOption('template', '%comparision% %from% %to%');
        
    }

    public function render($name, $value = null, $attributes = array(), $errors = array()) {
        $values = array_merge(array('from' => '', 'to' => '', 'comparision' => ''), is_array($value) ? $value : array());

        $html = strtr($this->translate($this->getOption('template')), array(
                    '%comparision%' => $this->getOption($this->attributes['id'] . '_' . 'comparision')->render($name . '[comparision]', $values['comparision'], array('id' => $this->attributes['id'] . '_' . 'comparision')),
                    '%from%' => $this->getOption($this->attributes['id'] . '_' . 'from')->render($name . '[from]', $values['from'], array('id' => $this->attributes['id'] . '_' . 'from')),
                    '%to%' => $this->getOption($this->attributes['id'] . '_' . 'to')->render($name . '[to]', $values['to'], array('id' => $this->attributes['id'] . '_' . 'to')),
                ));

        $javaScript = $javaScript = sprintf(<<<EOF
 <script type="text/javascript">

$(document).ready(function() {

    var idValue = '%s';
    var joinedDateFormat = '%s';
    var displayDateFormat = joinedDateFormat.replace('yy', 'yyyy');

    if($('#' + idValue + '_comparision').val() == ''){
        $('#' + idValue + '_from').hide().val('');
        $('#' + idValue + '_from').next('img.ui-datepicker-trigger').hide();
        $('#' + idValue + '_to').hide().val('');
        $('#' + idValue + '_to').next('img.ui-datepicker-trigger').hide();
    }

    $('#' + idValue + '_comparision').change(function(){
        if($('#' + idValue + '_comparision').val() == ''){

            $('#' + idValue + '_from').hide().val('');
            $('#' + idValue + '_from').next('img.ui-datepicker-trigger').hide();
            $('#' + idValue + '_to').hide().val('');
            $('#' + idValue + '_to').next('img.ui-datepicker-trigger').hide();            
        }else if($('#' + idValue + '_comparision').val() == '1'){
            $('#' + idValue + '_from').show();
            $('#' + idValue + '_from').next('img.ui-datepicker-trigger').show();
            $('#' + idValue + '_to').hide().val('');
            $('#' + idValue + '_to').next('img.ui-datepicker-trigger').hide();
        }else if($('#' + idValue + '_comparision').val() == '2'){
            $('#' + idValue + '_from').show();
            $('#' + idValue + '_from').next('img.ui-datepicker-trigger').show();
            $('#' + idValue + '_to').hide().val('');
            $('#' + idValue + '_to').next('img.ui-datepicker-trigger').hide();
        }else if($('#' + idValue + '_comparision').val() == '3'){
            $('#' + idValue + '_from').show();
            $('#' + idValue + '_from').next('img.ui-datepicker-trigger').show();
            $('#' + idValue + '_to').show();
            $('#' + idValue + '_to').next('img.ui-datepicker-trigger').show();
        }
        
        if ($('#' + idValue + '_from').is(":visible")) {
            if ($('#' + idValue + '_from').val() == '') {
                $('#' + idValue + '_from').val(displayDateFormat);
            }
        }
        if ($('#' + idValue + '_to').is(":visible")) {
            if ($('#' + idValue + '_to').val() == '') {
                $('#' + idValue + '_to').val(displayDateFormat);
            }
        }
        
    });
    
    $('#' + idValue + '_comparision').trigger('change');
 });
 </script>
EOF
                        ,
                        $this->attributes['id'],
                        get_datepicker_date_format(sfContext::getInstance()->getUser()->getDateFormat()));

        return $html . $javaScript;
    }

    /**
     * Embeds this widget into the form. Sets label and validator for this widget.
     * @param sfForm $form
     */
    public function embedWidgetIntoForm(sfForm &$form) {


        $widgetSchema = $form->getWidgetSchema();
        $validatorSchema = $form->getValidatorSchema();

        $widgetSchema[$this->attributes['id']] = $this;
        $widgetSchema[$this->attributes['id']]->setLabel(ucwords(str_replace("_", " ", $this->attributes['id'])));
        $requiredMessage = __(ValidationMessages::REQUIRED);               
        
        $validatorSchema[$this->attributes['id']] = new ohrmValidatorDateConditionalFilter(array(), 
                array('required' => $requiredMessage));        
    }

    /**
     * Sets whereClauseCondition.
     * @param string $condition
     */
    public function setWhereClauseCondition($condition) {
        if (isset($this->conditionMap[$condition])) {
            $this->whereClauseCondition = $this->conditionMap[$condition];
        }
    }

    /**
     * Gets whereClauseCondition. ( if whereClauseCondition is set returns that, elseF returns default condition )
     * @return string ( a condition )
     */
    public function getWhereClauseCondition() {

        if (isset($this->whereClauseCondition)) {
            $setCondition = $this->whereClauseCondition;
            return $setCondition;
        } else {
            $defaultCondition = "=";
            return $defaultCondition;
        }
    }

    /**
     * This method generates the where clause part.
     * @param string $fieldName
     * @param string $value
     * @return string
     */
    public function generateWhereClausePart($fieldName, $value) {

       $condition = $this->getWhereClauseCondition();

        if($condition == '<'){
            return $whereClausePart = $fieldName . " " . $this->getWhereClauseCondition() . " " . $value['value1'];
        }else if($condition == '>'){
            return $whereClausePart = $fieldName . " " . $this->getWhereClauseCondition() . " " . $value['value1'];
        }else if($condition == 'BETWEEN'){
            return "( " . $fieldName . " " . $this->getWhereClauseCondition() . " '" . $value['value1'] . "' AND '" . $value['value2'] . "' )";
        }

        return null;
    }
    
    public function getDefaultValue(SelectedFilterField $selectedFilterField) {
        
        $condition = '';
        
        if (!empty($selectedFilterField->whereCondition)) {
            $condition = array_search($selectedFilterField->whereCondition, $this->conditionMap);            
        }
        
        $values = array('from' => set_datepicker_date_format($selectedFilterField->value1), 
                        'to' => set_datepicker_date_format($selectedFilterField->value2),
                        'comparision' => $condition);

        return $values;
    }      
}

