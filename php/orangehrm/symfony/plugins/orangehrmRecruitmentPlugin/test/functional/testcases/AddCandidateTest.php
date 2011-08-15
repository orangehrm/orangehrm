<?php


class AddCandidateTest extends FunctionalTestcase{

    protected function setUp() {
        $this->setBrowser(Helper::getBrowserString());
        $this->setBrowserUrl(Config::$browserURL);
        Helper::deleteAllFromTable("ohrm_job_candidate");
        
    }
  
    public function testAddCandidate_MandatoryFieldValidation(){
        RecruitmentHelper::loadRecruitmentPrerequisites($this);

        $addCandidate = new AddCandidate($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddCandidateTest.yml";
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "MandatoryFieldMissing",$addCandidate);
        //echo "going to print input data "; print_r($inputData);

        $employeeInformation = Helper::login($this, 'admin', 'admin');        
        $i=0;
        foreach($inputData as $record){
           
            $addCandidate = Menu::goToAddCandidate($this);
            
            $addCandidate->saveCandidate($record);            
            $validations = $addCandidate->getArrayOfValidationMessages();
            if (!$record[$addCandidate->txtFirstName]){
                $searchFound = is_string(array_search("First name is required", $validations, "First Name validation failed for ". $record[$addCandidate->txtFirstName]));
                $this->assertTrue($searchFound);                
            }
            if (!$record[$addCandidate->txtLastName]){
                $searchFound = is_string(array_search("Last name is required", $validations, "Last Name validation failed for ". $record[$addCandidate->txtLastName]));
                $this->assertTrue($searchFound);               
            }
            if (!$record[$addCandidate->txtEmail]){
                $searchFound = is_string(array_search("E-mail is required", $validations, "First Name validation failed for ". $record[$addCandidate->txtEmail]));
                $this->assertTrue($searchFound);               
            }
            $i++;
        }

        Helper::logOutIfLoggedIn($this);
        
    }
    
    public function testInvalidEmail(){
        
        RecruitmentHelper::loadRecruitmentPrerequisites($this);

        $addCandidate = new AddCandidate($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddCandidateTest.yml";
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "InvalidEmail",$addCandidate);
        //echo "going to print input data "; print_r($inputData);

        $employeeInformation = Helper::login($this, 'admin', 'admin');
        $i=0;
        foreach($inputData as $record){

            $addCandidate = Menu::goToAddCandidate($this);

            $addCandidate->saveCandidate($record);
            $validations = $addCandidate->getArrayOfValidationMessages();
            $searchFound = is_string(array_search("Email address should contain at least one '.' and one '@' Example:user@example.com", $validations));
            $this->assertTrue($searchFound, $record[$addCandidate->txtEmail]. " did get saved");
            $i++;
        }

        Helper::logOutIfLoggedIn($this);
 

        }

    public function testInvalidContactNumber(){
        
        RecruitmentHelper::loadRecruitmentPrerequisites($this);
        $addCandidate = new AddCandidate($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddCandidateTest.yml";
        
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "InvalidContactNumber", $addCandidate);

        $employeeInformation = Helper::login($this, 'admin', 'admin');
        $i=0;
        foreach($inputData as $record){

            $addCandidate = Menu::goToAddCandidate($this);
            $addCandidate->saveCandidate($record);
            $validations = $addCandidate->getArrayOfValidationMessages();
            $searchFound = is_string(array_search("Enter a valid contact number", $validations));
            //print_r($validations);
            $this->assertTrue($searchFound, $record[$addCandidate->txtContactNo]. " did get saved");
            $i++;
        }

        Helper::logOutIfLoggedIn($this);

    }

    public function testInvalidDate(){

        RecruitmentHelper::loadRecruitmentPrerequisites($this);
        $addCandidate = new AddCandidate($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddCandidateTest.yml";
        
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "InvalidDate", $addCandidate);

        $employeeInformation = Helper::login($this, 'admin', 'admin');
        $i=0;
        foreach($inputData as $record){

            $addCandidate = Menu::goToAddCandidate($this);
            $addCandidate->saveCandidate($record);
            $validations = $addCandidate->getArrayOfValidationMessages();
            $searchFound = is_string(array_search("Enter a valid date in YYYY-MM-DD format", $validations));
            $this->assertTrue($searchFound, $record[$addCandidate->txtDateOfApplication]. " did get saved");
            $i++;
        }

        Helper::logOutIfLoggedIn($this);

    }

