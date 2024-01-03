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

namespace OrangeHRM\Core\Service;

class TextHelperService
{
    /**
     * @param string $text
     * @param string|null $encoding
     * @return int
     * @link https://www.php.net/manual/en/mbstring.supported-encodings.php
     */
    public function strLength(string $text, ?string $encoding = null): int
    {
        if (function_exists('mb_strlen')) {
            if (is_null($encoding)) {
                return mb_strlen($text);
            }
            return mb_strlen($text, $encoding);
        } else {
            return strlen($text);
        }
    }

    /**
     * @link https://www.php.net/manual/en/function.str-contains.php
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public function strContains(string $haystack, string $needle): bool
    {
        if (function_exists('str_contains')) {
            return str_contains($haystack, $needle);
        }
        return '' === $needle || false !== strpos($haystack, $needle);
    }

    /**
     * @link https://www.php.net/manual/en/function.str-starts-with.php
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public function strStartsWith(string $haystack, string $needle): bool
    {
        if (function_exists('str_starts_with')) {
            return str_starts_with($haystack, $needle);
        }
        return 0 === strncmp($haystack, $needle, strlen($needle));
    }

    /**
     * @link https://www.php.net/manual/en/function.str-ends-with.php
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public function strEndsWith(string $haystack, string $needle): bool
    {
        if (function_exists('str_ends_with')) {
            return str_ends_with($haystack, $needle);
        }
        return '' === $needle || ('' !== $haystack && 0 === substr_compare($haystack, $needle, -strlen($needle)));
    }
}
