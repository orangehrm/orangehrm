<?php

namespace ZxcvbnPhp\Matchers;

class SequenceMatch extends Match
{

    const LOWER = 'abcdefghijklmnopqrstuvwxyz';
    const UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const DIGITS = '0123456789';

    /**
     * @var
     */
    public $sequenceName;

    /**
     * @var
     */
    public $sequenceSpace;

    /**
     * @var
     */
    public $ascending;

    /**
     * Match sequences of three or more characters.
     *
     * @copydoc Match::match()
     */
    public static function match($password, array $userInputs = array())
    {
        $matches = array();
        $passwordLength = strlen($password);

        $sequences = self::LOWER . self::UPPER . self::DIGITS;
        $revSequences = strrev($sequences);

        for ($i = 0; $i < $passwordLength; $i++) {
            $pattern = false;
            $j = $i + 2;
            // Check for sequence sizes of 3 or more.
            if ($j < $passwordLength) {
                $pattern = substr($password, $i, 3);
            }
            // Find beginning of pattern and then extract full sequences intersection.
            if ($pattern && ($pos = strpos($sequences, $pattern)) !== false) {
                // Match only remaining password characters.
                $remainder = substr($password, $j + 1);
                $pattern .= static::intersect($sequences, $remainder, $pos + 3);
                $params = array(
                    'ascending' => true,
                    'sequenceName' => static::getSequenceName($pos),
                    'sequenceSpace' => static::getSequenceSpace($pos),
                );
                $matches[] = new static($password, $i, $i + strlen($pattern) - 1, $pattern, $params);
                // Skip intersecting characters on next loop.
                $i += strlen($pattern) - 1;
            }
            // Search the reverse sequence for pattern.
            elseif ($pattern && ($pos = strpos($revSequences, $pattern)) !== false) {
                $remainder = substr($password, $j + 1);
                $pattern .= static::intersect($revSequences, $remainder, $pos + 3);
                $params = array(
                    'ascending' => false,
                    'sequenceName' => static::getSequenceName($pos),
                    'sequenceSpace' => static::getSequenceSpace($pos),
                );
                $matches[] = new static($password, $i, $i + strlen($pattern) - 1, $pattern, $params);
                $i += strlen($pattern) - 1;
            }
        }
        return $matches;
    }

    /**
     * @param $password
     * @param $begin
     * @param $end
     * @param $token
     * @param array $params
     */
    public function __construct($password, $begin, $end, $token, $params = array())
    {
        parent::__construct($password, $begin, $end, $token);
        $this->pattern = 'sequence';
        if (!empty($params)) {
            $this->sequenceName = isset($params['sequenceName']) ? $params['sequenceName'] : null;
            $this->sequenceSpace = isset($params['sequenceSpace']) ? $params['sequenceSpace'] : null;
            $this->ascending = isset($params['ascending']) ? $params['ascending'] : null;
        }
    }

    /**
     * @copydoc Match::getEntropy()
     */
    public function getEntropy()
    {
        $char = $this->token[0];
        if ($char === 'a' || $char === '1') {
            $entropy = 1;
        }
        else {
            $ord = ord($char);

            if ($this->isDigit($ord)) {
                $entropy = $this->log(10);
            }
            elseif ($this->isLower($ord)) {
                $entropy = $this->log(26);
            }
            else {
                $entropy = $this->log(26) + 1; // Extra bit for upper.
            }
        }

        if (empty($this->ascending)) {
            $entropy += 1; // Extra bit for descending instead of ascending
        }

        return $entropy + $this->log(strlen($this->token));
    }

    /**
     * Find sub-string intersection in a string.
     *
     * @param string $string
     * @param string $subString
     * @param int $start
     *
     * @return string
     */
    protected static function intersect($string, $subString, $start) {
        $cut = str_split(substr($string, $start, strlen($subString)));
        $comp = str_split($subString);
        foreach ($cut as $i => $c) {
            if ($comp[$i] === $c) {
                $intersect[] = $c;
            }
            else {
                break; // Stop loop since intersection ends.
            }
        }
        if (!empty($intersect)) {
            return implode('', $intersect);
        }
        return '';
    }

    /**
     * @param $pos
     * @param bool $reverse
     * @return int
     */
    protected static function getSequenceSpace($pos, $reverse = false)
    {
        $name = static::getSequenceName($pos, $reverse);
        switch ($name) {
            case 'lower':
                return strlen(self::LOWER);
            case 'upper':
                return strlen(self::UPPER);
            case 'digits':
                return strlen(self::DIGITS);
        }
    }

    /**
     * Name of sequence a sequences position belongs to.
     *
     * @param int $pos
     * @param bool $reverse
     * @return string
     */
    protected static function getSequenceName($pos, $reverse = false)
    {
        $sequences = self::LOWER . self::UPPER . self::DIGITS;
        $end = strlen($sequences);
        if (!$reverse && $pos < strlen(self::LOWER)) {
            return 'lower';
        }
        elseif (!$reverse && $pos <= $end - strlen(self::DIGITS)) {
            return 'upper';
        }
        elseif (!$reverse) {
            return 'digits';
        }
        elseif ($pos < strlen(self::DIGITS)) {
            return 'digits';
        }
        elseif ($pos <= $end - strlen(self::LOWER)) {
            return 'upper';
        }
        else {
            return 'lower';
        }
    }
}