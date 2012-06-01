<?php
class LeaveSummaryValueCell extends Cell {

    public function __toString() {
         
        $leaveInfo = $this->getValue();
        $elementKey = $this->getPropertyValue('elementKey');
        $leaveInfoArray = explode("_", $leaveInfo);
        $leaveTaken = count($leaveInfoArray) > $elementKey ? $leaveInfoArray[$elementKey] : '0.00';
        
        
        $linkable = $this->getPropertyValue('linkable', false);
        if ($linkable) {
            if (($linkable instanceof sfOutputEscaperArrayDecorator) || is_array($linkable)) {
                list($method, $params) = $linkable;
                $linkable = call_user_func_array(array($this->dataObject, $method), $params->getRawValue());
                $employeeId = $this->dataObject->getEmployeeId();
            } else {
                $linkable = $this->getValue('linkable');
                $employeeId = $this->getValue('placeholderGetters');
                $employeeId = $employeeId['emp_number'];
            }
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
            
            return content_tag('a', $leaveTaken, $linkAttributes)
            . $this->getHiddenFieldHTML();
        } else {
        	return $leaveTaken;
        }
    }

}