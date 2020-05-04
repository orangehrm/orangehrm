<?php
/*
 * Author: Gayanath Jayarathne
 * 2011Aug11Thu, 2011Aug18Thu, 2011Aug19Fri
 */

require_once "../../../lib/confs/Conf.php";

/* Setting records limit */

$recordsLimit = 5000;

if (isset($_GET['limit'])) {

    $recordsLimit = $_GET['limit'];

    if ($recordsLimit <1 || $recordsLimit > 5000) {
        $recordsLimit = 5000;
    }

}

/* Starting a MySQL connection */
$conf = new Conf();
$dbConnection = mysqli_connect($conf->dbhost, $conf->dbuser, $conf->dbpass, $conf->dbname, $conf->dbport);

if (!$dbConnection) {
    die(mysqli_connect_error());
}
$dbConnection->set_charset("utf8mb4");

mysqli_query($dbConnection, "SET foreign_key_checks = 0");

/* Turning off auto-commit */
mysqli_autocommit($dbConnection, false);

/* Truncating tables */
truncateTables($dbConnection);

/* Adding job titles */
addJobTitles($dbConnection);

/* Adding job vacancies */
addJobVacancies($dbConnection);

/* Adding Candidates */
addCandidates($dbConnection, $recordsLimit);

/* Committing results */
mysqli_commit($dbConnection);

mysqli_query($dbConnection, "SET foreign_key_checks = 1");

if (!mysqli_error($dbConnection)) {
    echo "Done!<br><br>\n";
    echo "$recordsLimit candidates were added.<br><br>\n";
}

/* Closing the MySQL connection */
mysqli_close($dbConnection);

/* ======================================== */

/* Script Functions */

function display($value) {
    //echo $value . " <br><br>\n";
//    $logHandle = fopen('log.txt', 'w');
//    fwrite($logHandle, $value . "\n\r\n\r");
//    fclose($logHandle);
//    file_put_contents('log.txt', $value . "\n\r\n\r", FILE_APPEND);
}

function checkQueryResult($result, $dbConnection) {

    if ($result !== true) {
        echo mysqli_error($dbConnection) . " <br><br>\n";
        mysqli_rollback($dbConnection);
    }

}

function truncateTables($dbConnection) {

    $q = "TRUNCATE TABLE `ohrm_job_candidate_history`";
    display($q);
    $result = mysqli_query($dbConnection, $q);
    checkQueryResult($result, $dbConnection);
    
    $q = "TRUNCATE TABLE `ohrm_job_candidate_attachment`";
    display($q);
    $result = mysqli_query($dbConnection, $q);
    checkQueryResult($result, $dbConnection);    
    
    $q = "TRUNCATE TABLE `ohrm_job_candidate_vacancy`";
    display($q);
    $result = mysqli_query($dbConnection, $q);
    checkQueryResult($result, $dbConnection);    
    
    $q = "TRUNCATE TABLE `ohrm_job_candidate`";
    display($q);
    $result = mysqli_query($dbConnection, $q);
    checkQueryResult($result, $dbConnection);    
    
    $q = "TRUNCATE TABLE `ohrm_job_vacancy`";
    display($q);
    $result = mysqli_query($dbConnection, $q);
    checkQueryResult($result, $dbConnection);

    $q = "TRUNCATE TABLE `ohrm_job_title`";
    display($q);
    $result = mysqli_query($dbConnection, $q);
    checkQueryResult($result, $dbConnection);    
 
}

function addJobTitles($dbConnection) {
    
    $q = "INSERT INTO `ohrm_job_title` VALUES 
                                        ('1', 'Computer Information Systems Manager', 'CISM', 'CISM', 0),
                                        ('2', 'Computer Programmer', 'CP', 'CP', 0),
                                        ('3', 'Computer Security Specialist', 'CSS', 'CSS', 0),
                                        ('4', 'Database Administrator', 'DBA', 'DBA', 0),
                                        ('5', 'Game Designer', 'GD', 'GD', 1)
                                        ";
    
    $result = mysqli_query($dbConnection, $q);
    checkQueryResult($result, $dbConnection);
    
    $q = "UPDATE `hs_hr_unique_id` SET `last_id` = 5 WHERE `table_name` = 'ohrm_job_title'";
    display($q);
    $result = mysqli_query($dbConnection, $q);
    checkQueryResult($result, $dbConnection);  
    
}

