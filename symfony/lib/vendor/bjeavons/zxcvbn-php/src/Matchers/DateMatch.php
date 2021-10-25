<?php

namespace ZxcvbnPhp\Matchers;

class DateMatch extends Match
{

    const NUM_YEARS = 119; // Years match against 1900 - 2019
    const NUM_MONTHS = 12;
    const NUM_DAYS = 31;

    const DATE_RX_YEAR_SUFFIX = '/(\d{1,2})(\s|-|\/|\\|_|\.)(\d{1,2})\2(19\d{2}|200\d|201\d|\d{2})/';
    const DATE_RX_YEAR_PREFIX = '/(19\d{2}|200\d|201\d|\d{2})(\s|-|\/|\\|_|\.)(\d{1,2})\2(\d{1,2})/';

    /**
     * @var
     */
    public $day;

    /**
     * @var
     */
    public $month;

    /**
     * @var
     */
    public $year;

    /**
     * @var
     */
    public $separator;

    /**
     * Match occurences of dates in a password
     *
     * @copydoc Match::match()
     */
    public static function match($password, array $userInputs = array())
    {
        $matches = array();
        $dates = static::datesWithoutSeparators($password) + static::datesWithSeparators($password);
        foreach ($dates as $date) {
            $matches[] = new static($password, $date['begin'], $date['end'], $date['token'], $date);
        }
        return $matches;
    }

    /**
     * @param $password
     * @param $begin
     * @param $end
     * @param $token
     * @param array $params
     *   Array with keys: day, month, year, separator.
     */
    public function __construct($password, $begin, $end, $token, $params)
    {
        parent::__construct($password, $begin, $end, $token);
        $this->pattern = 'date';
        $this->day = $params['day'];
        $this->month = $params['month'];
        $this->year = $params['year'];
        $this->separator = $params['separator'];
    }

    /**
     * Get match entropy.
     *
     * @return float
     */
    public function getEntropy()
    {
        if ($this->year < 100) {
            // two-digit year
            $entropy = $this->log(self::NUM_DAYS * self::NUM_MONTHS * 100);
        }
        else {
            // four-digit year
            $entropy = $this->log(self::NUM_DAYS * self::NUM_MONTHS * self::NUM_YEARS);
        }
        // add two bits for separator selection [/,-,.,etc]
        if (!empty($this->separator)) {
            $entropy += 2;
        }
        return $entropy;
    }

    /**
     * Find dates with separators in a password.
     *
     * @param string $password
     * @return array
     */
    protected static function datesWithSeparators($password)
    {
        $dates = array();
        foreach (static::findAll($password, static::DATE_RX_YEAR_SUFFIX) as $captures) {
            $date = array(
                'day'   => (integer) $captures[1]['token'],
                'month' => (integer) $captures[3]['token'],
                'year'  => (integer) $captures[4]['token'],
                'sep'   => $captures[2]['token'],
                'begin' => $captures[0]['begin'],
                'end'   => $captures[0]['end'],
            );
            $dates[] = $date;
        }
        foreach (static::findAll($password, static::DATE_RX_YEAR_PREFIX) as $captures) {
            $date = array(
                'day'   => (integer) $captures[4]['token'],
                'month' => (integer) $captures[3]['token'],
                'year'  => (integer) $captures[1]['token'],
                'sep'   => $captures[2]['token'],
                'begin' => $captures[0]['begin'],
                'end'   => $captures[0]['end'],
            );
            $dates[] = $date;
        }

        $results = array();
        foreach ($dates as $candidate) {
            $date = static::checkDate($candidate['day'], $candidate['month'], $candidate['year']);

            if ($date === false) {
                continue;
            }
            list($day, $month, $year) = $date;

            $results[] = array(
                'pattern' => 'date',
                'begin' => $candidate['begin'],
                'end' => $candidate['end'],
                'token' => substr($password, $candidate['begin'], $candidate['begin'] + $candidate['end'] - 1),
                'separator' => $candidate['sep'],
                'day' => $day,
                'month' => $month,
                'year' => $year
            );
        }

        return $results;
    }

