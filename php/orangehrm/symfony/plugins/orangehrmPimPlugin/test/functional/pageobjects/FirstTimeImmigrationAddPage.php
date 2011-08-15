<?php



class FirstTimeImmigrationAddPage extends PIMPage
{
    /**
     *
     * @var ImmigrationEditPane
     */
    private $editPane;

    function  __construct($selenium)
    {
        parent::__construct($selenium);
         $this->editPane=new ImmigrationEditPane($selenium);
    }
    
    public function saveDetails($ImmType,$immNumber,$immPassportIssueDate,$immPassportExpDate,$immStatus,$immCountry,$immReviewDate,$immComment){
    	return $this->editPane->saveDetails($ImmType,$immNumber,$immPassportIssueDate,$immPassportExpDate,$immStatus,$immCountry,$immReviewDate,$immComment);
    }
    

}

?>
