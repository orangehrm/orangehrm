<?php




class AddVacancyTest extends FunctionalTestcase{ 
    

    public static $loadedFixture;
    public static $isTablesLoaded;

    public  function setUp() {        
        $this->setBrowser(Helper::getBrowserString());
        $this->setBrowserUrl(Config::$browserURL);

        if(!self::$isTablesLoaded){
            $fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmRecruitmentPlugin/test/fixtures/AddVacancyPrerequisites.yml';
            TestDataService::populate($fixture);
           self::$isTablesLoaded = true;

        }
        
    }
    
    public function testAddValidVacancies(){
        $addVacancy = new AddVacancy($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddVacancyTest.yml";
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "ValidVacancies", $addVacancy);
        $employeeInformation = Helper::login($this, 'admin', 'admin');
        $i=0;
        foreach($inputData as $record){
            $addVacancy = Menu::goToAddVacancy($this);
            $addVacancy->saveVacancy($record);
            $this->assertEquals("Job Vacancy Saved Successfully", $addVacancy->getSavedSuccessfullyMessage(), $record[$addVacancy->txtVacancyName]. " did not get saved");
            $i++;

        }
        Helper::logOutIfLoggedIn($this);
    }

    public function testAddJobVacancy_MandatoryFieldValidation(){

        $addVacancy = new AddVacancy($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddVacancyTest.yml";
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "MandatoryFieldsMissing", $addVacancy);

        $employeeInformation = Helper::login($this, 'admin', 'admin');
        $i=0;
        foreach($inputData as $record){

            $addVacancy = Menu::goToAddVacancy($this);
            $addVacancy->saveVacancy($record);
            $validations = $addVacancy->getArrayOfValidationMessages();

            if (!$record[$addVacancy->cmbJobTitle]){
                $searchFound = is_string(array_search("Job Title is required", $validations, "Job Title validation failed for ". $record[$addVacancy->txtVacancyName]));
                $this->assertTrue($searchFound);
            }
            if (!$record[$addVacancy->txtVacancyName]){
                $searchFound = is_string(array_search("Vacancy name is required", $validations, "Vacancy Name validation failed for ". $record[$addVacancy->cmbJobTitle]));
                $this->assertTrue($searchFound);
            }
            if (!$record[$addVacancy->txtHiringManager]){
                $searchFound = is_string(array_search("Enter a valid employee name", $validations, "Hiring Manager validation failed for ". $record[$addVacancy->txtHiringManager]));
                $this->assertTrue($searchFound);
            }
            $i++;
        }

        Helper::logOutIfLoggedIn($this);
        
    }
 

    public function testAddJobVacancy_InvalidHiringManager(){
        
        $addVacancy = new AddVacancy($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddVacancyTest.yml";
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "InvalidHiringManager", $addVacancy);
        

        $employeeInformation = Helper::login($this, 'admin', 'admin');
        $i=0;
        foreach($inputData as $record){

            $addVacancy = Menu::goToAddVacancy($this);
            $addVacancy->saveVacancy($record);
            $validations = $addVacancy->getArrayOfValidationMessages();
            $searchFound = is_string(array_search("Enter a valid employee name", $validations));
            $this->assertTrue($searchFound, $record[$addVacancy->txtHiringManager]. " validation is not displayed or vacancy get saved");
            $i++;    
    }
    Helper::logOutIfLoggedIn($this);
    }

