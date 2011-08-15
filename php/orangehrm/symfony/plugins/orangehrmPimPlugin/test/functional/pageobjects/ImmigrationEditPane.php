<?php

class ImmigrationEditPane extends EditPane
{

    private $immiType;
    private $immiNumber;
    private $immiPassportIssueDate;
    private $immiPassportExpDate;
    private $immiStatus;
    private $immiCountry;
    private $immiReviewDate;
    private $immiComment;

/**
 *
 * @param <type> $selenium
 * @param <type> $saveBUtton
 * @param <type> $cancelButton 
 */
    function  __construct($selenium) {
        parent::__construct($selenium, "btnSave", "btnCancel");
        $this->selenium = $selenium;
        //$this->immiType="//label[text()='".$ImmType."']";
        $this->immiNumber="immigration_number";
        $this->immiPassportIssueDate="immigration_passport_issue_date";
        $this->immiPassportExpDate="immigration_passport_expire_date";
        $this->immiStatus="immigration_i9_status";
        $this->immiCountry="immigration_country";
        $this->immiReviewDate="immigration_i9_review_date";
        $this->immiComment="immigration_comments";
    }
/**
 * 
 *
 * @param unknown_type $ImmType
 * @param unknown_type $immNumber
 * @param unknown_type $immPassportIssueDate
 * @param unknown_type $immPassportExpDate
 * @param unknown_type $immStatus
 * @param unknown_type $immCountry
 * @param unknown_type $immReviewDate
 * @param unknown_type $immComment
 * @return ImmigrationListView
 */
 
    function saveDetails($ImmType,$immNumber,$immPassportIssueDate,$immPassportExpDate,$immStatus,$immCountry,$immReviewDate,$immComment){

    $this->selenium->click("//label[text()='".$ImmType."']",$ImmType);
    $this->selenium->type($this->immiNumber,$immNumber);
    $this->selenium->type($this->immiPassportIssueDate,$immPassportIssueDate);
    $this->selenium->type($this->immiPassportExpDate,$immPassportExpDate);
    $this->selenium->type($this->immiStatus,$immStatus);
    $this->selenium->select($this->immiCountry,$immCountry);
    $this->selenium->type($this->immiReviewDate,$immReviewDate);
    $this->selenium->type($this->immiComment,$immComment);
    $this->save();
    return new ImmigrationListView($this->selenium);
}
}
?>
