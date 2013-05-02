<?php

class LabelCell extends Cell {

    public function __toString() {
        if ($this->isHiddenOnCallback()) {
            return '&nbsp;';
        }

        $value = $this->getValue();
        $default = $this->getPropertyValue('default');
        
        $isValueList = $this->getPropertyValue('isValueList', false);

        if ($isValueList && (is_array($value) || $value instanceof sfOutputEscaperArrayDecorator)) {
            
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

}
