<?php
/**
 * 2011-06-23
 */

require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

class ViewCandidatesTest extends FunctionalTestcase {

   

    private static $isTablesLoaded;
    private static $fixture;
    protected function setUp() {

        
        $this->setBrowser(Helper::getBrowserString());
        $this->setBrowserUrl(Config::$browserURL);
        if(!self::$isTablesLoaded){
            self::$fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmRecruitmentPlugin/test/fixtures/CandidateDao.yml';
            TestDataService::populate(self::$fixture);
           self::$isTablesLoaded = true;
            
        }

                
    }

    public function testSearchByJobTitle() {
        
        $selenium = Helper::login($this, 'admin', 'admin')->getBrowserInstance();
        $viewCandidates = Menu::goToViewCandidate($selenium);
        $searchCriteria[$viewCandidates->cmbJobTitle] = "Architect";
        $viewCandidates->search($searchCriteria);
        $expected[0] = array("Vacancy" => "A 2011", "Candidate" => "Renukshan Sap");
        $expected[1] = array("Vacancy" => "B 2011", "Candidate" => "Yasitha Pandi");
        $expected[2] = array("Vacancy" => "B 2011", "Candidate" => "Renukshan Sap");
       // print_r($viewCandidates->list->getListedRecordsIntoAnArray());
        //print_r($expected);
        $this->assertTrue($viewCandidates->list->isRecordsPresentInList($expected));
         
         
        
    }

    public function testSearch(){
        echo "printing loading fixture: \n";
        print_r(sfYaml::load(self::$fixture));
    }
    






}

?>

