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
class sendBeaconMessageAjaxAction extends sfAction {

    protected $beaconConfigService;
    protected $beaconDatapointService;
    protected $beaconCommunicationService;
    

    /**
     * 
     *
     * @return BeaconCommunicationsService
     */
    protected function getBeaconCommunicationService() {
        if (is_null($this->beaconCommunicationService)) {
            $this->beaconCommunicationService = new BeaconCommunicationsService();
        }

        return $this->beaconCommunicationService;
    }

    /**
     * 
     * @return BeaconDatapointService
     */
    protected function getBeaconDatapointService() {
        if (is_null($this->beaconDatapointService)) {
            $this->beaconDatapointService = new BeaconDatapointService();
        }
        return $this->beaconDatapointService;
    }

    protected function getBeaconConfigService() {
        if (is_null($this->beaconConfigService)) {
            $this->beaconConfigService = new BeaconConfigurationService();
        }
        return $this->beaconConfigService;
    }

    public function execute($request) {
        
        if ($this->getUser()->hasAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_REQUIRED) && $this->getUser()->getAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_REQUIRED)) {
            $this->getUser()->setAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_REQUIRED, false);
            $result = $this->sendRegistrationMessage();
            if ($result && $this->getBeaconConfigService()->getBeaconActivationAcceptanceStatus()=='on') {
                $this->getBeaconMessages();
                $this->sendBeaconFlash();
            }
            $this->getBeaconCommunicationService()->releaseLock();
        } else if ($this->getUser()->hasAttribute(BeaconCommunicationsService::BEACON_FLASH_REQUIRED) && $this->getUser()->getAttribute(BeaconCommunicationsService::BEACON_FLASH_REQUIRED)) {
            $this->getUser()->setAttribute(BeaconCommunicationsService::BEACON_FLASH_REQUIRED, false);
            $this->getBeaconMessages();
            $this->sendBeaconFlash();
            $this->getBeaconCommunicationService()->releaseLock();
        }

        return sfView::NONE;
    }

    /**
     * @return bool Description
     */
    public function sendRegistrationMessage() {
        echo 'registering \n';
        $url = "https://opensource-updates.orangehrm.com/app.php/register";
        $data = http_build_query(array(
            'serverAddr' => array_key_exists('SERVER_ADDR',$_SERVER)?urlencode($_SERVER['SERVER_ADDR']):urlencode($_SERVER['LOCAL_ADDR']),
            'host' => urlencode(php_uname("s")." ".php_uname("r")),
            'httphost'=> urlencode($_SERVER['HTTP_HOST']),
            'phpVersion' => urlencode(constant('PHP_VERSION')),
            'server' => urlencode($_SERVER['SERVER_SOFTWARE']),           
            'ohrmVersion'=> urlencode('Open Source 3.1.4'),            
        ));


        $contextOpts = array(
            'ssl' => array(
                'verify_peer' => false,
                'allow_self_signed' => true,
                'cafile' => '/etc/ssl/certs/cacert.pem',
                'capath'=>'/etc/ssl/certs',
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
                $this->getBeaconConfigService()->setBeaconUuid(base64_encode(urldecode($resultParams['uuid'])));
            }
            $this->getBeaconConfigService()->setBeaconActivationStatus('on');

            return true;
        }

        return false;
    }

    public function sendBeaconFlash() {
        echo 'flashing \n';
        $url = "https://opensource-updates.orangehrm.com/app.php/flash";
        $data = $this->getBeaconDatapointService()->resolveAllDatapoints();
        $uuid = base64_decode($this->getBeaconConfigService()->getBeaconUuid());

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

            $flashPeriod = (int) $this->getBeaconConfigService()->getBeaconFlashPeriod();
            $this->getBeaconConfigService()->setBeaconNextFlashTime(time() + $flashPeriod);
            return true;
        }

        return false;
    }

    public function getBeaconMessages() {
        echo "messages \n";
        $url = "https://opensource-updates.orangehrm.com/app.php/messages";
        $uuid = base64_decode($this->getBeaconConfigService()->getBeaconUuid());

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

                $idArray = $this->getBeaconCommunicationService()->resolveMessages($result);

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
