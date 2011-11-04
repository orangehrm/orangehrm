<?php

class ohrmForm implements ohrmFormInterface {

    private $object;
    private $propertyObject;

    public function getHtml() {
        $html = '';
        $fields = $this->propertyObject->getFields();
        $readOnlyFields = $this->propertyObject->getReadOnlyFields();
        $disabledFields = $this->propertyObject->getDisabledFields();
        $fieldTypes = $this->propertyObject->getFieldTypes();

        if (isset($this->object) && !empty($this->object)) {
            foreach ($fields as $label => $accessMethod) {
                if (is_array($accessMethod)) {
                    $value = $this->object;
                    foreach ($accessMethod as $key => $method) {
                        if ($key === 'hidden-field-value-accessor') {
                            continue;
                        }
                        $value = $value->$method();
                    }
                } else {
                    $includeExternalHtml = preg_match('/^\[html/', $label);
                    $value = (empty($accessMethod) || $includeExternalHtml) ? '' : $this->object->$accessMethod();
                }
                $labelAttributes = array();
                
                if ($label === $this->propertyObject->getIdField()) {
                    $html .= $this->_getIdFieldHtml($label, $value);
                } elseif ($includeExternalHtml) {
                    $divId = "divHtml_{$accessMethod}";
                    $html .= content_tag('div', $divId, array('id' => $divId));
                } else {
                    $readOnly = in_array($label, $readOnlyFields);
                    $disabled = in_array($label, $disabledFields);
                    $html .= $this->_getRegularFieldHtml($label, $value, $readOnly, $disabled);
                }
            }
        }

        $html = content_tag('form', $html, array(
                    'method' => 'post',
                    'id' => $this->propertyObject->getFormId(),
                ));
      
        return $html;
    }

    public function setObject($object) {
        $this->object = $object;
    }

    public function setPropertyObject(ohrmFormComponentProperty $propertyObj) {
        $this->propertyObject = $propertyObj;
    }

    /**
     *
     * @param string $label
     * @param string $value
     * @param bool $visible
     * @return string
     */
    private function _getIdFieldHtml($label, $value, $visible = true) {

        $controlId = $this->_translateToAttributeValue("hdn{$label}");

        $html = tag('input', array(
                    'type' => 'hidden',
                    'value' => $value,
                    'id' => $controlId,
                    'name' => $controlId,
                ));

        if ($visible) {
            if ($this->propertyObject->getFieldTypeBy($label) !== 'hidden') {
                $html .= content_tag('label', $label);
                $html .= content_tag('label', $value, array('class' => 'idValueLabel'));
                $html .= tag('br', array('class' => 'clear')); // TODO: Remove this class attribute
            }
        }

        return $html;
    }