    public function testAddValidCandidates(){
        RecruitmentHelper::loadRecruitmentPrerequisites($this);
        $addCandidate = new AddCandidate($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddCandidateTest.yml";
        
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "ValidCandidate", $addCandidate);

        $employeeInformation = Helper::login($this, 'admin', 'admin');
        $i=0;
        foreach($inputData as $record){
            if($record[$addCandidate->txtresume]){
                $record[$addCandidate->txtresume] = Config::$absolutePath . Helper::convertPathToCurrentPlatform($record[$addCandidate->txtresume]);
                //print_r($record[$addCandidate->txtresume]);
            }
            $addCandidate = Menu::goToAddCandidate($this);
            $viewCandidate = $addCandidate->saveCandidate($record);
            $candidateFullName = Helper::getFullName($record[$addCandidate->txtFirstName], $record[$addCandidate->txtLastName]);
            $expected[0] = array("Candidate" => $candidateFullName);
            //print_r($viewCandidate->list->getListedRecordsIntoAnArray());
            $this->assertTrue($viewCandidate->list->isRecordsPresentInList($expected), $candidateFullName. " is not in the list");
            $i++;

    }

   Helper::logOutIfLoggedIn($this);


}
public function testAddCandidate_LengthValidation(){
        RecruitmentHelper::loadRecruitmentPrerequisites($this);

        $addCandidate = new AddCandidate($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddCandidateTest.yml";
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "LengthValidation",$addCandidate);
        //echo "going to print input data "; print_r($inputData);

        $employeeInformation = Helper::login($this, 'admin', 'admin');        
        $i=0;
        foreach($inputData as $record){
           
            $addCandidate = Menu::goToAddCandidate($this);
            
            $addCandidate->saveCandidate($record);            
            $validations = $addCandidate->getArrayOfValidationMessages();
            if ($record[$addCandidate->txtFirstName]){
                $searchFound = is_string(array_search("Please enter no more than 30 characters", $validations, "First Name validation failed for ". $record[$addCandidate->txtFirstName]));
                $this->assertTrue($searchFound);                
            }
            if (!$record[$addCandidate->txtMiddleName]){
                $searchFound = is_string(array_search("Please enter no more than 30 characters", $validations, "Middle Name validation failed for ". $record[$addCandidate->txtMiddleName]));
                $this->assertTrue($searchFound);               
            }
            if (!$record[$addCandidate->txtLastName]){
                $searchFound = is_string(array_search("Please enter no more than 30 characters", $validations, "Last Name validation failed for ". $record[$addCandidate->txtLastName]));
                $this->assertTrue($searchFound);               
            }
            if (!$record[$addCandidate->txtEmail]){
                $searchFound = is_string(array_search("Please enter no more than 30 characters", $validations, "Email validation failed for ". $record[$addCandidate->txtEmail]));
                $this->assertTrue($searchFound);               
            }
            if (!$record[$addCandidate->txtContactNo]){
                $searchFound = is_string(array_search("Please enter no more than 30 characters", $validations, "Contact No validation failed for ". $record[$addCandidate->txtContactNo]));
                $this->assertTrue($searchFound);               
            }
            if (!$record[$addCandidate->txtKeywords]){
                $searchFound = is_string(array_search("Please enter no more than 255 characters", $validations, "Keywords validation failed for ". $record[$addCandidate->txtKeywords]));
                $this->assertTrue($searchFound);
            }
             if (!$record[$addCandidate->txtComment]){
                $searchFound = is_string(array_search("Please enter no more than 255 characters", $validations, "Comment validation failed for ". $record[$addCandidate->txtComment]));
                $this->assertTrue($searchFound);
            }
            
            $i++;
        }
        Helper::logOutIfLoggedIn($this);
        
    }
     public function testInvalidResume(){

        RecruitmentHelper::loadRecruitmentPrerequisites($this);
        $addCandidate = new AddCandidate($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddCandidateTest.yml";
        
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "InvalidResume", $addCandidate);

        $employeeInformation = Helper::login($this, 'admin', 'admin');
        $i=0;
        foreach($inputData as $record){
            if($record[$addCandidate->txtresume]){
                $record[$addCandidate->txtresume] = Config::$absolutePath . Helper::convertPathToCurrentPlatform($record[$addCandidate->txtresume]);
                //print_r($record[$addCandidate->txtresume]);
            }
            $addCandidate = Menu::goToAddCandidate($this);
            $viewCandidate = $addCandidate->saveCandidate($record);
            if($i==0){
            $this->assertEquals($addCandidate->getSavedSuccessfullyMessage(), "Adding Candidate Failed - Invalid File Type" );
            }
            if($i==1){
            $this->assertEquals($addCandidate->getSavedSuccessfullyMessage(), "Adding Candidate Failed. Resume Size Exceeded 1MB" );
            }
            $i++;
        }

        Helper::logOutIfLoggedIn($this);

    }
    
    
    
    
}

?>
