<?php

class ListHeader extends ComponentProperty {

    const DEFAULT_ELEMENT_TYPE = 'label';

    private $elementTypes = array(
        'label',
        'link',
        'textbox',
        'textarea',
        'checkbox',
        'radio',
        'select-single',
        'select-multiple',
    );


    protected $name;
    protected $isSortable;
    protected $sortOrder;
    protected $sortField;
    protected $elementType;
    protected $elementProperty;
    protected $width;
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function isSortable($isSortable = null) {
        if (is_null($isSortable)) {
            return (bool) $this->isSortable;
        } else {
            $this->isSortable = (bool) $isSortable;
        }
    }

    public function getSortOrder() {
        return $this->sortOrder;
    }

    public function setSortOrder($sortOrder) {
        if (preg_match('/(A|DE)SC/', $sortOrder)) {
            $this->sortOrder = $sortOrder;
        } else {
            throw new Exception('Tried to assign an invalid sort order');
        }
    }

    public function getSortField() {
        return $this->sortField;
    }
    
    public function setSortField($sortField) {
        if (is_numeric($sortField)) {
            throw new Exception('Tried to assign a numeric value to sort field');
        }
        $this->sortField = $sortField;
    }

    public function getElementType() {
        return (empty ($this->elementType)) ? self::DEFAULT_ELEMENT_TYPE : $this->elementType;
    }

    public function setElementType($elementType) {
        if (in_array($elementType, $this->elementTypes)) {
            $this->elementType = $elementType;
        } else {
            throw new Exception('Tried to assign an unsupported element type');
        }
    }

    public function getElementProperty() {
        return $this->elementProperty;
    }

    public function setElementProperty($elementProperty) {
        $this->elementProperty = $elementProperty;
    }

    public function getWidth() {
        return $this->width;
    }

    public function setWidth($width) {
        if (preg_match('/\d{1,}(%{0,1})$/', $width)) {
            $this->width = $width;
        } else {
            throw new Exception('Tried to assign an invalid width');
        }
    }

    public function getElementTypes() {
        return $this->elementTypes;
    }
}

