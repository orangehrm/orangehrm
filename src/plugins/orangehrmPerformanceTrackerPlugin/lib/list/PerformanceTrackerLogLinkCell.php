<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is the PerformanceTrackerLogLinkCell
 *
 * @author chameera
 */
class PerformanceTrackerLogLinkCell extends Cell{
    public $currentUserId;
    public $loggedInUserId;
    
    
    protected function getLabel() {
        if ($this->hasProperty('labelGetter')) {
            $label = $this->getValue('labelGetter');
        } else {
            $label = $this->getPropertyValue('label', 'Undefined');
        }

        return $label;
    }
    
    public function __toString() {
        $linkable = $this->getPropertyValue('linkable', $this->dataObject->canUpdate());
        
        if (($linkable instanceof sfOutputEscaperArrayDecorator) || is_array($linkable)) {
            list($method, $params) = $linkable;
            $linkable = call_user_func_array(array($this->dataObject, $method), $params->getRawValue());
        }
        
        if ($linkable) {
            $placeholderGetters = $this->getPropertyValue('placeholderGetters');
            $urlPattern = $this->getPropertyValue('urlPattern');

            $url = $urlPattern;
            
            if (!is_null($placeholderGetters)) {
                foreach ($placeholderGetters as $placeholder => $getter) {
                    $placeholderValue = ($this->getDataSourceType() == self::DATASOURCE_TYPE_ARRAY) ? $this->dataObject[$getter] : $this->dataObject->$getter();
                    $url = preg_replace("/\{{$placeholder}\}/", $placeholderValue, $url);
                }
            }

            if (preg_match('/^index.php/', $url)) {
                sfProjectConfiguration::getActive()->loadHelpers('Url');
                $url = public_path($url, true);
            }

            $linkAttributes = array(
                'href' => $url,
            );
                
            $label = $this->getLabel();
            

            return content_tag('a', $label, $linkAttributes) 
                    . $this->getHiddenFieldHTML();
        } else {
            
            
            return $this->toValue() . $this->getHiddenFieldHTML();
        }
    }

    public function toValue() {
        return $this->getLabel();
    }

    

}

?>
