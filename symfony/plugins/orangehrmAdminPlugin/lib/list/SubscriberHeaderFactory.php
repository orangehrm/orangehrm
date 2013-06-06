<?php

class SubscriberHeaderFactory extends ohrmListConfigurationFactory{
    protected function init() {

        $header1 = new ListHeader();
        $header2 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Name',
            'elementType' => 'link',
            'elementProperty' => array(
                'labelGetter' => 'getName',
                'urlPattern' => 'javascript:'),
        ));

         $header2->populateFromArray(array(
            'name' => 'Email',
            'width' => '49%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getEmail'),
        ));

        $this->headers = array($header1, $header2);
    }

    public function getClassName() {
        return 'Subscriber';
    }
}

