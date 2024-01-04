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

use InvalidArgumentException;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;

class StrLength extends AbstractRule
{
    use TextHelperTrait;

    private ?int $minValue;
    private ?int $maxValue;
    private ?string $encoding;
    private bool $inclusive;

    public function __construct(?int $min = null, ?int $max = null, ?string $encoding = null, bool $inclusive = true)
    {
        $this->minValue = $min;
        $this->maxValue = $max;
        $this->encoding = $encoding;
        $this->inclusive = $inclusive;

        if ($max !== null && $min > $max) {
            throw new InvalidArgumentException('Max value should be greater than Min value');
        }
    }

    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        $length = $this->getTextHelper()->strLength($input, $this->encoding);
        return $this->validateMin($length) && $this->validateMax($length);
    }

    /**
     * @param int $length
     * @return bool
     */
    private function validateMin(int $length): bool
    {
        if ($this->minValue === null) {
            return true;
        }

        return $this->inclusive ? ($length >= $this->minValue) : ($length > $this->minValue);
    }

    /**
     * @param int $length
     * @return bool
     */
    private function validateMax(int $length): bool
    {
        if ($this->maxValue === null) {
            return true;
        }

        return $this->inclusive ? ($length <= $this->maxValue) : ($length < $this->maxValue);
    }
}
