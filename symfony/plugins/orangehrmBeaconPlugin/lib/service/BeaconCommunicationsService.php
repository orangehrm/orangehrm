<?php

/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */
class BeaconCommunicationsService extends BaseService implements StateAccessibleByExecutionFilters {

    const BEACON_ACTIVATION_REQUIRED = 'beacon.activation';
    const BEACON_FLASH_REQUIRED = 'beacon.flash';
    const BEACON_MESSAGES_REQUIRED = 'beacon.messages';

    private static $state = self::EMPTY_STATE;
    protected $beaconConfigService;
    protected $beaconDataPointService;

    /**
     * 
     * @return BeaconDatapointService
     */
    protected function getBeaconDatapointService() {
        if (is_null($this->beaconDataPointService)) {
            $this->beaconDataPointService = new BeaconDatapointService();
        }

        return $this->beaconDataPointService;
    }

    public static function getState() {
        return self::$state;
    }

    public function getBeaconConfigurationService() {
        if (is_null($this->beaconConfigService)) {
            $this->beaconConfigService = new BeaconConfigurationService();
        }
        return $this->beaconConfigService;
    }

    public function acquireLock() {


        if ($this->getBeaconConfigurationService()->setBeaconLock('locked') > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function releaseLock() {
        if ($this->getBeaconConfigurationService()->getBeaconLock() == 'locked') {
            $this->getBeaconConfigurationService()->setBeaconLock('unlocked');
        }
    }

    public function setBeaconActivation() {

        if ($this->getBeaconConfigurationService()->getBeaconActivationStatus() != 'on') {

            if ($this->acquireLock()) {
                sfContext::getInstance()->getUser()->setAttribute(self::BEACON_ACTIVATION_REQUIRED, true);
                sfContext::getInstance()->getUser()->setAttribute(self::BEACON_MESSAGES_REQUIRED, true);
                if ($this->getBeaconConfigurationService()->getBeaconActivationAcceptanceStatus() == 'on') {
                    sfContext::getInstance()->getUser()->setAttribute(self::BEACON_FLASH_REQUIRED, true);
                }
            }
        } else {
            $this->enablePortalCommunication();
        }
    }

    public function enablePortalCommunication() {
        //the value is a unix timestamp storing the minimum next flash time

        $nextFlashDate = (int) $this->getBeaconConfigurationService()->getBeaconNextFlashTime();

        if (time() > $nextFlashDate) {

            if ($this->acquireLock()) {

                sfContext::getInstance()->getUser()->setAttribute(self::BEACON_MESSAGES_REQUIRED, true);
                if ($this->getBeaconConfigurationService()->getBeaconActivationAcceptanceStatus() == 'on') {
                    sfContext::getInstance()->getUser()->setAttribute(self::BEACON_FLASH_REQUIRED, true);
                }
            }
        }
    }

    protected function resolveDatapointMessages($messages) {
        $idArray = array();
        foreach ($messages as $message) {
            try {
                $messageBody = new SimpleXMLElement($message['definition']);
                $idArray[] = $message['id'];
                $dataPoint = new DataPoint();
                if ($message['operation'] == 'DELETE') {
                    $result = $this->getBeaconDatapointService()->deleteDatapointByName($message['name']);
                    if ($result > 0) {
                        $idArray[] = $message['id'];
                    }
                    continue;
                }
                if ($message['operation'] == 'UPDATE') {
                    $dataPoint = $this->getBeaconDatapointService()->getDatapointByName($message['name']);
                }
                $dataPoint->setName($message['name']);

                $dataPoint->setDefinition($message['definition']);
                $dataPoint->setDataPointType($this->getBeaconDatapointService()->getDatapointTypeByName($messageBody['type'])->getFirst());

                $dataPoint->save();
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }

        return $idArray;
    }

    protected function resolveConfigMessages($messages) {
        $idArray = array();
        foreach ($messages as $message) {
            $this->getBeaconConfigurationService()->changeConfigTable($message['definition']);
            $idArray[] = $message['id'];
        }

        return $idArray;
    }

    public function resolveNotificationMessages($messages) {
        $idArray = array();
        $beaconNotificationService = new BeaconNotificationService();

        foreach ($messages as $message) {
            try {
                $messageBody = new SimpleXMLElement($message['definition']);
                $name = $message['name'];
                $idArray[] = $message['id'];
                $notification = new BeaconNotification();
                if ($message['operation'] == 'DELETE') {

                    $result = $beaconNotificationService->deleteNotificationByName($name);
                    continue;
                } else if ($message['operation'] == 'UPDATE') {
                    $notification = $beaconNotificationService->getNotificationByName($name);
                }

                $notification->setName($name);
                $notification->setDefinition($message['definition']);
                $period = trim($messageBody->settings->period . "");
                $expiry = time() + (int) $period;
                $time = new DateTime();
                $time->setTimestamp($expiry);

                $notification->setExpiryDate($time->format('Y-m-d H:i:s'));
                $notification->save();
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }

        return $idArray;
    }

    public function resolveMessages($messages) {

        $idArray = array();
        foreach ($messages as $name => $messageArray) {
            switch ($name) {
                case 'datapoints':

                    $idArray['datapoints'] = $this->resolveDatapointMessages($messageArray);


                    break;
                case 'configurations':
                    $idArray['configurations'] = $this->resolveConfigMessages($messageArray);
                    break;
                case 'notifications':
                    $idArray['notifications'] = $this->resolveNotificationMessages($messageArray);
                    break;
            }
        }


        return $idArray;
    }

    public function sendRegistrationMessage() {
        echo 'registering \n';
        $url = "https://opensource-updates.orangehrm.com/app.php/register";
        $data = http_build_query(array(
            'serverAddr' => array_key_exists('SERVER_ADDR', $_SERVER) ? urlencode($_SERVER['SERVER_ADDR']) : urlencode($_SERVER['LOCAL_ADDR']),
            'host' => urlencode(php_uname("s") . " " . php_uname("r")),
            'httphost' => urlencode($_SERVER['HTTP_HOST']),
            'phpVersion' => urlencode(constant('PHP_VERSION')),
            'server' => urlencode($_SERVER['SERVER_SOFTWARE']),
            'ohrmVersion' => urlencode('Open Source 3.2'),
        ));


        $contextOpts = array(
            'ssl' => array(
                'verify_peer' => false,
                'allow_self_signed' => true,
                'cafile' => '/etc/ssl/certs/cacert.pem',
                'capath' => '/etc/ssl/certs',
                'verify_depth' => 20,
                'CN_match' => '*.orangehrm.com',
                'disable_compression' => true,
                'SNI_enabled' => true,
                'ciphers' => 'ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW!RC4'
            ),
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $data
            )
        );
        var_dump($data);

//        foreach ($data as $key => $value) {
//            $fields_string .= $key . '=' . $value . '&';
//        }
//
//        $ch = curl_init();
//
////set the url, number of POST vars, POST data
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_POST, count($data));
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $result = curl_exec($ch);

        $sslContext = stream_context_create($contextOpts);
        $result = file_get_contents($url, null, $sslContext);
        var_dump($result);
        $headers = $http_response_header;
        var_dump($headers);

//        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        curl_close($ch);
        if (strpos($headers[0], '200 OK') !== false && strpos($result, 'SUCCESSFUL') !== false) {

            $resultParams = json_decode($result, true);

            if (isset($resultParams['uuid'])) {
                $this->getBeaconConfigurationService()->setBeaconUuid(base64_encode(urldecode($resultParams['uuid'])));
            }
            $this->getBeaconConfigurationService()->setBeaconActivationStatus('on');

            return true;
        }

        return false;
    }

    public function sendBeaconFlash() {
        echo 'flashing \n';
        $url = "https://opensource-updates.orangehrm.com/app.php/flash";
        $data = $this->getBeaconDatapointService()->resolveAllDatapoints();
        $uuid = base64_decode($this->getBeaconConfigurationService()->getBeaconUuid());

        $content = array();
        $content['uuid'] = urlencode($uuid);
        $content['flash_data'] = $data;

        $contentJSON = json_encode($content);

        $contextOpts = array(
            'ssl' => array(
                'verify_peer' => false,
                'allow_self_signed' => true,
                'cafile' => '/etc/ssl/certs/cacert.pem',
                'verify_depth' => 5,
                'CN_match' => '127.0.0.1',
                'disable_compression' => true,
                'SNI_enabled' => true,
                'ciphers' => 'ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW!RC4'
            ),
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => $contentJSON
            )
        );
        var_dump($contentJSON);
        $sslContext = stream_context_create($contextOpts);
        $result = file_get_contents($url, null, $sslContext);
        var_dump($result);
        $headers = $http_response_header;

//        $ch = curl_init();
//
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $contentJSON);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//            'Content-Type: application/json',
//            'Content-Length: ' . strlen($contentJSON))
//        );
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $result = curl_exec($ch);
        //      $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //  curl_close($ch);
        if (strpos($headers[0], '200 OK') !== false && strpos($result, 'SUCCESSFUL') !== false) {

            $flashPeriod = (int) $this->getBeaconConfigurationService()->getBeaconFlashPeriod();
            $this->getBeaconConfigurationService()->setBeaconNextFlashTime(time() + $flashPeriod);
            return true;
        }

        return false;
    }

