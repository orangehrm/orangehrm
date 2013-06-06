<?php

class EmailNotificationHeaderFactory extends ohrmListConfigurationFactory {

    protected function init() {

        $header1 = new ListHeader();
        $header2 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Notification Type',
            'width' => '49%',
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
            'width' => '49%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getSubscriberList'),
        ));

        $this->headers = array($header1, $header2);
    }

    public function getClassName() {
        return 'EmailNotification';
    }

}