    public function testAddJobVacancy_InvalidNoOfPositions(){
        
        $addVacancy = new AddVacancy($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddVacancyTest.yml";
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "InvalidNoOfPositions", $addVacancy);

        $employeeInformation = Helper::login($this, 'admin', 'admin');
        $i=0;
        foreach($inputData as $record){

            $addVacancy = Menu::goToAddVacancy($this);
            $addVacancy->saveVacancy($record);
            $validations = $addVacancy->getArrayOfValidationMessages();
            $searchFound = is_string(array_search("Number of positions should be a positive integer", $validations));
            $this->assertTrue($searchFound, $record[$addVacancy->txtNoOfPositions]. " validation is not displayed or vacancy get saved");
            $i++;    
    }
    Helper::logOutIfLoggedIn($this);
    }
    
    
    public function testAddJobVacancy_LengthValidation(){
        
        $addVacancy = new AddVacancy($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddVacancyTest.yml";
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "LengthValidation", $addVacancy);

        $employeeInformation = Helper::login($this, 'admin', 'admin');
        $i=0;
        foreach($inputData as $record){

            $addVacancy = Menu::goToAddVacancy($this);
            $addVacancy->saveVacancy($record);
            $validations = $addVacancy->getArrayOfValidationMessages();
            $searchFound = is_string(array_search("Description length cannot exceed 250 characters", $validations));
            $this->assertTrue($searchFound, $record[$addVacancy->txtDesc]. " validation is not displayed or vacancy get saved");
            $i++;    
    }
    Helper::logOutIfLoggedIn($this);
    
    }
   
    
    public function testAddVacancies_WithSpecialChars(){
          
        $addVacancy = new AddVacancy($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddVacancyTest.yml";
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "SpecialCharacters", $addVacancy);
        
        $employeeInformation = Helper::login($this, 'admin', 'admin');
        $i=0;
        foreach($inputData as $record){
            $addVacancy = Menu::goToAddVacancy($this);
            $addVacancy->saveVacancy($record);
            $this->assertEquals("Job Vacancy Saved Successfully", $addVacancy->getSavedSuccessfullyMessage(), $record[$addVacancy->txtVacancyName]. " did not get saved");
            $i++;

        }
        Helper::logOutIfLoggedIn($this);
    }
    
    public function testAddVacancies_DuplicateVacancy(){
          
        $addVacancy = new AddVacancy($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddVacancyTest.yml";
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "DuplicateJobVacancy", $addVacancy);
        $employeeInformation = Helper::login($this, 'admin', 'admin');
        $i=0;
        foreach($inputData as $record){
            $addVacancy = Menu::goToAddVacancy($this);
            if ($i==0){
            $addVacancy->saveVacancy($record);
            $this->assertEquals("Job Vacancy Saved Successfully", $addVacancy->getSavedSuccessfullyMessage(), $record[$addVacancy->txtVacancyName]. " did not get saved");
            }
            
            if($i != 0){
            $addVacancy->saveVacancy($record);  
            $validations = $addVacancy->getArrayOfValidationMessages();
            $searchFound = is_string(array_search("This vacancy already exists", $validations));
            $this->assertTrue($searchFound, $record[$addVacancy->txtVacancyName]. " validation is not displayed or vacancy get saved");
            }
            $i++;

        }
        Helper::logOutIfLoggedIn($this);
    }
    
    public function testOnlyActiveVacanciesListed(){
        $addVacancy = new AddVacancy($this);
        $fixture = sfConfig::get('sf_plugins_dir') . "/orangehrmRecruitmentPlugin/test/fixtures/AddVacancyTest.yml";
        $inputData = RecruitmentHelper::loadFixtureToInputArray($fixture, "OnlyActivevacanciesListed", $addVacancy);
        $employeeInformation = Helper::login($this, 'admin', 'admin');
        $i=0;
        foreach($inputData as $record){
            $addVacancy = Menu::goToAddVacancy($this);
            $addVacancy->saveVacancy($record);
            $addCandidate = Menu::goToAddCandidate($this);
            $jobVacancyLabels = $this->getSelectOptions($addCandidate->cmbJobVacancy);
            //print_r($jobVacancyLabels);
            //print_r($record[$addVacancy->txtVacancyName]) ;
            $searchFound = array_search($record[$addVacancy->txtVacancyName], $jobVacancyLabels);
            $this->assertFalse($searchFound, "Job Vacancy drop down contains inactive vacancies");
            //sleep(10);
            $i++;
        }
        
        
        Helper::logOutIfLoggedIn($this);
    }
     
    
    
}
?>
