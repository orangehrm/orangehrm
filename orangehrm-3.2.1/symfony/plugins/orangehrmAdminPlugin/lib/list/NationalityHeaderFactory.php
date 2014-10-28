<?php

class NationalityHeaderFactory extends ohrmListConfigurationFactory {
    protected function init() {

        $header1 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Nationality',
            'elementType' => 'link',
            'filters' => array('I18nCellFilter' => array()
                              ),
            'elementProperty' => array(
                'labelGetter' => 'getName',
                'urlPattern' => 'javascript:'),
        ));

        $this->headers = array($header1);
    }

    public function getClassName() {
        return 'Nationality';
    }
}

