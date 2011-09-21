<?php

class AncientNationalityListConfigurationFactory extends ohrmListConfigurationFactory {
    
    protected function init() {
        $header1 = new ListHeader();
        $header2 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Id',
            'width' => '45%',
            'isSortable' => false,
            'elementType' => 'link',
            'elementProperty' => array(
                'labelGetter' => 0,
                'default' => 'default link value',
                'placeholderGetters' => array('id' => 0),
                'urlPattern' => '../../../lib/controllers/CentralController.php?id={id}&uniqcode=NAT&capturemode=updatemode'),
        ));

        $header2->populateFromArray(array(
            'name' => 'Name',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 1, 'default' => 'default label value',),
        ));

        $this->headers = array($header1, $header2);
    }
    
    public function getClassName() {
        return 'AncientNationality';
    }

}

