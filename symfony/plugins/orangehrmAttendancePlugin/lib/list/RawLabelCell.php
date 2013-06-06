<?php

class RawLabelCell extends Cell {

    public function __toString() {
        if ($this->isHiddenOnCallback()) {
            return '&nbsp;';
        }

        $value = $this->getValue();
        $default = $this->getPropertyValue('default');
        
        $isValueList = $this->getPropertyValue('isValueList', false);

        if ($isValueList && is_array($value)) {
            
            $lines = $value;
            if (count($lines) >= 1) {
                $value = '<table class="valueListCell"><tbody>';
                foreach ($lines as $line) {
                    if (!$line && $default) {
                        $value .= '<tr><td>' . $default . '</td></tr>';
                    } else {
                        $value .= '<tr><td> &bull; ' . $line . '</td></tr>';
                    }
                }
                $value .= '</tbody></table>';
            }
        }

        return $value . $this->getHiddenFieldHTML();
    }
    
    protected function getValue($getterName = 'getter') {
        $getter = $this->getPropertyValue($getterName);
        $default = $this->getPropertyValue('default');
        if ($getter instanceof sfOutputEscaperArrayDecorator || is_array($getter)) {
            $value = $this->dataObject;
            foreach ($getter as $method) {
                if (is_object($value)) {
                    $value = $value->$method(ESC_RAW);
                }
            }
        } else {
            $value = ($this->dataSourceType === self::DATASOURCE_TYPE_ARRAY) ? $this->dataObject[$getter] : $this->dataObject->$getter(ESC_RAW);
        }

        if (!$value && $default) {
            return $default;
        }

        $value = $this->filterValue($value);

        return $value;
    }

}