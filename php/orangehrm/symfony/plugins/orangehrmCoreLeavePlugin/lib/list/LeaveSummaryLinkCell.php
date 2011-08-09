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

class LeaveSummaryLinkCell extends Cell {

    public function __toString() {
        $linkable = $this->getPropertyValue('linkable', true);

        if (($linkable instanceof sfOutputEscaperArrayDecorator) || is_array($linkable)) {
            list($method, $params) = $linkable;
            $linkable = call_user_func_array(array($this->dataObject, $method), $params->getRawValue());
        }

        if ($linkable) {
            $placeholderGetters = $this->getPropertyValue('placeholderGetters');
            $urlPattern = $this->getPropertyValue('urlPattern');
            if ($this->dataObject->getEmployeeId() == $_SESSION['empNumber']) {
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

            return content_tag('a', $this->getValue('labelGetter'), $linkAttributes)
                    . $this->getHiddenFieldHTML();
        } else {
            return $this->toValue();
        }
    }

    public function toValue() {
        return $this->getValue('labelGetter');
    }
}
