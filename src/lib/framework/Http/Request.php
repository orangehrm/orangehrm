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

namespace OrangeHRM\Framework\Http;

use BadFunctionCallException;
use OrangeHRM\Config\Config;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class Request extends HttpRequest
{
    /**
     * @inheritDoc
     * @deprecated
     */
    public function get(string $key, $default = null)
    {
        if (Config::PRODUCT_MODE == Config::MODE_DEV) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
            if (count($backtrace) > 0 && isset($backtrace[0]['file'])) {
                $callerFile = $backtrace[0]['file'];
                $callerFile = str_replace(Config::get(Config::BASE_DIR), '', $callerFile);
                if (false !== strpos($callerFile, '/src/plugins')) {
                    throw new BadFunctionCallException(
                        'Internal method since Symfony 5.4, use explicit request parameters from the appropriate public property (attributes, query, request) instead. ' .
                        'See more https://symfony.com/blog/new-in-symfony-5-4-controller-changes'
                    );
                }
            }
        }
        return parent::get($key, $default);
    }

    /**
     * @return static
     */
    public static function createFromGlobals(): self
    {
        if (!isset($_SERVER['HTTP_AUTHORIZATION']) || !isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            // https://github.com/symfony/symfony/issues/19693
            $headers = array_change_key_case(getallheaders(), CASE_LOWER);
            if (isset($headers['authorization'])) {
                $_SERVER['HTTP_AUTHORIZATION'] = $headers['authorization'];
            }
        }
        return parent::createFromGlobals();
    }
}
