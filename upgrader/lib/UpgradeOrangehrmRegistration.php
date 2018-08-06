<?php
/**
 * Created by PhpStorm.
 * User: maduka
 * Date: 2/8/18
 * Time: 5:56 PM
 */

class UpgradeOrangehrmRegistration
{
    public function sendRegistrationData() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://osreg-test-iris.orangehrm.com/registerAcceptor.php");
        curl_setopt($ch, CURLOPT_POST, 1);

        $data = "username=" . $_SESSION['defUser']['AdminUserName']
            . "&userEmail=" . $_SESSION['defUser']['organizationEmailAddress']
            . "&telephone=" . $_SESSION['defUser']['contactNumber']
            . "&admin_first_name=" . $_SESSION['defUser']['adminEmployeeFirstName']
            . "&admin_last_name=" . $_SESSION['defUser']['adminEmployeeLastName']
            . "&timezone=" . $_SESSION['defUser']['timezone']
            . "&language=" . $_SESSION['defUser']['language']
            . "&country=" . $_SESSION['defUser']['country']
            . "&organization_name=" . $_SESSION['defUser']['organizationName']
            . "&instance_identifier=" . $_SESSION['defUser']['organizationName'] . '_' . $_SESSION['defUser']['organizationEmailAddress'] . '_' . date('Y-m-d');

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