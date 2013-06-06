<?php

class ohrmFormTitle extends ohrmFormDecorator {
    private $title = '';
    private $tag = 'h2';

    public function  getHtml() {
        $html = content_tag($this->tag, $this->title);
        $html = content_tag('div', $html, array('class' => 'mainHeading'));
        return $html . $this->decoratedForm->getHtml();
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setTag($tag) {
        $this->tag = $tag;
    }
}
