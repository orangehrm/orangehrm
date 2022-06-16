<?php

namespace OrangeHRM\Performance\Exception;

use Exception;

class PerformanceTrackerServiceException extends Exception
{
    /**
     * @return static
     */
    public static function cannotEditEmployeeWithLogs(): self
    {
        return new self("The employee cannot be updated since the performance tracker already contains review logs.");
    }
}
