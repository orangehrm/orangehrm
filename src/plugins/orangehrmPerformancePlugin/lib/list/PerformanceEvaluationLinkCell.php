<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of leaveSummaryLinkCell
 *
 * @author nadeeth
 */

class PerformanceEvaluationLinkCell extends Cell {

    public function __toString() {
       $linkable = $this->getPropertyValue('linkable', true);
        
        if (($linkable instanceof sfOutputEscaperArrayDecorator) || is_array($linkable)) {
            list($method, $params) = $linkable;
            $linkable = call_user_func_array(array($this->dataObject, $method), $params->getRawValue());
        }
        
        if ($linkable) {
            $placeholderGetters = $this->getPropertyValue('placeholderGetters');
            $urlPattern = $this->getPropertyValue('urlPattern');

            $url = $urlPattern;
            foreach ($placeholderGetters as $placeholder => $getter) {
                $placeholderValue = is_array($this->dataObject) ? $this->dataObject[$getter] : $this->dataObject->$getter();
                $url = preg_replace("/\{{$placeholder}\}/", $placeholderValue, $url);
            }

            if (preg_match('/^index.php/', $url)) {
                sfProjectConfiguration::getActive()->loadHelpers('Url');
                $url = public_path($url, true);
            }

            $linkAttributes = array(
                'href' => $url,
            );
                
            if ($this->hasProperty('labelGetter')) {
                $label = $this->getValue('labelGetter');
            } else {
                $label = $this->getPropertyValue('label', 'Undefined');
            }

            return content_tag('a', $label, $linkAttributes). $this->getHiddenFieldHTML();
     
        } else {
            return $this->toValue();
        }
    }

    public function toValue() {
        return $this->getValue('labelGetter');
    }
}