    /**
     *
     * @param string $label
     * @param string $value
     * @param boolean $readOnly
     * @param boolean $disabled
     * @return string
     */
    private function _getRegularFieldHtml($label, $value, $readOnly = false, $disabled = false) {
        $fieldType = $this->propertyObject->getFieldTypeBy($label);

        $tag = 'input';
        $type = 'text';
        $class = 'formInputText'; // TODO: Refactor this to load values from a YAML file

        switch ($fieldType) {
            case 'textarea':
                $controlPrefix = 'txt';
                $tag = 'textarea';
                $class = 'formTextArea';
                $content = $value;
                break;
            case 'password':
                $controlPrefix = 'txt';
                $type = 'password';
                $class = 'formInputText';
                $content = '';
                break;
            case 'select-single':
                $controlPrefix = 'cmb';
                $tag = 'select';
                $class = 'formSelect';
                $content = $this->_getSelectOptionsHtml($this->propertyObject->getSelectOptionsBy($label), $value);
                break;
            case 'select-multiple':
                $controlPrefix = 'cmb';
                $tag = 'select';
                $class = 'formSelect';
                $content = $this->_getSelectOptionsHtml($this->propertyObject->getSelectOptionsBy($label));
                break;
            case 'checkbox':
                $controlPrefix = 'chk';
                $type = 'checkbox';
                $class = 'formCheckbox';
                break;
            case 'checkbox-list':
                $controlPrefix = 'chk';
                $tag = 'div';
                $class = '';
                $content = $this->_getCheckboxListHtml($this->propertyObject->getSelectOptionsBy($label), $this->_translateToAttributeValue("{$controlPrefix}{$label}"), $value);
                break;
            case 'radio':
                $controlPrefix = 'rad';
                $tag = 'div';
                $type = 'radio';
                $class = 'formRadio';
                $content = $this->_getRadioButtonListHtml($this->propertyObject->getSelectOptionsBy($label), $this->_translateToAttributeValue("{$controlPrefix}{$label}"), $value);
                break;
            case 'date-picker':
                $controlPrefix = 'dtp';
                $class = 'formDateInput';
                $readOnly = true;
                break;
            case 'file-upload':
                $controlPrefix = 'file';
                $type = 'file';
                $class = 'formFileInput';
                break;
            case 'hidden':
                $controlPrefix = 'hdn';
                $type = 'hidden';
                $class = '';
                break;
            case 'ohrm-employee-list-selection':
                $controlPrefix = 'txt';
                $type = 'text';
                $readOnly = true;
                break;
            case 'textbox':
            case ohrmFormComponentProperty::DEFAULT_FIELD_TYPE:
            default:
                $controlPrefix = 'txt';
        }

        $controlId = $this->_translateToAttributeValue("{$controlPrefix}{$label}");

        $requiredFields = $this->propertyObject->getRequiredFields();

        if (in_array($label, $requiredFields)) {
            $label = __($label);
            $label .= '&nbsp;' . content_tag('span', '*', array('class' => 'required'));
        }else{
            $label = __($label);
        }

        $tagAttributes = array(
            'name' => $controlId,
            'id' => $controlId,
            'class' => $class,
        );

        if ($tag === 'input') {
            $tagAttributes['type'] = $type;
            $tagAttributes['value'] = $value;
        }

        if ($disabled) {
            $tagAttributes['disabled'] = 'disabled';
        }

        if ($readOnly) {
            $tagAttributes['readonly'] = 'readonly';
        }

        if ($tag === 'div') {
            $tagAttributes = array();
        }

        $html = ($fieldType !== 'hidden') ? content_tag('label', $label, array(
                    'for' => $controlId
                )) : '';

        if ($tag === 'input') {
            $html .= tag($tag, $tagAttributes);
        } else {
            $html .= content_tag($tag, $content, $tagAttributes);
        }

        if ($fieldType === 'date-picker') {
            $buttonId = $this->_translateToAttributeValue("btn{$label}");
            $html .= tag('input', array(
                        'type' => 'button',
                        'id' => $buttonId,
                        'name' => $buttonId,
                        'class' => 'calendarBtn',
                        'value' => '  ',
                    ));
        }

        if ($fieldType === 'ohrm-employee-list-selection') {
            $fields = $this->propertyObject->getFields();

            $buttonId = $this->_translateToAttributeValue("pub{$label}");
            $hiddenFieldId = $this->_translateToAttributeValue("els{$label}");
            if (is_array($fields[$label]) && array_key_exists('hidden-field-value-accessor', $fields[$label])) {
                $method = $fields[$label]['hidden-field-value-accessor'];
                $hiddenFieldValue = $this->object->$method();
            } else {
                $hiddenFieldValue = '';
            }

            $html .= tag('input', array(
                        'type' => 'button',
                        'id' => $buttonId,
                        'name' => $buttonId,
                        'class' => 'popupButton',
                        'value' => '...',
                    ));

            $html .= tag('input', array(
                        'type' => 'hidden',
                        'id' => $hiddenFieldId,
                        'name' => $hiddenFieldId,
                        'value' => $hiddenFieldValue,
                    ));
        }

        $html .= tag('br', array('class' => 'clear')); // TODO: Remove this class attribute

        return $html;
    }

    private function _translateToAttributeValue($value) {
        return str_replace(array(' ', '.', "'"), '_', $value);
    }

    private function _getSelectOptionsHtml(array $options, $selectedValue = null) {
        $html = '';
        $attributes = array('selected-value' => $selectedValue);
        foreach ($options as $value => $label) {
            $attributes['value'] = $value;
            if ($selectedValue == $value) {
                $attributes['selected'] = 'selected';
            } else {
                if (isset($attributes['selected'])) {
                    unset($attributes['selected']);
                }
            }
            $html .= content_tag('option', $label, $attributes);
        }
        return $html;
    }

    private function _getCheckboxListHtml(array $options, $controlId, $checkedValues = array()) {
        $html = '';
        $attributes = array('name' => "{$controlId}[]", 'type' => 'checkbox', 'class' => 'formCheckbox');

        $i = 0;
        foreach ($options as $value => $label) {
            $fieldId = $controlId . '_' . $value;

            $attributes['id'] = $fieldId;
            $attributes['value'] = $value;

            if (in_array($value, $checkedValues)) {
                $attributes['checked'] = 'checked';
            } else {
                if (isset($attributes['checked'])) {
                    unset($attributes['checked']);
                }
            }

            $attributes['style'] = 'margin-left: ' . (($i++ == 0) ? '10' : '140') . 'px';

            $html .= tag('input', $attributes);
            $html .= content_tag('label', $label, array('for' => $fieldId));
            $html .= tag('br', array('class' => 'clear'));
        }
        return $html;
    }

    private function _getRadioButtonListHtml(array $options, $controlId, $checkedValue) {
        $html = '';
        $attributes = array('name' => $controlId, 'type' => 'radio', 'class' => 'formRadio');

        $i = 0;
        foreach ($options as $value => $label) {
            $fieldId = $controlId . '_' . $value;

            $attributes['id'] = $fieldId;
            $attributes['value'] = $value;

            if ($value == $checkedValue) {
                $attributes['checked'] = 'checked';
            } else {
                if (isset($attributes['checked'])) {
                    unset($attributes['checked']);
                }
            }

            $attributes['style'] = 'margin-left: 10px;';

            $html .= tag('input', $attributes);
            $html .= content_tag('label', $label, array('for' => $fieldId, 'style' => 'width: 100px; padding-left: 4px;'));
        }
        return $html;
    }

}
