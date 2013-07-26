<?php

class EmailNotificationHeaderFactory extends ohrmListConfigurationFactory {

    protected function init() {

        $header1 = new ListHeader();
        $header2 = new ListHeader();
        $header3 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Notification Type',
            'width' => '30%',
            'isSortable' => false,
            'filters' => array('I18nCellFilter' => array()
                              ),
            'elementType' => 'link',
            'elementProperty' => array(
                'labelGetter' => 'getName',
                'placeholderGetters' => array('id' => 'getId'),
                'urlPattern' => 'saveSubscriber?notificationId={id}'),
        ));
        
        $header2->populateFromArray(array(
            'name' => 'Subscribers',
            'width' => '60%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getSubscriberList'),
        ));
        
        $header3->populateFromArray(array(
            'name' => 'Enabled',
            'width' => '10%',
            'elementType' => 'checkbox',
            'textAlignmentStyle' => 'center',
            'elementProperty' => array(
                'id' => 'ohrmList_chkSelectRecord_{id}',
                'name' => 'chkSelectRow[]',
                'valueGetter' => 'getId',
                'checkedGetter' => 'isEnable',
                'placeholderGetters' => array('id' => 'getId'),
             ),
        ));

        $this->headers = array($header1, $header2, $header3);
    }

    public function getClassName() {
        return 'EmailNotification';
    }

}

