<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Framework\Http;

use BadFunctionCallException;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class Request extends HttpRequest
{
    /**
     * @inheritDoc
     * @deprecated
     */
    public function get(string $key, $default = null)
    {
        throw new BadFunctionCallException(
            'Internal method since Symfony 5.4, use explicit request parameters from the appropriate public property (attributes, query, request) instead. ' .
            'See more https://symfony.com/blog/new-in-symfony-5-4-controller-changes'
        );
    }
}
