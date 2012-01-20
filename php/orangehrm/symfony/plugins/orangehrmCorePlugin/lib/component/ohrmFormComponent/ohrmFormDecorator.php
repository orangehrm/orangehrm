<?php

abstract class ohrmFormDecorator implements ohrmFormInterface {
    protected $decoratedForm;

    public function  __construct(ohrmFormInterface $form) {
        $this->decoratedForm = $form;
    }

    public function getHtml() {
        return $this->decoratedForm->getHtml();
    }
}

