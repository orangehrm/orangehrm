<?php

class ohrmWidgetDatePickerNew extends sfWidgetFormInput {

    public function render($name, $value = null, $attributes = array(), $errors = array()) {

        if (array_key_exists('class', $attributes)) {
            $attributes['class'] .= ' ohrm_datepicker';
        } else {
            $attributes['class'] = 'ohrm_datepicker';
        }

        $html = parent::render($name, $value, $attributes, $errors);
        $html .= $this->renderTag('input', array(
                    'type' => 'button',
                    'id' => "{$this->attributes['id']}_Button",
                    'class' => 'calendarBtn',
                    'style' => 'float: none; display: inline; margin-left: 16px;',
                    'value' => '',
                ));

        $javaScript = sprintf(<<<EOF
 <script type="text/javascript">

    var dateFormat        = '%s' ;
    var jsDateFormat = '%s';
    var dateDisplayFormat = dateFormat.toUpperCase();

    $(document).ready(function(){

        var rDate = trim($("#%s").val());
        if (rDate == '') {
            $("%s").val(dateDisplayFormat);
        }

        //Bind date picker
        daymarker.bindElement("#%s",
        {
            onSelect: function(date){

            },
            dateFormat:jsDateFormat
        });

        $('#%s_Button').click(function(){
            daymarker.show("#%s");

        });
    });
</script>
EOF
                        ,
                        sfContext::getInstance()->getUser()->getDateFormat(),
                        get_js_date_format(sfContext::getInstance()->getUser()->getDateFormat()),
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id'],
                        $this->attributes['id']
        );

        return $html . $javaScript;
    }

}

