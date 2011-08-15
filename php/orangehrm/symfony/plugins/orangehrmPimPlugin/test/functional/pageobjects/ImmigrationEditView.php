<?php


//

class ImmigrationEditView extends EditView
{

    public function  __construct($selenium) {
        $editPane=new ImmigrationEditPane($selenium);
        $list = new TitledList($selenium, "/form[@id='frmImmigrationDelete']", FALSE );
        parent::__construct($selenium, $list, $editPane);

    }
    
    public function saveDetails($ImmType,$immNumber,$immPassportIssueDate,$immPassportExpDate,$immStatus,$immCountry,$immReviewDate,$immComment){
    	return $this->editPane->saveDetails($ImmType,$immNumber,$immPassportIssueDate,$immPassportExpDate,$immStatus,$immCountry,$immReviewDate,$immComment);
        
    }

}
?>
