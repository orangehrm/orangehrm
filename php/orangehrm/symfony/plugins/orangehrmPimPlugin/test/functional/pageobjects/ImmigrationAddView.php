<?php



class ImmigrationAddView extends AddView
{
   public function  __construct($selenium) {
        $this->selenium=$selenium;
    	$list = new TitledList($this->selenium, "//form[@id='frmImmigrationDelete']");
    	$editpane = new ImmigrationEditPane($selenium);
    	parent::__construct($selenium, $list, $editpane);

       
       }

       /**
        *
        * @param <type> $ImmType
        * @param <type> $immNumber
        * @param <type> $immPassportIssueDate
        * @param <type> $immPassportExpDate
        * @param <type> $immStatus
        * @param <type> $immCountry
        * @param <type> $immReviewDate
        * @param <type> $immComment
        * @return <type>
        */
    public function saveDetails($ImmType,$immNumber,$immPassportIssueDate,$immPassportExpDate,$immStatus,$immCountry,$immReviewDate,$immComment){
    	return $this->editPane->saveDetails($ImmType,$immNumber,$immPassportIssueDate,$immPassportExpDate,$immStatus,$immCountry,$immReviewDate,$immComment);
    }
}
?>

