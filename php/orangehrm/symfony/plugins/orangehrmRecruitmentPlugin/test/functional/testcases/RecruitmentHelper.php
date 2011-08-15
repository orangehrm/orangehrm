<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class RecruitmentHelper {
    
    public static $isPrerequisitesLoaded = false;
    
    
    public static function loadRecruitmentPrerequisites($selenium){

        if (!self::$isPrerequisitesLoaded){
            Helper::deleteAllFromTable("ohrm_job_candidate");
            if(self::addPrerequisiteVacancies($selenium))
                self::$isPrerequisitesLoaded = true;
        }      
    }

    public static function addPrerequisiteVacancies($selenium){
        return self::createPrerequisiteVacancies($selenium);
      }

    public static function loadFixtureToInputArray($fixturePath, $section, $pageobject) {
        $mapper = self::getFixtureToInputArrayMapping($pageobject);
        //echo "printing mapper "; print_r($mapper);
        $loadedFixture = sfYaml::load($fixturePath);

        if (count($mapper['fixture']) != count($mapper['inputData'])) {
            echo "number of fixture fields and number of inputData fields are different \n";
            echo "fixture count: " .count($mapper['fixture']) . "\t inputData count: ". count($mapper['inputData']) ;
            exit();
        }

        $recordNumber = 0;
        foreach ($loadedFixture[$section] as $record) {
            for ($columnNumber = 0; $columnNumber < count($mapper['inputData']); $columnNumber++) {
                
                $inputData[$recordNumber][$mapper['inputData'][$columnNumber]] = $record[$mapper['fixture'][$columnNumber]];
            }
            $recordNumber++;
        }
        //print_r($inputData);
        return $inputData;
    }

    public static function createPrerequisiteVacancies($selenium) {
        $fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmRecruitmentPlugin/test/fixtures/AddVacancyPrerequisites.yml';
        TestDataService::populate($fixture);
        $fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmRecruitmentPlugin/test/fixtures/AddVacancyTest.yml';
        $addVacancy = new AddVacancy($selenium);
        $inputData = self::loadFixtureToInputArray($fixture, "ValidVacancies", $addVacancy);
        
        Helper::logOutIfLoggedIn($selenium);
        Helper::login($selenium, "admin", "admin");
        foreach ($inputData as $record) {
            try{
                $addVacancy = Menu::goToAddVacancy($selenium);
                $addVacancy->saveVacancy($record);
                if ("Job Vacancy Saved Successfully" != $addVacancy->getSavedSuccessfullyMessage()) {
                    echo "\n the following vacancy was not created successfully";
                    print_r($record);
                    exit(-1);
                }
            }catch(Exception $e){
                echo " Error while creating vacancy of the record " ; print_r($record);
                exit(-1);
            }
            
            
        }
        Helper::logOutIfLoggedIn($selenium);
        return true;
    }

    public static function getFixtureToInputArrayMapping($pageobject) {

       $fixtureFields=null;
       $inputData=null;

        if ($pageobject instanceof AddVacancy) {
            $fixtureFields = array('jobTitle', 'vacancyName', 'hiringManager', 'numberOfPositions', 'description', 'active');
            $inputData = array($pageobject->cmbJobTitle, $pageobject->txtVacancyName, $pageobject->txtHiringManager, $pageobject->txtNoOfPositions, $pageobject->txtDesc, $pageobject->chkActive);            
            
        }
        if ($pageobject instanceof AddCandidate){
            $fixtureFields = array('firstName', 'middleName', 'lastName', 'contactNumber', 'keywords', 'dateOfApplication', 'email', 'comment', 'vacancy', 'resume');
            $inputData = array($pageobject->txtFirstName, $pageobject->txtMiddleName, $pageobject->txtLastName, $pageobject->txtContactNo, $pageobject->txtKeywords, $pageobject->txtDateOfApplication,
                $pageobject->txtEmail, $pageobject->txtComment, $pageobject->cmbJobVacancy, $pageobject->txtresume);
        }

        $mapper['fixture'] = $fixtureFields;
        $mapper['inputData'] = $inputData;
        //print_r($mapper);
        return $mapper;

    }

}
?>
