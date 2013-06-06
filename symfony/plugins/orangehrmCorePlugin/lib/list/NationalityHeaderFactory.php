<?php

class NationalityListConfigurationFactory extends ohrmListConfigurationFactory {
    
    protected function init() {
        $header1 = new ListHeader();
        $header2 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Id',
            'width' => '45%',
            'isSortable' => true,
            'sortField' => 'nat_code',
            'elementType' => 'link',
            'elementProperty' => array(
                'labelGetter' => 'getNatCode',
                'placeholderGetters' => array('id' => 'getNatCode'),
                'urlPattern' => '../../../lib/controllers/CentralController.php?id={id}&uniqcode=NAT&capturemode=updatemode'),
        ));

        $header2->populateFromArray(array(
            'name' => 'Name',
            'isSortable' => true,
            'sortField' => 'nat_name',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getNatName'),
        ));

        $this->headers = array($header1, $header2);
    }
    
    public function getClassName() {
        return 'Nationality';
    }

}

