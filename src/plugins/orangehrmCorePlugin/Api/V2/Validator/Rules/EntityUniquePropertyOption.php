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

use Closure;

class EntityUniquePropertyOption
{
    private bool $trim = true;

    private Closure $trimFunction;
    private ?array $ignoreValues;
    private ?array $matchValues;

    public function __construct()
    {
        $this->setTrimFunction(fn ($input) => trim($input));
        $this->ignoreValues = null;
    }

    /**
     * @return bool
     */
    public function isTrim(): bool
    {
        return $this->trim;
    }

    /**
     * @param bool $trim
     * @return $this
     */
    public function setTrim(bool $trim): self
    {
        $this->trim = $trim;
        return $this;
    }

    /**
     * @return Closure
     */
    public function getTrimFunction(): Closure
    {
        return $this->trimFunction;
    }

    /**
     * @param Closure $trimFunction
     *
     * e.g. 1;
     * $entityUniquePropertyOption->setTrimFunction(function ($input) {
     *     return trim($input);
     * });
     *
     * e.g. 2;
     * $entityUniquePropertyOption->setTrimFunction(fn($input) => rtrim($input));
     *
     * @return $this
     */
    public function setTrimFunction(Closure $trimFunction): self
    {
        $this->trimFunction = $trimFunction;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasIgnoreValues(): bool
    {
        return isset($this->ignoreValues);
    }

    /**
     * @param array $ignoreValues
     * Getter method => value (if this value is set, entity is ignored)
     * E.g: ['isDeleted' => true, 'getId' => 11]
     */
    public function setIgnoreValues(array $ignoreValues): self
    {
        $this->ignoreValues = $ignoreValues;
        return $this;
    }

    /**
     * @return array
     */
    public function getIgnoreValues(): array
    {
        return $this->ignoreValues;
    }

    /**
     * @param int $ignoreId
     * @return $this
     * Helper function. Provide ID to ignore
     */
    public function setIgnoreId(int $ignoreId): self
    {
        $ignoreIdValues = ['id' => $ignoreId];
        $this->ignoreValues = is_array($this->ignoreValues) ?
            array_merge($ignoreIdValues, $this->ignoreValues) :
            $ignoreIdValues;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getMatchValues(): ?array
    {
        return $this->matchValues;
    }

    /**
     * @param array|null $matchValues
     * @return $this
     */
    public function setMatchValues(?array $matchValues): self
    {
        $this->matchValues = $matchValues;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasMatchValues(): bool
    {
        return isset($this->matchValues);
    }
}
