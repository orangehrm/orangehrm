<?php
/*
 * Author: Gayanath Jayarathne
 * 2011Aug11Thu, 2011Aug18Thu, 2011Aug19Fri
 */

$conf = null;
$confPaths = [
    __DIR__ . '/../../../lib/confs/Conf.php',
    '/var/www/html/lib/confs/Conf.php',
];
foreach ($confPaths as $confPath) {
    if (is_file($confPath)) {
        require_once $confPath;
        if (class_exists('Conf')) {
            $conf = new Conf();
        }
        break;
    }
}

if (!$conf instanceof Conf) {
    $conf = new stdClass();
    $conf->dbhost = 'mysql';
    $conf->dbuser = 'orangehrm';
    $conf->dbpass = 'orangehrm';
    $conf->dbname = 'orangehrm';
    $conf->dbport = 3306;
}

if (getenv('DB_HOST') !== false) {
    $conf->dbhost = getenv('DB_HOST');
}
if (getenv('DB_USER') !== false) {
    $conf->dbuser = getenv('DB_USER');
}
if (getenv('DB_PASS') !== false) {
    $conf->dbpass = getenv('DB_PASS');
}
if (getenv('DB_NAME') !== false) {
    $conf->dbname = getenv('DB_NAME');
}
if (getenv('DB_PORT') !== false) {
    $conf->dbport = (int) getenv('DB_PORT');
}

/* Setting records limit */

$recordsLimit = 5000;

if (isset($_GET['limit'])) {
    $candidateLimit = (int) $_GET['limit'];
    if ($candidateLimit >= 1 && $candidateLimit <= 5000) {
        $recordsLimit = $candidateLimit;
    }
}
$mysqliAvailable = function_exists('mysqli_connect');
$pdoTransactionActive = false;

if ($mysqliAvailable) {
    $dbConnection = mysqli_connect($conf->dbhost, $conf->dbuser, $conf->dbpass, $conf->dbname, $conf->dbport);
    if (!$dbConnection) {
        fwrite(STDERR, mysqli_connect_error() . PHP_EOL);
        exit(1);
    }
    mysqli_set_charset($dbConnection, 'utf8mb4');
} else {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $conf->dbhost, $conf->dbport, $conf->dbname);
    try {
        $dbConnection = new PDO($dsn, $conf->dbuser, $conf->dbpass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        fwrite(STDERR, $e->getMessage() . PHP_EOL);
        exit(1);
    }
}

$runQuery = function (string $sql) use (&$dbConnection, $mysqliAvailable) {
    if ($mysqliAvailable) {
        $result = mysqli_query($dbConnection, $sql);
        if ($result === false) {
            throw new RuntimeException(mysqli_error($dbConnection));
        }
        return $result;
    }
    return $dbConnection->exec($sql);
};

$escapeValue = function (string $value) use (&$dbConnection, $mysqliAvailable) {
    if ($mysqliAvailable) {
        return mysqli_real_escape_string($dbConnection, $value);
    }
    // Strip surrounding quotes applied by PDO::quote
    return substr($dbConnection->quote($value), 1, -1);
};

$fetchAll = function (string $sql) use (&$dbConnection, $mysqliAvailable) {
    if ($mysqliAvailable) {
        $result = mysqli_query($dbConnection, $sql);
        if ($result === false) {
            throw new RuntimeException(mysqli_error($dbConnection));
        }
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_free_result($result);
        return $rows;
    }
    $statement = $dbConnection->query($sql);
    if ($statement === false) {
        throw new RuntimeException('Failed to execute query');
    }
    return $statement->fetchAll();
};

$fetchValue = function (string $sql) use (&$fetchAll) {
    $rows = $fetchAll($sql);
    if (!empty($rows)) {
        $row = reset($rows);
        return reset($row);
    }
    return null;
};

$vacancyJobTitleColumn = 'job_title_id';
try {
    $fetchAll("SELECT job_title_id FROM `ohrm_job_vacancy` LIMIT 1");
} catch (Throwable $columnCheckException) {
    $vacancyJobTitleColumn = 'job_title_code';
    try {
        $fetchAll("SELECT job_title_code FROM `ohrm_job_vacancy` LIMIT 1");
    } catch (Throwable $fallbackException) {
        throw new RuntimeException('Unable to determine job title column for vacancies. Neither job_title_id nor job_title_code exists.');
    }
}

