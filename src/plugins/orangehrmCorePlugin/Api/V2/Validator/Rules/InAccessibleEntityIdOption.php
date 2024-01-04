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

namespace OrangeHRM\Core\Api\V2\Validator\Rules;

use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Validator\Exceptions\ValidationEscapableException;

class InAccessibleEntityIdOption
{
    /**
     * @var bool
     */
    private bool $throw = true;

    /**
     * @var bool
     */
    private bool $throwIfOnlyEntityExist = true;

    /**
     * @var ValidationEscapableException|null
     */
    private ?ValidationEscapableException $throwable = null;

    /**
     * @var string
     */
    private string $exceptionMessage = ForbiddenException::DEFAULT_ERROR_MESSAGE;

    /**
     * @var array
     */
    private array $requiredPermissions = [];

    /**
     * @var string[]
     */
    private array $rolesToExclude = [];

    /**
     * @var string[]
     */
    private array $rolesToInclude = [];

    /**
     * @return ValidationEscapableException
     */
    public function getThrowable(): ValidationEscapableException
    {
        if (is_null($this->throwable)) {
            $this->throwable = new ForbiddenException($this->getExceptionMessage());
        }
        return $this->throwable;
    }

    /**
     * @param ValidationEscapableException $throwable
     * @return $this
     */
    public function setThrowable(ValidationEscapableException $throwable): self
    {
        $this->throwable = $throwable;
        return $this;
    }

    /**
     * @return string
     */
    public function getExceptionMessage(): string
    {
        return $this->exceptionMessage;
    }

    /**
     * @param string $exceptionMessage
     * @return $this
     */
    public function setExceptionMessage(string $exceptionMessage): self
    {
        $this->exceptionMessage = $exceptionMessage;
        return $this;
    }

    /**
     * @return bool
     */
    public function isThrow(): bool
    {
        return $this->throw;
    }

    /**
     * @param bool $throw
     * @return $this
     */
    public function setThrow(bool $throw): self
    {
        $this->throw = $throw;
        return $this;
    }

    /**
     * @return bool
     */
    public function isThrowIfOnlyEntityExist(): bool
    {
        return $this->throwIfOnlyEntityExist;
    }

    /**
     * @param bool $throwIfOnlyEntityExist
     * @return $this
     */
    public function setThrowIfOnlyEntityExist(bool $throwIfOnlyEntityExist): self
    {
        $this->throwIfOnlyEntityExist = $throwIfOnlyEntityExist;
        return $this;
    }

    /**
     * @return array
     */
    public function getRequiredPermissions(): array
    {
        return $this->requiredPermissions;
    }

    /**
     * @param array $requiredPermissions
     * @return self
     */
    public function setRequiredPermissions(array $requiredPermissions): self
    {
        $this->requiredPermissions = $requiredPermissions;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getRolesToExclude(): array
    {
        return $this->rolesToExclude;
    }

    /**
     * @param string[] $rolesToExclude
     * @return self
     */
    public function setRolesToExclude(array $rolesToExclude): self
    {
        $this->rolesToExclude = $rolesToExclude;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getRolesToInclude(): array
    {
        return $this->rolesToInclude;
    }

    /**
     * @param string[] $rolesToInclude
     * @return self
     */
    public function setRolesToInclude(array $rolesToInclude): self
    {
        $this->rolesToInclude = $rolesToInclude;
        return $this;
    }
}