    public function getBeaconMessages() {
        echo "messages \n";
        $url = "https://opensource-updates.orangehrm.com/app.php/messages";
        $uuid = base64_decode($this->getBeaconConfigurationService()->getBeaconUuid());

        $content = array();
        $content['uuid'] = urlencode($uuid);
        $content['type'] = 'REQ';

        $contentJSON = json_encode($content);
        var_dump($contentJSON);
//        $ch = curl_init();
//
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $contentJSON);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//            'Content-Type: application/json',
//            'Content-Length: ' . strlen($contentJSON))
//        );
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//        $result = curl_exec($ch);
//        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contextOpts = array(
            'ssl' => array(
                'verify_peer' => false,
                'allow_self_signed' => true,
                'cafile' => '/etc/ssl/certs/cacert.pem',
                'verify_depth' => 5,
                'CN_match' => '127.0.0.1',
                'disable_compression' => true,
                'SNI_enabled' => true,
                'ciphers' => 'ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW!RC4'
            ),
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => $contentJSON
            )
        );

        $sslContext = stream_context_create($contextOpts);
        $result = file_get_contents($url, null, $sslContext);
        var_dump($result);
        $headers = $http_response_header;

        if (strpos($headers[0], '200 OK')) {

            $result = json_decode($result, true);

            if (!empty($result)) {

                $idArray = $this->resolveMessages($result);

                if (!empty($idArray)) {
                    $ackContent = array();
                    $ackContent['type'] = 'ACK';
                    $ackContent['ids'] = $idArray;
                    $idsJSON = json_encode($ackContent);
                    var_dump($idsJSON);
                    $contextOpts['http']['content'] = $idsJSON;
                    $sslContext = stream_context_create($contextOpts);
//                    stream_context_set_option($sslContext, null, 'content', $idsJSON);
//                    var_dump($idsJSON);
//                    curl_setopt($ch, CURLOPT_POSTFIELDS, $idsJSON);
//                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//                        'Content-Type: application/json',
//                        'Content-Length: ' . strlen($idsJSON))
//                    );
                    $result = file_get_contents($url, null, $sslContext);
                    var_dump($result);
//                    $result = curl_exec($ch);
                }
            }
        }
    }

}
