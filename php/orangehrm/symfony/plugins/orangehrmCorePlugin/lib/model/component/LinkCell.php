<?php

class LinkCell extends Cell {

    public function __toString() {
        $labelGetter = $this->getPropertyValue('labelGetter');
        $placeholderGetters = $this->getPropertyValue('placeholderGetters');
        $urlPattern = $this->getPropertyValue('urlPattern');

        $url = $urlPattern;
        foreach ($placeholderGetters as $placeholder => $getter) {
            $url = preg_replace("/\{{$placeholder}\}/", $this->dataObject->$getter(), $url);
        }

        $linkAttributes = array(
            'href' => $url,
        );

        $linkLabel = $this->dataObject->$labelGetter();
        return content_tag('a', $linkLabel, $linkAttributes);
    }

}

