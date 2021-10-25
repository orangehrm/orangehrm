<?php

namespace ZxcvbnPhp;

class Scorer implements ScorerInterface
{

    const SINGLE_GUESS = 0.010; // Lower bound assumption of time to hash based on bcrypt/scrypt/PBKDF2.
    const NUM_ATTACKERS = 100; // Assumed number of cores guessing in parallel.

    protected $crackTime;

    /**
     *
     */
    public function score($entropy)
    {
        $seconds = $this->calcCrackTime($entropy);

        if ($seconds < pow(10, 2)) {
            return 0;
        }
        if ($seconds < pow(10, 4)) {
            return 1;
        }
        if ($seconds < pow(10, 6)) {
            return 2;
        }
        if ($seconds < pow(10, 8)) {
            return 3;
        }
        return 4;
    }

    /**
     *
     */
    public function getMetrics()
    {
        return array(
            'crack_time' => $this->crackTime
        );
    }

    /**
     * Get average time to crack based on entropy.
     *
     * @param $entropy
     * @return float
     */
    protected function calcCrackTime($entropy)
    {
        $this->crackTime = (0.5 * pow(2, $entropy)) * (Scorer::SINGLE_GUESS / Scorer::NUM_ATTACKERS);
        return $this->crackTime;
    }


}