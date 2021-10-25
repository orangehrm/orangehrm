<?php

namespace ZxcvbnPhp;

use ZxcvbnPhp\Matchers\Bruteforce;

class Searcher
{

    /**
     * @var
     */
    public $matchSequence;

    /**
     * Calculate the minimum entropy for a password and its matches.
     *
     * @param string $password
     *   Password.
     * @param array $matches
     *   Array of Match objects on the password.
     *
     * @return float
     *   Minimum entropy for non-overlapping best matches of a password.
     */
    public function getMinimumEntropy($password, $matches)
    {
        $passwordLength = strlen($password);
        $entropyStack = array();
        // for the optimal sequence of matches up to k, holds the final match (match.end == k).
        // null means the sequence ends without a brute-force character.
        $backpointers = array();
        $bruteforceMatch = new Bruteforce($password, 0, $passwordLength - 1, $password);
        $charEntropy = log($bruteforceMatch->getCardinality(), 2);

        foreach (range(0, $passwordLength - 1) as $k) {
            // starting scenario to try and beat: adding a brute-force character to the minimum entropy sequence at k-1.
            $entropyStack[$k] = $this->prevValue($entropyStack, $k) + $charEntropy;
            $backpointers[$k] = null;
            foreach ($matches as $match) {
                if (!isset($match->begin) || $match->end != $k ) {
                    continue;
                }

                // See if entropy prior to match + entropy of this match is less than
                // the current minimum top of the stack.
                $candidateEntropy = $this->prevValue($entropyStack, $match->begin) + $match->getEntropy();
                if ($candidateEntropy <= $entropyStack[$k]) {
                    $entropyStack[$k] = $candidateEntropy;
                    $backpointers[$k] = $match;
                }
            }
        }

        // Walk backwards and decode the best sequence
        $matchSequence = array();
        $k = $passwordLength - 1;
        while ($k >= 0) {
            $match = $backpointers[$k];

            if ($match) {
                $matchSequence[] = $match;

                $k = $match->begin - 1;
            }
            else {
                $k -= 1;
            }
        }
        $matchSequence = array_reverse($matchSequence);

        $s = 0;
        $matchSequenceCopy = array();
        // Handle subtrings that weren't matched as bruteforce match.
        foreach ($matchSequence as $match) {
            if ($match->begin - $s > 0) {
                $matchSequenceCopy[] = $this->makeBruteforceMatch($password, $s, $match->begin - 1, $bruteforceMatch->getCardinality());
            };

            $s = $match->end + 1;
            $matchSequenceCopy[] = $match;
        }

        if ($s < $passwordLength) {
            $matchSequenceCopy[] = $this->makeBruteforceMatch($password, $s, $passwordLength - 1, $bruteforceMatch->getCardinality());
        }

        $this->matchSequence = $matchSequenceCopy;
        $minEntropy = $entropyStack[$passwordLength - 1];

        return $minEntropy;
    }

    /**
     * Get previous value in an array if set otherwise 0.
     *
     * @param array $array
     *   Array to search.
     * @param $index
     *   Index to get previous value from.
     *
     * @return mixed
     */
    protected function prevValue($array, $index)
    {
        $index = $index - 1;
        return ($index < 0 || $index >= count($array)) ? 0 : $array[$index];
    }

    /**
     * Make a bruteforce match object for substring of password.
     *
     * @param string $password
     * @param int $begin
     * @param int $end
     * @param int $cardinality optional
     *
     * @return Bruteforce match
     */
    protected function makeBruteforceMatch($password, $begin, $end, $cardinality = null)
    {
        $match = new Bruteforce($password, $begin, $end, substr($password, $begin, $end + 1), $cardinality);
        // Set entropy in match.
        $match->getEntropy();
        return $match;
    }
}