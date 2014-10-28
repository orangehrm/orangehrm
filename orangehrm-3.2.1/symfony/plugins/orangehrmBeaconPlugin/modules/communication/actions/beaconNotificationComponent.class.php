<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of beaconNotificationsComponent
 *
 * @author chathura
 */
class beaconNotificationComponent extends sfComponent {

    const MAX_TRIES = 5;

    private $beaconNotificationService;

    protected function getBeaconNotificationService() {
        if (is_null($this->beaconNotificationService)) {
            $this->beaconNotificationService = new BeaconNotificationService();
        }
        return $this->beaconNotificationService;
    }

    public function execute($request) {
        $this->notificationEnabled = false;
//        if ($this->getUser()->getAttribute('auth.isAdmin') == 'Yes') {
            $count = 0;
            $notification = null;

            do {
                if (isset($notification) && $notification) {
                    $this->getBeaconNotificationService()->deleteNotificationByName($notification->getName());
                }
                $notificationArr = $this->getBeaconNotificationService()->getRandomNotification();
                if (!$notificationArr) {
                    break;
                }
                $notification = new BeaconNotification();
                $notification->fromArray($notificationArr);
            } while (time() > strtotime($notification->getExpiryDate()) && ++$count < self::MAX_TRIES);
            if (isset($notification) && $count < self::MAX_TRIES) {
                $this->notificationEnabled = true;
                $notificationXML = new SimpleXMLElement($notification->getDefinition());
                $this->notificationHeader = trim($notificationXML->content->header . "");
                $this->notificationBody = trim($notificationXML->content->body."");
            }
//        }
    }

//put your code here
}