function addJobVacancies($dbConnection) {
    
    $name1 = "Computer Information Systems Manager " . date('Y');
    $name2 = "Computer Programmer " . date('Y');
    $name3 = "Computer Security Specialist " . date('Y');
    $name4 = "Database Administrator " . date('Y');
    $name5 = "Game Designer " . date('Y');

    $vd1 = mysqli_real_escape_string($dbConnection, file_get_contents('job-description-1.txt'));
    $vd2 = mysqli_real_escape_string($dbConnection, file_get_contents('job-description-2.txt'));
    $vd3 = mysqli_real_escape_string($dbConnection, file_get_contents('job-description-3.txt'));
    $vd4 = mysqli_real_escape_string($dbConnection, file_get_contents('job-description-4.txt'));
    $vd5 = mysqli_real_escape_string($dbConnection, file_get_contents('job-description-5.txt'));
    
    $dateTime1 = date('Y-m-d H:i:s', strtotime("-10 days"));
    $dateTime2 = date('Y-m-d H:i:s', strtotime("-9 days"));
    $dateTime3 = date('Y-m-d H:i:s', strtotime("-8 days"));
    $dateTime4 = date('Y-m-d H:i:s', strtotime("-7 days"));
    $dateTime5 = date('Y-m-d H:i:s', strtotime("-6 days"));

    $q = "INSERT INTO `ohrm_job_vacancy` (`id`, `job_title_code`, `hiring_manager_id`, `name`, `description`, `no_of_positions`,
                                          `status`, `published_in_feed`, `defined_time`, `updated_time`) VALUES 
                                          (1, '1', '1', '$name1', '$vd1', 1, 1, 1, '$dateTime1', '$dateTime1'),
                                          (2, '2', '2', '$name2', '$vd2', 2, 1, 1, '$dateTime2', '$dateTime2'),
                                          (3, '3', '3', '$name3', '$vd3', 3, 1, 1, '$dateTime3', '$dateTime3'),
                                          (4, '4', '4', '$name4', '$vd4', 4, 1, 1, '$dateTime4', '$dateTime4'),
                                          (5, '5', '5', '$name5', '$vd5', 5, 1, 1, '$dateTime5', '$dateTime5')";

    display($q);
    $result = mysqli_query($dbConnection, $q);
    checkQueryResult($result, $dbConnection);
    
    $q = "UPDATE `hs_hr_unique_id` SET `last_id` = 5 WHERE `table_name` = 'ohrm_job_vacancy'";
    display($q);
    $result = mysqli_query($dbConnection, $q);
    checkQueryResult($result, $dbConnection);   
    
//    $jobFeedService = new JobFeedService();
//    $jobFeedService->updateJobFeed();
    
}

function addCandidates($dbConnection, $recordsLimit) {

    $candidateFile = file('canidate-name-list.txt');
    $recordLimit = $recordsLimit + 1;
    $vacancyId = 1;
    $dateI = -1;
    $appliedDate = date('Y-m-d');
    $vacancyNames = array('Computer Information Systems Manager',
                          'Computer Programmer',
                          'Computer Security Specialist',
                          'Database Administrator',
                          'Game Designer');

    $candidateQ = "INSERT INTO `ohrm_job_candidate` (`id`, `first_name`, `middle_name`, `last_name`, 
                                            `email`, `contact_number`, `status`, `comment`, 
                                            `mode_of_application`, `date_of_application`, `cv_file_id`, 
                                            `cv_text_version`, `keywords`, `added_person`) VALUES";
 
    $cvQ = "INSERT INTO `ohrm_job_candidate_attachment` VALUES";    
    
    $candidateVacancyQ = "INSERT INTO `ohrm_job_candidate_vacancy` VALUES";
    
    $candidateHistoryQ = "INSERT INTO `ohrm_job_candidate_history` VALUES";

    for ($i=1; $i<$recordLimit; $i++) {
        
        $vacancyName = $vacancyNames[$vacancyId-1];
        $appliedDate = date('Y-m-d', strtotime("$dateI day"));
        $note = file_get_contents("job-description-$vacancyId.txt");

        $candidateQ .= getCandidateQPartial($i, $recordLimit, $candidateFile[$i-1], $appliedDate);
        $cvQ .= getCvQPartial($i, $recordLimit);
        $candidateVacancyQ .= getCandidateVacancyQPartial($i, $recordLimit, $vacancyId, $appliedDate);
        $candidateHistoryQ .= getCandidateHistoryQPartial($i, $recordLimit, $vacancyId, $vacancyName, $appliedDate, $dbConnection, $note);

        if ($vacancyId < 5) {
            $vacancyId++;
        } else {
            $vacancyId = 1;
        }

        if ($dateI > -5) {
            $dateI--;
        } else {
            $dateI = -1;
        }

    }
    
    /* Adding candidate details */
    display($candidateQ);
    $result = mysqli_query($dbConnection, $candidateQ);
    checkQueryResult($result, $dbConnection);    
    
    $q = "UPDATE `hs_hr_unique_id` SET `last_id` = 5000 WHERE `table_name` = 'ohrm_job_candidate'";
    display($q);
    $result = mysqli_query($dbConnection, $q);
    checkQueryResult($result, $dbConnection);
    
    /* Adding candidate CV details */