    /**
     * Find dates without separators in a password.
     *
     * @param string $password
     * @return array
     */
    protected static function datesWithoutSeparators($password)
    {
        $dateMatches = array();

        // 1197 is length-4, 01011997 is length 8
        foreach (static::findAll($password, '/(\d{4,8})/') as $captures) {
            $capture = $captures[1];
            $begin = $capture['begin'];
            $end = $capture['end'];

            $token = $capture['token'];
            $tokenLen = strlen($token);

            // Create year candidates.
            $candidates1 = array();
            if ($tokenLen <= 6) {
                // 2 digit year prefix (990112)
                $candidates1[] = array(
                    'daymonth' => substr($token, 2),
                    'year' => substr($token, 0, 2),
                    'begin' => $begin,
                    'end' => $end
                );
                // 2 digit year suffix (011299)
                $candidates1[] = array(
                    'daymonth' => substr($token, 0, ($tokenLen - 2)),
                    'year' => substr($token, -2),
                    'begin' => $begin,
                    'end' => $end
                );
            }
            if ($tokenLen >= 6) {
                // 4 digit year prefix (199912)
                $candidates1[] = array(
                    'daymonth' => substr($token, 4),
                    'year' => substr($token, 0, 4),
                    'begin' => $begin,
                    'end' => $end
                );
                // 4 digit year suffix (121999)
                $candidates1[] = array(
                    'daymonth' => substr($token, 0, ($tokenLen - 4)),
                    'year' => substr($token, -4),
                    'begin' => $begin,
                    'end' => $end
                );
            }
            // Create day/month candidates from years.
            $candidates2 = array();
            foreach ($candidates1 as $candidate) {
                switch (strlen($candidate['daymonth'])) {
                    case 2: // ex. 1 1 97
                        $candidates2[] = array(
                            'day' => $candidate['daymonth'][0],
                            'month' => $candidate['daymonth'][1],
                            'year' => $candidate['year'],
                            'begin' => $candidate['begin'],
                            'end' => $candidate['end']
                        );
                        break;
                    case 3: // ex. 11 1 97 or 1 11 97
                        $candidates2[] = array(
                            'day' => substr($candidate['daymonth'], 0, 2),
                            'month' => substr($candidate['daymonth'], 2),
                            'year' => $candidate['year'],
                            'begin' => $candidate['begin'],
                            'end' => $candidate['end']
                        );
                        $candidates2[] = array(
                            'day' => substr($candidate['daymonth'], 0, 1),
                            'month' => substr($candidate['daymonth'], 1, 3),
                            'year' => $candidate['year'],
                            'begin' => $candidate['begin'],
                            'end' => $candidate['end']
                        );
                        break;
                    case 4: // ex. 11 11 97
                        $candidates2[] = array(
                            'day' => substr($candidate['daymonth'], 0, 2),
                            'month' => substr($candidate['daymonth'], 2, 4),
                            'year' => $candidate['year'],
                            'begin' => $candidate['begin'],
                            'end' => $candidate['end']
                        );
                        break;
                }
            }
            // Reject invalid candidates
            foreach ($candidates2 as $candidate) {
                $day = (integer) $candidate['day'];
                $month = (integer) $candidate['month'];
                $year = (integer) $candidate['year'];

                $date = static::checkDate($day, $month, $year);
                if ($date === false) {
                    continue;
                }
                list($day, $month, $year) = $date;

                $dateMatches[] = array(
                    'begin' => $candidate['begin'],
                    'end' => $candidate['end'],
                    'token' => substr($password, $begin, $begin + $end - 1),
                    'separator' => '',
                    'day' => $day,
                    'month' => $month,
                    'year' => $year
                );
            }
        }
        return $dateMatches;
    }

    /**
     * Validate date.
     *
     * @param int $day
     * @param int $month
     * @param int $year
     *
     * @return array|false
     */
    protected static function checkDate($day, $month, $year)
    {
        // Tolerate both day-month and month-day order
        if ((12 <= $month && $month <= 31) && $day <= 12) {
            $m = $month;
            $month = $day;
            $day = $m;
        }
        if ($day > 31 || $month > 12) {
            return false;
        }
        if (!((1900 <= $year && $year <= 2019))) {
            return false;
        }
        return array($day, $month, $year);
    }
}