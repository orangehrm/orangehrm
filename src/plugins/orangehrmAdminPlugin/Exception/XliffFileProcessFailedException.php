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

namespace OrangeHRM\Admin\Exception;

use Exception;

class XliffFileProcessFailedException extends Exception
{
    /**
     * @return static
     */
    public static function validationFailed(): self
    {
        return new self("The XLIFF file is not valid");
    }

    /**
     * @return static
     */
    public static function emptyFile(): self
    {
        return new self("The XLIFF file is empty");
    }

    /**
     * @return static
     */
    public static function missingTargetLanguage(): self
    {
        return new self("The XLIFF file is missing the target language attribute");
    }

    /**
     * @return static
     */
    public static function invalidTargetLanguage(): self
    {
        return new self("The target language does not match the selected language");
    }
}
