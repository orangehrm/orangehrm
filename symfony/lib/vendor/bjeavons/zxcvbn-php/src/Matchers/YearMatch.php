<?php

namespace ZxcvbnPhp\Matchers;

class YearMatch extends Match
{

    const NUM_YEARS = 119;

    /**
     * Match occurences of years in a password
     *
     * @copydoc Match::match()
     */
    public static function match($password, array $userInputs = array())
    {
        $matches = array();
        $groups = static::findAll($password, "/(19\d\d|200\d|201\d)/");
        foreach ($groups as $captures) {
            $matches[] = new static($password, $captures[1]['begin'], $captures[1]['end'], $captures[1]['token']);
        }
        return $matches;
    }

    /**
     * @param $password
     * @param $begin
     * @param $end
     * @param $token
     */
    public function __construct($password, $begin, $end, $token)
    {
        parent::__construct($password, $begin, $end, $token);
        $this->pattern = 'year';
    }

    /**
     * @return float
     */
    public function getEntropy()
    {
        if (is_null($this->entropy)) {
            $this->entropy = $this->log(self::NUM_YEARS);
        }
        return $this->entropy;
    }
}