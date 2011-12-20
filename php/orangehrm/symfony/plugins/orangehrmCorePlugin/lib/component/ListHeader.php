<?php

class ListHeader extends ComponentProperty {
    const DEFAULT_ELEMENT_TYPE = 'label';

    protected $elementTypes = array(
        'label',
        'link',
        'textbox',
        'textarea',
        'checkbox',
        'radio',
        'selectSingle', // TODO: Make this values dash-separated
        'selectMultiple', // TODO: Make this values dash-separated
        'comment',
        'treeLink',
    );
    private $textAlignmentStyles = array(
        'left',
        'right',
        'center'
    );
    protected $name;
    protected $isSortable;
    protected $sortOrder;
    protected $sortField;
    protected $elementType;
    protected $elementProperty;
    protected $width;
    protected $isExportable = true;
    protected $textAlignmentStyle = "left";
    protected $textAlignmentStyleForHeader = "left";

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
        return (empty($this->elementType)) ? self::DEFAULT_ELEMENT_TYPE : $this->elementType;
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

    public function isExportable($isExportable = null) {
        if (is_null($isExportable)) {
            return (bool) $this->isExportable;
        } else {
            $this->isExportable = (bool) $isExportable;
        }
    }

    public function setTextAlignmentStyle($textAlignmentStyle) {

        if (in_array($textAlignmentStyle, $this->textAlignmentStyles)) {
            $this->textAlignmentStyle = $textAlignmentStyle;
        } else {
            throw new Exception('Tried to assign an unsupported text alignment style');
        }
    }

    public function setTextAlignmentStyleForHeader($textAlignmentStyleForHeader) {

        if (in_array($textAlignmentStyleForHeader, $this->textAlignmentStyles)) {
            $this->textAlignmentStyleForHeader = $textAlignmentStyleForHeader;
        } else {
            throw new Exception('Tried to assign an unsupported text alignment style');
        }
    }

    public function getTextAlignmentStyle() {

        return $this->textAlignmentStyle;
    }
    public function getTextAlignmentStyleForHeader() {

        return $this->textAlignmentStyleForHeader;
    }

}