//    $result = mysqli_query($dbConnection, $cvQ);
//    checkQueryResult($result, $dbConnection);
//
//    $q = "UPDATE `hs_hr_unique_id` SET `last_id` = 5000 WHERE `table_name` = 'ohrm_job_candidate_attachment'";
//    $result = mysqli_query($dbConnection, $q);
//    checkQueryResult($result, $dbConnection);
    
    /* Adding candidate vacancy details */
    display($candidateVacancyQ);
    $result = mysqli_query($dbConnection, $candidateVacancyQ);
    checkQueryResult($result, $dbConnection);    
    
    $q = "UPDATE `hs_hr_unique_id` SET `last_id` = 5000 WHERE `table_name` = 'ohrm_job_candidate_vacancy'";
    display($q);
    $result = mysqli_query($dbConnection, $q);
    checkQueryResult($result, $dbConnection);    
    
    /* Adding candidate history details */
    display($candidateHistoryQ);
    $result = mysqli_query($dbConnection, $candidateHistoryQ);
    checkQueryResult($result, $dbConnection);    
    
    $q = "UPDATE `hs_hr_unique_id` SET `last_id` = 5000 WHERE `table_name` = 'ohrm_job_candidate_history'";
    display($q);
    $result = mysqli_query($dbConnection, $q);
    checkQueryResult($result, $dbConnection);    

}

function getCandidateQPartial($i, $recordLimit, $nameString, $appliedDate) {
   
    $nameItems = explode(" ", trim($nameString));    

    $id                     = $i;
    $first_name             = $nameItems[0];
    $middle_name            = "";
    $last_name              = $nameItems[0];
    $email                  = "$first_name.$last_name@example.com";
    $contact_number         = "+12-12569-" . str_pad($i, 5, "0", STR_PAD_LEFT);
    $status                 = 1;
    $comment                = "Candidate $id";
    $mode_of_application    = 2; // (($i%2)==0)?1:2;
    $date_of_application    = $appliedDate;
    $cv_file_id             = $i;
    $cv_text_version        = "";
    $keywords               = "PHP, jQuery, MySQL, SVN";
    $added_person           = "";  

    $q = " ($id, '$first_name', '$middle_name', '$last_name', 
            '$email', '$contact_number', $status, '$comment', 
            $mode_of_application, '$date_of_application', NULL,
            '$cv_text_version', '$keywords', NULL)";

    if ($i < ($recordLimit - 1)) {
        $q .= ",";
    }

    return $q;
    
}

function getCvQPartial($i, $recordLimit) {
    
    $fileName = "test-cv.pdf";
    $fileType = "application/pdf";
    $fileSize = 10658;
    $fileContent = file_get_contents("test-cv.pdf");
    
    $q = " ($i, $i, '$fileName', '$fileType', $fileSize, '$fileContent', NULL)";
    
    if ($i < ($recordLimit - 1)) {
        $q .= ",";
    }
    
    return $q;
    
}

function getCandidateVacancyQPartial($i, $recordLimit, $vacancyId, $appliedDate) {
    
    $status = "APPLICATION INITIATED";
   
    $q = " ($i, $i, $vacancyId, '$status', '$appliedDate')";

    if ($i < ($recordLimit - 1)) {
        $q .= ",";
    }

    return $q;
    
}

function getCandidateHistoryQPartial($i, $recordLimit, $vacancyId, $vacancyName, $performedDate, $dbConnection, $note) {
    
    $note = mysqli_real_escape_string($dbConnection, $note);
    
    $q = " ($i, $i, $vacancyId, '$vacancyName', NULL, 16, NULL, '$performedDate', '$note', NULL)";

    if ($i < ($recordLimit - 1)) {
        $q .= ",";
    }

    return $q;   
    
}


?>
