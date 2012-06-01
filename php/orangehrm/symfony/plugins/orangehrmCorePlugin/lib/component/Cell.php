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

        $this->dataObject = ($dataObject instanceof sfOutputEscaperArrayDecorator) ? $dataObject->getRawValue() : $dataObject;
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

}
