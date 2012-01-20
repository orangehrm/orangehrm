<?php

class ohrmFormComponentProperty {
    const DEFAULT_FIELD_TYPE = 'textbox';

    private $service;
    private $method;
    private $parameters;
    private $object;
    private $fields;
    private $title;
    private $formId = 'ohrmFormComponent_Form';
    private $formStyle = null;
    private $idField;
    private $fieldTypes = array();
    private $backButtonUrl = '.';
    private $requiredFields = array();
    private $readOnlyFields = array();
    private $disabledFields = array();
    private $selectOptions = array();

    public function getService() {
        return $this->service;
    }

    public function setService(BaseService $service) {
        $this->service = $service;
    }

    public function getMethod() {
        return $this->method;
    }

    public function setMethod($method) {
        if (!empty($method) && is_string($method)) {
            if (preg_match('/^[A-Za-z]+(\w)/', $method) && !preg_match('/\s|\(|\)/', $method)) {
                $this->method = $method;
            } else {
                throw new ohrmFormComponentException();
            }
        } else {
            throw new ohrmFormComponentException();
        }
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function setParameters(array $params) {
        $this->parameters = $params;
    }

    public function getObject() {
        return $this->object;
    }

    public function setObject($object) {
        if (!empty($object) && is_object($object)) {
            $this->object = $object;
        } else {
            throw new ohrmFormComponentException();
        }
    }

    public function getFields() {
        return $this->fields;
    }

    public function setFields(array $fields) {
        if (!empty($fields)) {
            $this->fields = $fields;
        } else {
            throw new ohrmFormComponentException();
        }
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getFormId() {
        return $this->formId;
    }

    public function setFormId($id) {
        $this->formId = $id;
    }

    public function getFormStyle() {
        return $this->formStyle;
    }

    public function setFormStyle($style) {
        $this->formStyle = $style;
    }

    public function getIdField() {
        return $this->idField;
    }

    public function setIdField($field) {
        if (array_key_exists($field, $this->fields)) {
            $this->idField = $field;
        } else {
            throw new ohrmFormComponentException();
        }
    }

    public function getFieldTypes() {
        return $this->fieldTypes;
    }

    public function setFieldTypes(array $types) {
        $this->fieldTypes = $types;
    }

    public function getFieldTypeBy($fieldName) {

        if (array_key_exists($fieldName, $this->fieldTypes)) {
            return $this->fieldTypes[$fieldName];
        } else {
            if (array_key_exists($fieldName, $this->fields)) {
                return self::DEFAULT_FIELD_TYPE;
            } else {
                throw new ohrmFormComponentException();
            }
        }
    }

    public function getBackButtonUrl() {
        return $this->backButtonUrl;
    }

    public function setBackButtonUrl($backButtonUrl) {
        $this->backButtonUrl = $backButtonUrl;
    }

    public function getRequiredFields() {
        return $this->requiredFields;
    }

    public function setRequiredFields(array $fields) {
        $this->requiredFields = $fields;
    }

    public function getReadOnlyFields() {
        return $this->readOnlyFields;
    }

    public function setReadOnlyFields(array $fields) {
        $this->readOnlyFields = $fields;
    }

    public function getDisabledFields() {
        return $this->disabledFields;
    }

    public function setDisabledFields(array $fields) {
        $this->disabledFields = $fields;
    }

    public function getSelectOptions() {
        return $this->selectOptions;
    }

    public function setSelectOptions(array $options) {
        $this->selectOptions = $options;
    }

    public function getSelectOptionsBy($label) {

        if (array_key_exists($label, $this->selectOptions)) {
            return $this->selectOptions[$label];
        } else {
            if (array_key_exists($label, $this->fields)) {
                return array();
            } else {
                throw new ohrmFormComponentException();
            }
        }
    }

}