try {
    $runQuery("SET foreign_key_checks = 0");

    if ($mysqliAvailable) {
        mysqli_autocommit($dbConnection, false);
    } elseif ($dbConnection instanceof PDO) {
        $dbConnection->beginTransaction();
        $pdoTransactionActive = true;
    }

    /* Adding job titles */
    addJobTitles($dbConnection);

    /* Adding job vacancies */
    addJobVacancies($dbConnection);

    /* Adding Candidates */
    $addedCount = addCandidates($dbConnection, $recordsLimit);

    if ($mysqliAvailable) {
        mysqli_commit($dbConnection);
    } elseif ($pdoTransactionActive && $dbConnection instanceof PDO) {
        $dbConnection->commit();
        $pdoTransactionActive = false;
    }

    $runQuery("SET foreign_key_checks = 1");

    if (PHP_SAPI === 'cli') {
        echo "Done!" . PHP_EOL;
        echo $addedCount . " new candidates were added (requested $recordsLimit)." . PHP_EOL;
    } else {
        echo "Done!<br><br>\n";
        echo "$addedCount new candidates added (target: $recordsLimit).<br><br>\n";
    }
} catch (Throwable $e) {
    if ($mysqliAvailable) {
        mysqli_rollback($dbConnection);
    } elseif ($pdoTransactionActive && $dbConnection instanceof PDO && $dbConnection->inTransaction()) {
        $dbConnection->rollBack();
        $pdoTransactionActive = false;
    }
    try {
        $runQuery("SET foreign_key_checks = 1");
    } catch (Throwable $inner) {
        // Suppress secondary errors while handling the primary failure.
    }
    $errorMessage = 'Error loading candidates: ' . $e->getMessage();
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, $errorMessage . PHP_EOL);
    } else {
        echo '<pre>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</pre>';
    }
    exit(1);
} finally {
    if ($mysqliAvailable && isset($dbConnection) && $dbConnection instanceof mysqli) {
        mysqli_close($dbConnection);
    }
}

/* ======================================== */

/* Script Functions */

function display($value) {
    //echo $value . " <br><br>\n";
//    $logHandle = fopen('log.txt', 'w');
//    fwrite($logHandle, $value . "\n\r\n\r");
//    fclose($logHandle);
//    file_put_contents('log.txt', $value . "\n\r\n\r", FILE_APPEND);
}

