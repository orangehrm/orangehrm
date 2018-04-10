<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of registrationAction
 *
 * @author rimaz
 */
class registrationAction extends sfAction {

    public function preExecute() {
        $this->getUser()->setAttribute('currentScreen', 'registration');
    }

    public function execute($request) {
        if ($request->isMethod('post')) {
            $params = $request->getPostParameters();
            if ($params['request'] == "NOREG") {
                $this->getRequest()->setParameter('submitBy', 'registration');
                $this->forward('upgrade', 'index');
            }
            $this->reqAccept = $this->sendRegistrationData($params);
        }
    }

    protected function sendRegistrationData($postArr) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.orangehrm.com/registration/registerAcceptor.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        $data = "userName=" . $postArr['userName']
                . "&userEmail=" . $postArr['userEmail']
                . "&userTp=" . $postArr['userTp']
                . "&userComments=" . $postArr['userComments']
                . "&firstName=" . $postArr['firstName']
                . "&company=" . $postArr['company']
                . "&empCount=" . $postArr['empCount']
                . "&updates=" . (isset($postArr['chkUpdates']) ? '1' : '0');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        if (strpos($response, 'SUCCESSFUL') === false) {
            return false;
        } else {
            return true;
        }
    }

}
