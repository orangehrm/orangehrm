<?php

//db credentials
$dbInfo['host'] = 'localhost';
$dbInfo['username'] = 'root';
$dbInfo['password'] = 'root123';
$dbInfo['database'] = 'orangehrm_3.1.3_upgrade';

global $dbConnection;
$dbConnection = createDbConnection($dbInfo['host'], $dbInfo['username'], $dbInfo['password'], $dbInfo['database']);

//upgrade
transferHrHSKpisToOhrmKpis();
transferReviewsToOhrm();
finalizeUpgrade();

//close connection
mysqli_close($dbConnection);

function createDbConnection($host, $username, $password, $dbname, $port = null) {
    if (!$port) {
        $dbConnection = mysqli_connect($host, $username, $password, $dbname);
    } else {
        $dbConnection = mysqli_connect($host, $username, $password, $dbname, $port);
    }

    if (!$dbConnection) {
        die('Could not connect: ' . mysqli_connect_error());
    }
    $dbConnection->set_charset("utf8");
    mysqli_autocommit($dbConnection, FALSE);
    return $dbConnection;
}

function executeSql($query) {
    global $dbConnection;
    echo ".";
    $result = mysqli_query($dbConnection, $query);
    if (mysqli_error($dbConnection)) {
        echo "\n" . $query . "::" . mysqli_error($dbConnection) . "\n";
    }
    return $result;
}

function escapeString($string) {
    global $dbConnection;
    return mysqli_real_escape_string($dbConnection, $string);
}

function transferHrHSKpisToOhrmKpis() {
    $sql = "SELECT * FROM `hs_hr_kpi`";
    $result = executeSql($sql);
    while ($row = mysqli_fetch_array($result)) {
        $kpiId = $row['id'];
        $jobTitleCode = $row['job_title_code'];
        $description = $row['description'];
        $rateMin = $row['rate_min'];
        $rateMax = $row['rate_max'];
        $rateDefault = $row['rate_default'];
        $isActive = $row['is_active'];

        if ($rateDefault != '1') {
            $rateDefault = 'NULL';
        }

        if ($isActive == '1') {
            $isActive = 'NULL';
        } else {
            $isActive = date('Y-m-d');
        }

        executeSql("INSERT INTO `ohrm_kpi`(`id`, `job_title_code`, `kpi_indicators`, `min_rating`, `max_rating`, `default_kpi`,`deleted_at`) VALUES "
                . "('" . escapeString($kpiId) . "', '" . escapeString($jobTitleCode) . "','" . escapeString($description) . "'," . escapeString($rateMin) . "," . escapeString($rateMax) . "," . escapeString($rateDefault) . ",'$isActive')");
    }
}

function getStateId($state) {
    switch ($state) {
        case 1:
            return 2;
            break;
        case 3:

            return 3;
            break;
        case 5:
            return 3;
            break;
        case 7:
            return 1;
            break;
        case 9:
            return 4;
            break;
        default:
            return 1;
            break;
    }
}

function getReviewerState($state) {
    switch ($state) {
        case 1:
            return 1;
            break;
        case 3:

            return 2;
            break;
        case 5:
            return 2;
            break;
        case 7:
            return 1;
            break;
        case 9:
            return 3;
            break;
        default:
            return 1;
            break;
    }
}

