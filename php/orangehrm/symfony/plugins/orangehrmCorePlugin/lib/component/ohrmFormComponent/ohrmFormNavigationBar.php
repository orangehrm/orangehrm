<?php

class ohrmFormNavigationBar extends ohrmFormDecorator {
    public function  getHtml() {
        $navigationHtml = '';

        $navigationHtml .= tag('input', array(
            'type' => 'button',
            'value' => 'Back',
            'id' => 'ohrmFormNavigatorButton_Back',
            'class' => 'plainbtn'
        ));
        $navigationHtml = content_tag('div', $navigationHtml, array('style' => 'margin: 4px;'));

        return $navigationHtml . $this->decoratedForm->getHtml();
    }
}