function addJobTitles($dbConnection) {
    global $runQuery;

    $q = "INSERT INTO `ohrm_job_title` (id, job_title, job_description, is_deleted) VALUES 
            (1, 'Computer Information Systems Manager', 'CISM', 0),
            (2, 'Computer Programmer', 'CP', 0),
            (3, 'Computer Security Specialist', 'CSS', 0),
            (4, 'Database Administrator', 'DBA', 0),
            (5, 'Game Designer', 'GD', 1)
        ON DUPLICATE KEY UPDATE
            job_title = VALUES(job_title),
            job_description = VALUES(job_description),
            is_deleted = VALUES(is_deleted)";
    $runQuery($q);

    $runQuery("UPDATE `hs_hr_unique_id`
        SET `last_id` = GREATEST(`last_id`, 5)
        WHERE `table_name` = 'ohrm_job_title'");
}

function addJobVacancies($dbConnection) {
    global $runQuery, $escapeValue, $vacancyJobTitleColumn;

    $name1 = "Computer Information Systems Manager " . date('Y');
    $name2 = "Computer Programmer " . date('Y');
    $name3 = "Computer Security Specialist " . date('Y');
    $name4 = "Database Administrator " . date('Y');
    $name5 = "Game Designer " . date('Y');

    $vd1 = $escapeValue(file_get_contents(__DIR__ . '/job-description-1.txt'));
    $vd2 = $escapeValue(file_get_contents(__DIR__ . '/job-description-2.txt'));
    $vd3 = $escapeValue(file_get_contents(__DIR__ . '/job-description-3.txt'));
    $vd4 = $escapeValue(file_get_contents(__DIR__ . '/job-description-4.txt'));
    $vd5 = $escapeValue(file_get_contents(__DIR__ . '/job-description-5.txt'));

    $dateTime1 = date('Y-m-d H:i:s', strtotime("-10 days"));
    $dateTime2 = date('Y-m-d H:i:s', strtotime("-9 days"));
    $dateTime3 = date('Y-m-d H:i:s', strtotime("-8 days"));
    $dateTime4 = date('Y-m-d H:i:s', strtotime("-7 days"));
    $dateTime5 = date('Y-m-d H:i:s', strtotime("-6 days"));

    $q = "INSERT INTO `ohrm_job_vacancy`
            (`id`, `" . $vacancyJobTitleColumn . "`, `hiring_manager_id`, `name`, `description`, `no_of_positions`,
             `status`, `published_in_feed`, `defined_time`, `updated_time`)
          VALUES
            (1, '1', '1', '$name1', '$vd1', 1, 1, 1, '$dateTime1', '$dateTime1'),
            (2, '2', '2', '$name2', '$vd2', 2, 1, 1, '$dateTime2', '$dateTime2'),
            (3, '3', '3', '$name3', '$vd3', 3, 1, 1, '$dateTime3', '$dateTime3'),
            (4, '4', '4', '$name4', '$vd4', 4, 1, 1, '$dateTime4', '$dateTime4'),
            (5, '5', '5', '$name5', '$vd5', 5, 1, 1, '$dateTime5', '$dateTime5')
          ON DUPLICATE KEY UPDATE
            `" . $vacancyJobTitleColumn . "` = VALUES(`" . $vacancyJobTitleColumn . "`),
            hiring_manager_id = VALUES(hiring_manager_id),
            name = VALUES(name),
            description = VALUES(description),
            no_of_positions = VALUES(no_of_positions),
            status = VALUES(status),
            published_in_feed = VALUES(published_in_feed),
            defined_time = VALUES(defined_time),
            updated_time = VALUES(updated_time)";

    display($q);
    $runQuery($q);

    $runQuery("UPDATE `hs_hr_unique_id`
        SET `last_id` = GREATEST(`last_id`, 5)
        WHERE `table_name` = 'ohrm_job_vacancy'");

//    $jobFeedService = new JobFeedService();
//    $jobFeedService->updateJobFeed();

}

function addCandidates($dbConnection, $recordsLimit) {
    global $runQuery, $escapeValue, $fetchAll, $fetchValue;

    $candidateFile = array_values(array_filter(array_map('trim', file(__DIR__ . '/canidate-name-list.txt'))));
    if (empty($candidateFile)) {
        throw new RuntimeException('Candidate name list is empty.');
    }

    shuffle($candidateFile);

    $existingRows = $fetchAll("SELECT LOWER(CONCAT(first_name, ' ', last_name)) AS candidate_key FROM `ohrm_job_candidate`");
    $existingCandidates = [];
    foreach ($existingRows as $row) {
        if (isset($row['candidate_key'])) {
            $existingCandidates[$row['candidate_key']] = true;
        }
    }

    $nextCandidateId = (int) ($fetchValue('SELECT COALESCE(MAX(id), 0) FROM `ohrm_job_candidate`') ?? 0) + 1;
    $nextCandidateVacancyId = (int) ($fetchValue('SELECT COALESCE(MAX(id), 0) FROM `ohrm_job_candidate_vacancy`') ?? 0) + 1;
    $nextHistoryId = (int) ($fetchValue('SELECT COALESCE(MAX(id), 0) FROM `ohrm_job_candidate_history`') ?? 0) + 1;

    $candidateValues = [];
    $candidateVacancyValues = [];
    $candidateHistoryValues = [];

    $vacancyNames = [
        'Computer Information Systems Manager',
        'Computer Programmer',
        'Computer Security Specialist',
        'Database Administrator',
        'Game Designer',
    ];

    $vacancyId = 1;
    $dateOffset = -1;
    $inserted = 0;

    foreach ($candidateFile as $nameString) {
        if ($inserted >= $recordsLimit) {
            break;
        }

        $nameItems = preg_split('/\s+/', $nameString);
        $firstName = $nameItems[0] ?? '';
        if ($firstName === '') {
            continue;
        }

        $lastName = $nameItems[count($nameItems) - 1] ?? $firstName;

        $candidateKey = strtolower($firstName . ' ' . $lastName);
        if (isset($existingCandidates[$candidateKey])) {
            continue;
        }

        $inserted++;
        $existingCandidates[$candidateKey] = true;

        $candidateId = $nextCandidateId++;
        $candidateVacancyId = $nextCandidateVacancyId++;
        $candidateHistoryId = $nextHistoryId++;

        $vacancyName = $vacancyNames[$vacancyId - 1];
        $appliedDate = date('Y-m-d', strtotime("$dateOffset day"));
        $note = file_get_contents(__DIR__ . "/job-description-$vacancyId.txt");
        if ($note === false) {
            $note = '';
        }

        $email = strtolower($firstName . '.' . $lastName) . '@example.com';
        $contactNumber = '+12-12569-' . str_pad((string) $candidateId, 5, '0', STR_PAD_LEFT);
        $comment = 'Candidate ' . $candidateId;

        $candidateValues[] = sprintf(
            "(%d, '%s', '', '%s', '%s', '%s', 1, '%s', 2, '%s', NULL, '', 'PHP, jQuery, MySQL, SVN', NULL)",
            $candidateId,
            $escapeValue($firstName),
            $escapeValue($lastName),
            $escapeValue($email),
            $escapeValue($contactNumber),
            $escapeValue($comment),
            $escapeValue($appliedDate)
        );

        $candidateVacancyValues[] = sprintf(
            "(%d, %d, %d, 'APPLICATION INITIATED', '%s')",
            $candidateVacancyId,
            $candidateId,
            $vacancyId,
            $escapeValue($appliedDate)
        );

        $performedDateTime = $escapeValue($appliedDate . ' 00:00:00');

        $candidateHistoryValues[] = sprintf(
            "(%d, %d, %d, '%s', NULL, 16, NULL, '%s', '%s', NULL)",
            $candidateHistoryId,
            $candidateId,
            $vacancyId,
            $escapeValue($vacancyName),
            $performedDateTime,
            $escapeValue($note)
        );

        if ($vacancyId < 5) {
            $vacancyId++;
        } else {
            $vacancyId = 1;
        }

        if ($dateOffset > -5) {
            $dateOffset--;
        } else {
            $dateOffset = -1;
        }
    }

    if ($inserted === 0) {
        return 0;
    }

    $candidateInsert = 'INSERT INTO `ohrm_job_candidate` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `contact_number`, `status`, `comment`, `mode_of_application`, `date_of_application`, `cv_file_id`, `cv_text_version`, `keywords`, `added_person`) VALUES ' . implode(",\n", $candidateValues);
    display($candidateInsert);
    $runQuery($candidateInsert);

    $candidateVacancyInsert = 'INSERT INTO `ohrm_job_candidate_vacancy` (`id`, `candidate_id`, `vacancy_id`, `status`, `applied_date`) VALUES ' . implode(",\n", $candidateVacancyValues);
    display($candidateVacancyInsert);
    $runQuery($candidateVacancyInsert);

    $candidateHistoryInsert = 'INSERT INTO `ohrm_job_candidate_history` (`id`, `candidate_id`, `vacancy_id`, `candidate_vacancy_name`, `interview_id`, `action`, `performed_by`, `performed_date`, `note`, `interviewers`) VALUES ' . implode(",\n", $candidateHistoryValues);
    display($candidateHistoryInsert);
    $runQuery($candidateHistoryInsert);

    $maxCandidateId = $nextCandidateId - 1;
    $maxCandidateVacancyId = $nextCandidateVacancyId - 1;
    $maxHistoryId = $nextHistoryId - 1;

    $runQuery("UPDATE `hs_hr_unique_id`
        SET `last_id` = GREATEST(`last_id`, {$maxCandidateId})
        WHERE `table_name` = 'ohrm_job_candidate'");

    $runQuery("UPDATE `hs_hr_unique_id`
        SET `last_id` = GREATEST(`last_id`, {$maxCandidateVacancyId})
        WHERE `table_name` = 'ohrm_job_candidate_vacancy'");

    $runQuery("UPDATE `hs_hr_unique_id`
        SET `last_id` = GREATEST(`last_id`, {$maxHistoryId})
        WHERE `table_name` = 'ohrm_job_candidate_history'");

    return $inserted;
}

?>