function transferKpisToReviewer($reviewId, $reviewerId, $state, $completedDate, $kpisXml) {
    $reviewerState = getReviewerState($state);
    executeSql("INSERT INTO `ohrm_reviewer`(`review_id`, `employee_number`, `status`, `reviewer_group_id`, `completed_date`, `comment`) VALUES "
            . "(" . escapeString($reviewId) . "," . escapeString($reviewerId) . "," . escapeString($reviewerState) . "," . escapeString('2') . "," . escapeString($completedDate) . "," . escapeString('NULL') . ")");
    $xmlDoc = new DOMDocument();
    $xmlDoc->loadXML($kpisXml);
    $x = $xmlDoc->documentElement;
    $kpis = array();
    $count = 0;
    if ($x->hasChildNodes()) {
        foreach ($x->childNodes as $item) {
            if ($item->hasChildNodes()) {
                foreach ($item->childNodes as $childItem) {
                    if ($childItem->hasChildNodes()) {
                        foreach ($childItem->childNodes as $grandChildItem) {
                            if ($grandChildItem->nodeName == 'id') {
                                $kpis[$count]['id'] = $grandChildItem->nodeValue;
                                $kpiId = $kpis[$count]['id'];
                            } else if ($grandChildItem->nodeName == 'desc') {
                                $kpis[$count]['description'] = $grandChildItem->nodeValue;
                            } else if ($grandChildItem->nodeName == 'rate') {
                                $kpis[$count]['rate'] = $grandChildItem->nodeValue;
                            } else if ($grandChildItem->nodeName == 'comment') {
                                $kpis[$count]['comment'] = trim($grandChildItem->nodeValue);
                            } else if ($grandChildItem->nodeName == 'min') {
                                $kpis[$count]['minrate'] = $grandChildItem->nodeValue;
                            } else if ($grandChildItem->nodeName == 'max') {
                                $kpis[$count]['maxrate'] = $grandChildItem->nodeValue;
                            }
                        }
                        $count++;
                    }
                }
            }
        }
    }
    $reviewerReviewId = executeSql("SELECT `id` FROM `ohrm_reviewer` WHERE `review_id`=$reviewId AND `employee_number`=$reviewerId LIMIT 1");
    if (mysqli_num_rows($reviewerReviewId) > 0) {
        foreach ($kpis as $kpi) {
            executeSql("INSERT INTO `ohrm_reviewer_rating`(`rating`, `kpi_id`, `review_id`, `reviewer_id`, `comment`) VALUES "
                    . "(".escapeString($kpi['rate']).",".escapeString($kpi['id']).",".escapeString($kpi['id']).",".escapeString($reviewId).",".escapeString($reviewerReviewId[0]['id']).",'".escapeString($kpi['comment'])."')");
        }
    }
}

function transferReviewsToOhrm() {
    $sql = "SELECT * FROM `hs_hr_performance_review`";
    $result = executeSql($sql);
    while ($row = mysqli_fetch_array($result)) {
        $reviewId = $row['id'];
        $employeeId = $row['employee_id'];
        $reviewerId = $row['reviewer_id'];
        $jobTitleCode = $row['job_title_code'];
        $subDivisionId = $row['sub_division_id'];
        $creationDate = $row['creation_date'];
        $periodFrom = $row['period_from'];
        $periodTo = $row['period_to'];
        $dueDate = $row['due_date'];
        $state = $row['state'];
        $kpis = $row['kpis'];

        $stateId = getStateId($state);

        if ($stateId == 4) {
            $completedDate = $dueDate;
        } else {
            $completedDate = 'NULL';
        }

        if ($stateId >= 2) {
            $activatedDate = $creationDate;
        } else {
            $activatedDate = 'NULL';
        }

        executeSql("INSERT INTO `ohrm_performance_review`(`id`, `status_id`, `employee_number`, `work_period_start`, `work_period_end`, `job_title_code`, `department_id`, `due_date`, `completed_date`, `activated_date`) VALUES"
                . "(" . escapeString($reviewId) . ", " . escapeString($stateId) . "," . escapeString($employeeId) . "," . escapeString($periodFrom) . "," . escapeString($periodTo) . "," . escapeString($jobTitleCode) . "," . escapeString($subDivisionId) . "," . escapeString($dueDate) . "," . escapeString($completedDate) . "," . escapeString($activatedDate) . ")");

        if ($stateId >= 2) {
            transferKpisToReviewer($reviewId, $reviewerId, $state, $completedDate, $kpis);
        }
    }
}

?>
