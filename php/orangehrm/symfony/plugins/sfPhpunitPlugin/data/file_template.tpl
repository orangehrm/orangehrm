<?php

require_once 'PHPUnit/Framework.php';

class {testClassName} extends PHPUnit_Framework_TestCase
{
    /**
     * {className}
     *
     * @var {className}
     */
    protected $o;
    
    public function setup()
    {
        $this->o = new {className}();
    }

    {methods}

}