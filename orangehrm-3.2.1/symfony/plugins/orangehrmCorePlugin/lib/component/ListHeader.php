<?php

class ListHeader extends ComponentProperty {
    const DEFAULT_ELEMENT_TYPE = 'label';

    protected $elementTypes = array(
        'label',
        'labelDate',
        'link',
        'textbox',
        'textarea',
        'checkbox',
        'radio',
        'selectSingle', // TODO: Make this values dash-separated
        'selectMultiple', // TODO: Make this values dash-separated
        'comment',
        'treeLink',
        'linkDate',
        'checkbox'
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
    protected $filters = array();
    
    protected $filterObjects;

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
            throw new ListHeaderException('Tried to assign an invalid sort order');
        }
    }

    public function getSortField() {
        return $this->sortField;
    }

    public function setSortField($sortField) {
        if (is_numeric($sortField)) {
            throw new ListHeaderException('Tried to assign a numeric value to sort field');
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
            throw new ListHeaderException('Tried to assign an unsupported element type');
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
            throw new ListHeaderException('Tried to assign an invalid width');
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
            throw new ListHeaderException('Tried to assign an unsupported text alignment style');
        }
    }

    public function setTextAlignmentStyleForHeader($textAlignmentStyleForHeader) {

        if (in_array($textAlignmentStyleForHeader, $this->textAlignmentStyles)) {
            $this->textAlignmentStyleForHeader = $textAlignmentStyleForHeader;
        } else {
            throw new ListHeaderException('Tried to assign an unsupported text alignment style');
        }
    }

    public function getTextAlignmentStyle() {

        return $this->textAlignmentStyle;
    }
    public function getTextAlignmentStyleForHeader() {

        return $this->textAlignmentStyleForHeader;
    }
    
    public function setFilters($filters) {
        
        if (is_array($filters)) {
            $this->filters = $filters;
        } else {
            $this->filters = array();
        }
        
        $this->createFilterObjects();
        
    }
    
    public function getFilters() {
        return $this->filters;
    }        
    
    protected function createFilterObjects() {
        
        $this->filterObjects = array();

        foreach($this->filters as $filterClass => $properties) {

            $filterObject = new $filterClass;
            if (is_array($properties)) {
                $filterObject->populateFromArray($properties);
            }
            $this->filterObjects[] = $filterObject;
        }
    }
    
    public function filterValue($value) {
        
        if (is_null($this->filterObjects)) {
            $this->createFilterObjects();  
        }

        foreach ($this->filterObjects as $filter) {

            if ($filter instanceof ohrmCellFilter) {
                $value = $filter->filter($value);
            }
        }
        
        return ($value);      
    }

}

