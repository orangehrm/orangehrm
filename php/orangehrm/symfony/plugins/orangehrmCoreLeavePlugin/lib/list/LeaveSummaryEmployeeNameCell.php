<?php
/**
 * Description of LeaveSummaryEmployeeNameCell
 *
 */

class LeaveSummaryEmployeeNameCell extends Cell {

    public function __toString() {
        $linkable = $this->getPropertyValue('linkable', true);

        if (($linkable instanceof sfOutputEscaperArrayDecorator) || is_array($linkable)) {
            list($method, $params) = $linkable;
            $linkable = call_user_func_array(array($this->dataObject, $method), $params->getRawValue());
            $employeeId = $this->dataObject->getEmployeeId();
        } else {
            $linkable = $this->getValue('linkable');
            $employeeId = $this->getValue('hiddenFieldValueGetter');
        }

         if ($linkable) {
            $placeholderGetters = $this->getPropertyValue('placeholderGetters');
            $urlPattern = $this->getPropertyValue('urlPattern');
            if ($employeeId == $this->getValue('loggedUserId')) {
                $urlPattern = $this->getPropertyValue('altUrlPattern');
            }

            $url = $urlPattern;
            foreach ($placeholderGetters as $placeholder => $getter) {
                $placeholderValue = is_array($this->dataObject) ? $this->dataObject[$getter] : $this->dataObject->$getter();
                $url = preg_replace("/\{{$placeholder}\}/", $placeholderValue, $url);
            }

            $linkAttributes = array(
                'href' => $url,
            );
            $employeeName = $this->getValue('labelGetter');
            if($this->getValue('terminatedEmployee')) {
                $employeeName .= ' ('. __('Past Employee').')';
            }
            return content_tag('a', $employeeName, $linkAttributes)
                    . $this->getHiddenFieldHTML();
        } else {
            return $this->toValue();
        }
    }

    public function toValue() {
        return $this->getValue('labelGetter');
    }
}
