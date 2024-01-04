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
use DateTime;

class LessThanOrEqual extends AbstractRule
{
    private $targetObject;

    /**
     * @param Closure|DateTime|float|int $targetObject
     */
    public function __construct($targetObject)
    {
        $this->targetObject = $targetObject;
    }

    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        $targetObject = $this->targetObject;
        if (is_callable($targetObject)) {
            $targetObject = $targetObject();
        }
        if ($targetObject instanceof DateTime) {
            $input = new DateTime($input);
        }
        return $input <= $targetObject;
    }
}
