<?php

class ohrmReportWidgetServicePeriod extends sfWidgetForm implements ohrmEnhancedEmbeddableWidget {

    private $whereClauseCondition;
    private $id;
    private $conditionMap = array(1 => '<', 2 => '>', 3 => 'BETWEEN');

    public function configure($options = array(), $attributes = array()) {

        $this->id = $attributes['id'];

        $choices = array(
            '' => '-- ' . __('Select') . ' --',
            '1' => __('Less Than'),
            '2' => __('Greater Than'),
            '3' => __('Range')
        );

        $this->addOption($this->id . '_' . 'comparision', new sfWidgetFormChoice(array('choices' => $choices)));
        $this->addOption($this->id . '_' . 'value1', new sfWidgetFormInputText($options, array('size' => 10)));
        $this->addOption($this->id . '_' . 'value2', new sfWidgetFormInputText($options, array('size' => 10)));


        $this->addOption('template', '%comparision% &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp %value1% &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp %value2%');
    }

    public function render($name, $value = null, $attributes = array(), $errors = array()) {
        $values = array_merge(array('value1' => '', 'value2' => '', 'comparision' => ''), is_array($value) ? $value : array());

        $html = strtr($this->translate($this->getOption('template')), array(
                    '%comparision%' => $this->getOption($this->attributes['id'] . '_' . 'comparision')->render($name . '[comparision]', $values['comparision'], array('id' => $this->attributes['id'] . '_' . 'comparision')),
                    '%value1%' => $this->getOption($this->attributes['id'] . '_' . 'value1')->render($name . '[value1]', $values['value1'], array('id' => $this->attributes['id'] . '_' . 'value1')),
                    '%value2%' => $this->getOption($this->attributes['id'] . '_' . 'value2')->render($name . '[value2]', $values['value2'], array('id' => $this->attributes['id'] . '_' . 'value2')),
                ));

        $javaScript = $javaScript = sprintf(<<<EOF
 <script type="text/javascript">

$(document).ready(function() {

    if($('#%s' + '_comparision').val() == ''){
        $('#%s' + '_value1').hide().val('');
        $('#%s' + '_value2').hide().val('');
    }

    $('#%s' + '_comparision').change(function(){
        if($('#%s' + '_comparision').val() == ''){
            $('#%s' + '_value1').hide().val('');
            $('#%s' + '_value2').hide().val('');
        }else if($('#%s' + '_comparision').val() == '1'){
            getHints('#%s' + '_value1','%s');
            $('#%s' + '_value1').show();
            $('#%s' + '_value2').hide().val('');
        }else if($('#%s' + '_comparision').val() == '2'){
            getHints('#%s' + '_value1','%s');
            $('#%s' + '_value1').show();
            $('#%s' + '_value2').hide().val('');
        }else if($('#%s' + '_comparision').val() == '3'){
            getHints('#%s' + '_value1','%s');
            getHints('#%s' + '_value2','%s');
            $('#%s' + '_value1').show();
            $('#%s' + '_value2').show();
        }
    });
    
    $('#%s' + '_comparision').trigger('change');
 });

function getHints( id , message ){
    if ($(id).val() == ''){
        $(id).val(message)
        .addClass("inputFormatHint");
    }

    $(id).one('focus', function() {
        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });
}


 </script>
EOF
                        ,
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id'],
                        __('Type in years') . '..',
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id'],
                        __('Type in years') . '..',
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id'],
                        __('Type in years') . '..',
                        $this->attributes['id'],
                        __('Type in years') . '..',
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id']);

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
        $validatorSchema[$this->attributes['id']] =  new ohrmValidatorConditionalFilter(array(), array('required' => $requiredMessage));

//$form->setValidator('date_period', new sfValidatorString());
//        $validatorSchema[$this->attributes['id']] = new ohrmValidatorDateRange(array(), array("invalid" => "Insert a correct date"));
//        $validatorSchema[$this->attributes['id']] = new sfValidatorPass();
//        $validatorSchema->setPostValidator(new ohrmValidatorSchemaDateRange($this->attributes['id'], ohrmValidatorSchemaDateRange::LESS_THAN_EQUAL, $this->attributes['id'],
//                        array('throw_global_error' => true),
//                        array('invalid' => 'The from date ("%left_field%") must be before the to date ("%right_field%")')
//        ));
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
     * Gets whereClauseCondition. ( if whereClauseCondition is set returns that, else returns default condition )
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
        
        $values = array('value1' => $selectedFilterField->value1, 
                        'value2' => $selectedFilterField->value2,
                        'comparision' => $condition);

        return $values;
    }    
}

