<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
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
