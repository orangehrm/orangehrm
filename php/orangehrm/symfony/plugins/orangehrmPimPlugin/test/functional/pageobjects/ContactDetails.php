<?php



class ContactDetails extends PIMPage {

    public function __construct($selenium) {
        parent::__construct($selenium);
        $this->address01 = "contact_street1";
        $this->address02 = "contact_street2";
        $this->city = "contact_city";
        $this->province = "contact_province";
        $this->zipCode = "contact_emp_zipcode";
        $this->country = "contact_country";
        $this->homeTelephone = "contact_emp_hm_telephone";
        $this->mobile = "contact_emp_mobile";
        $this->workTelephone = "contact_emp_work_telephone";
        $this->workEmail = "contact_emp_work_email";
        $this->otherEmail = "contact_emp_oth_email";
    }

    public function editContactInfo($Address01, $Address02, $City, $Province, $ZipCode, $Country, $HomeTelephn, $Mobile, $workTelephn, $WorkEmail, $OtherEmail) {
        $this->selenium->selectFrame("relative=up");
        $this->selenium->click("btnSave");
        $this->selenium->type($this->address01, $Address01);
        $this->selenium->type($this->address02, $Address02);
        $this->selenium->type($this->city, $City);
        $this->selenium->type($this->province, $Province);
        $this->selenium->type($this->zipCode, $ZipCode);
        $this->selenium->select($this->country, $Country);
        $this->selenium->type($this->homeTelephone, $HomeTelephn);
        $this->selenium->type($this->mobile, $Mobile);
        $this->selenium->type($this->workTelephone, $workTelephn);
        $this->selenium->type($this->workEmail, $WorkEmail);
        $this->selenium->type($this->otherEmail, $OtherEmail);
        $this->selenium->click("btnSave");
        $this->selenium->waitForPageToLoad(Config::$timeoutValue);
        return new ContactDetails($this->selenium);
    }

    public function verifyContactDetailsPresent($Address01, $Address02, $City, $Province, $ZipCode, $Country, $HomeTelephn, $Mobile, $workTelephn, $WorkEmail, $OtherEmail) {
        if ($Address01 != $this->selenium->getValue($this->address01)) {
            return false;
        }
        if ($Address02 != $this->selenium->getValue($this->address02)) {
            return false;
        }
        if ($City != $this->selenium->getValue($this->city)) {
            return false;
        }
        if ($Province != $this->selenium->getValue($this->province)) {
            return false;
        }
        if ($ZipCode != $this->selenium->getValue($this->zipCode)) {
            return false;
        }
        if ($Country != $this->selenium->getSelectedLabel($this->country)) {
            return false;
        }
        if ($HomeTelephn != $this->selenium->getValue($this->homeTelephone)) {
            return false;
        }
        if ($Mobile != $this->selenium->getValue($this->mobile)) {
            return false;
        }
        if ($workTelephn != $this->selenium->getValue($this->workTelephone)) {
            return false;
        }
        if ($WorkEmail != $this->selenium->getValue($this->workEmail)) {
            return false;
        }
        if ($OtherEmail != $this->selenium->getValue($this->otherEmail)) {
            return false;
        }
        return TRUE;
    }

}
?>




