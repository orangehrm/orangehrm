<?php

abstract class Cell implements PopulatableFromArray {

    const DATASOURCE_TYPE_OBJECT = 1;
    const DATASOURCE_TYPE_ARRAY = 2;

    protected $properties;
    protected $dataObject;
    protected $header;
    private $dataSourceType = self::DATASOURCE_TYPE_OBJECT;

    public function populateFromArray(array $properties) {
        PropertyPopulator::populateFromArray($this, $properties);
    }

    public function getProperties() {
        return $this->properties;
    }

    public function setProperties($properties) {
        $this->properties = $properties;
    }

    public function getPropertyValue($name, $default = null) {
        return isset($this->properties[$name]) ? $this->properties[$name] : $default;
    }

    public function hasProperty($name) {
        return isset($this->properties[$name]);
    }

    public function getDataObject() {
        return $this->dataObject;
    }

    public function setDataObject($dataObject) {
        if (is_array($dataObject) || $dataObject instanceof sfOutputEscaperArrayDecorator) {
            $this->dataSourceType = self::DATASOURCE_TYPE_ARRAY;
        }

        $this->dataObject = $dataObject;
    }

    public function setHeader($header) {

        if ($header instanceof sfOutputEscaperObjectDecorator) {
            $header = $header->getRawValue();
        }

        $this->header = $header;
    }

    public function getHeader() {
        return $this->header;
    }

    public function toValue() {
        return $this->getValue();
    }

    protected function getValue($getterName = 'getter') {
        $getter = $this->getPropertyValue($getterName);
        $default = $this->getPropertyValue('default');
        if ($getter instanceof sfOutputEscaperArrayDecorator || is_array($getter)) {
            $value = $this->dataObject;
            foreach ($getter as $method) {
                if (is_object($value)) {
                    $value = $value->$method();
                }
            }
        } else {
            $value = ($this->dataSourceType === self::DATASOURCE_TYPE_ARRAY) ? $this->dataObject[$getter] : $this->dataObject->$getter();
        }

        if (!$value && $default) {
            return $default;
        }

        $value = $this->filterValue($value);

        return $value;
    }

    /**
     * Filters given value using all filters set in the header.
     * 
     * @param String $value
     * @return String Filtered value
     */
    protected function filterValue($value) {

        if (isset($this->header)) {
            $value = $this->header->filterValue($value);
        }

        return $value;
    }

    protected function getHiddenFieldHTML() {
        $placeholderGetters = $this->getPropertyValue('placeholderGetters', array());
        $hiddenFieldHtml = '';

        if ($this->getPropertyValue('hasHiddenField', false)) {
            $hiddenFieldHtml = tag('input', array(
                'type' => 'hidden',
                'name' => $this->generateAttributeValue($placeholderGetters, $this->getPropertyValue('hiddenFieldName')),
                'id' => $this->generateAttributeValue($placeholderGetters, $this->getPropertyValue('hiddenFieldId')),
                'class' => $this->generateAttributeValue($placeholderGetters, $this->getPropertyValue('hiddenFieldClass')),
                'value' => $this->getValue('hiddenFieldValueGetter'),
                    ));
        }

        return $hiddenFieldHtml;
    }

    protected function generateAttributeValue($placeholderGetters, $attributeValue) {
        if (empty($placeholderGetters)) {
            return $attributeValue;
        } else {
            $patterns = array();
            $replacements = array();

            foreach ($placeholderGetters as $placeholderKey => $getterMethod) {
                $patterns[] = '/\{' . $placeholderKey . '\}/';
                $replacements[] = ($this->dataSourceType === self::DATASOURCE_TYPE_ARRAY) ? $this->dataObject[$getterMethod] : $this->dataObject->$getterMethod();
            }

            return preg_replace($patterns, $replacements, $attributeValue);
        }
    }

    /**
     *
     * @return bool
     */
    protected function isHiddenOnCallback() {
        $hideIfCallback = $this->getPropertyValue('hideIfCallback', null);
        if (!empty($hideIfCallback) && is_callable(array($this->dataObject, $hideIfCallback))) {
            return $this->dataObject->$hideIfCallback();
        }

        return false;
    }
    
    public function getDataSourceType() {
        return $this->dataSourceType;
    }
    
    /**
     * Gets the value of the given element property.
     * 
     * Supports placeholders and label getters.
     * Ex: If property name is 'label'
     * First looks for property named 'label'
     * If not found, looks for property 'labelGetter' and uses that to get the value
     * 
     * Once value is found, looks for property 'labelPlaceholders'.
     * If found, users that to replace any placeholders in the property.
     * 
     * Ex:
     * <pre>
     * 'elementProperty' => array(
     *     'id' => 'ohrmList_chkSelectRecord_{id}',
     *     'name' => 'chkSelectRow[]',
     *     'valueGetter' => 'getId',
     *     'label' => 'Enable',
     *     'placeholderGetters' => array('id' => 'getId'),
     * )
     * </pre>
     * 
     * @param string $name Property name
     * @param mixed $default Default value to return if property is not available
     * @return mixed property value with any placeholders replaced
     */
    public function getParsedPropertyValue($name, $default = null) {
        
        // Get value
        $value = $this->getPropertyValue($name);
        if (is_null($value)) {
            $getter = $this->getPropertyValue($name . 'Getter');
            if (!is_null($getter)) {
                $value = $this->getDataObjectProperty($getter);
            }
        }
        
        // Replace any placeholders
        if (!empty($value)) {
            if (strpos($value, '{') !== false) {

                $placeHolderGetters = $this->getPropertyValue('placeholderGetters');
                
                if ($placeHolderGetters instanceof sfOutputEscaperArrayDecorator || is_array($placeHolderGetters)) {                    
                    $patterns = array();
                    $replacements = array();        

                    foreach ($placeHolderGetters as $placeholder => $getter) {
                        $placeholderValue = $this->getDataObjectProperty($getter);
                        $patterns[] = "/\{{$placeholder}\}/";
                        $replacements[] = $placeholderValue;                        
                    }
                    
                    $value = preg_replace($patterns, $replacements, $value);                    
                }
            } 
        }
        
        return is_null($value) ? $default : $value;
    }    
    
    /**
     * Get property value from data object.
     * Checks for key if array,
     * Checks for method or property if object.
     * 
     * @param string $property Propery Name
     * @return Property value Property value, or null if not available.
     */
    public function getDataObjectProperty($property) {
        $value = null;
        if ($this->getDataSourceType() == self::DATASOURCE_TYPE_ARRAY) {

            if (isset($this->dataObject[$property])) {
                $value =  $this->dataObject[$property];                    
            }
        } else {
            
            // Data source is an object (usually a sfOutputEscaperObjectDecorator
            // wrapping a Doctrine_Record object 
            if (isset($this->dataObject->$property)) {
                
                $value = $this->dataObject->$property;
            } else if (is_callable(array($this->dataObject, $property))) {
                $value = $this->dataObject->$property();
            }
        }   

        return $value;
    }

}
