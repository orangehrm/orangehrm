<?php

class CommentCell extends Cell {
    
    public function __toString() {
        
        $name = $this->getPropertyValue('namePattern');
        $id = $this->getPropertyValue('namePattern');
        
        $imageHTML = tag('img', array(
            'src' => theme_path('images/callout-left.png'),
            'title' => 'Click here to edit',
            'alt' => 'Edit',
            'class' => 'callout dialogInvoker',
        ));

        $placeholderGetters = $this->getPropertyValue('placeholderGetters');
        $id = $this->generateAttributeValue($placeholderGetters, $this->getPropertyValue('idPattern'));
        $name = $this->generateAttributeValue($placeholderGetters, $this->getPropertyValue('namePattern'));
        
        $hiddenFieldHTML = tag('input', array(
            'type' => 'hidden',
            'id' => $id,
            'name' => $name,
            'value' => $this->getValue(),
        ));

        $commentContainerHTML = content_tag('span', $this->trimComment($this->getValue()), array(
            'id' => $this->generateAttributeValue($placeholderGetters, 'commentContainer-{id}'),
        ));

        $commentHTML = content_tag('span', $commentContainerHTML . $imageHTML . $hiddenFieldHTML, array(
            'class' => 'commentContainerLong',
        ));

        if ($this->isHiddenOnCallback()) {
            return '&nbsp;';
        }
        
        return $commentHTML . $this->getHiddenFieldHTML();
    }
    
    protected function trimComment($comment) {
        if (strlen($comment) > 30) {
            $comment = substr($comment, 0, 30) . '...';
        }
        return $comment;
    }
}


