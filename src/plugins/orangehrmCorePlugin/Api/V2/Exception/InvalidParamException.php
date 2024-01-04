<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Core\Api\V2\Exception;

use Exception;
use Throwable;

class InvalidParamException extends Exception
{
    public const DEFAULT_ERROR_MESSAGE = "Invalid Parameter";

    /**
     * @var array
     */
    protected array $errorBag = [];

    /**
     * @param array $errorBag
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        array $errorBag = [],
        $message = self::DEFAULT_ERROR_MESSAGE,
        $code = 0,
        Throwable $previous = null
    ) {
        $this->errorBag = $errorBag;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getErrorBag(): array
    {
        return $this->errorBag;
    }

    /**
     * @param array $errorBag
     */
    public function setErrorBag(array $errorBag): void
    {
        $this->errorBag = $errorBag;
    }

    /**
     * @return array
     */
    public function getNormalizedErrorBag(): array
    {
        return [
            'invalidParamKeys' => array_keys($this->getErrorBag()),
        ];
    }
}
