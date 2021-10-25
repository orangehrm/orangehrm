<?php

namespace ZxcvbnPhp;

interface ScorerInterface
{

    /**
     * Score for a password's bits of entropy.
     *
     * @param float $entropy
     *   Entropy to score.
     * @return float
     *   Score.
     */
    public function score($entropy);

    /**
     * Get metrics used to determine score.
     *
     * @return array
     *   Key value array of metrics.
     */
    public function getMetrics();
}