<?php

class LabelDateCell extends Cell {

    public function __toString() {
        if ($this->isHiddenOnCallback()) {
            return '&nbsp;';
        }

        $value = $this->getValue();
        $default = $this->getPropertyValue('default');
        $spliter = $this->getPropertyValue('dateSplit');

        $isValueList = $this->getPropertyValue('isValueList', false);

        if ($isValueList && (is_array($value) || $value instanceof sfOutputEscaperArrayDecorator)) {

            $lines = $value;
            if (count($lines) >= 1) {
                $value = '<table class="valueListCell"><tbody>';
                foreach ($lines as $line) {
                    if (!$line && $default) {
                        $value .= '<tr><td>' . $default . '</td></tr>';
                    } else {
                        $value .= '<tr><td> &bull; ' . set_datepicker_date_format($line) . '</td></tr>';
                    }
                }
                $value .= '</tbody></table>';
            }
        } else {
            if ($spliter) {
                $formatted = array();
                $dates = split($spliter, $value);
                foreach ($dates as $date) {
                    $formatted[] = set_datepicker_date_format($date);
                }
                $value = implode($spliter, $formatted);
            } else {
                if ($value != $default) {
                    $value = set_datepicker_date_format($value);
                }
            }
        }

        return $value . $this->getHiddenFieldHTML();
    }

}
