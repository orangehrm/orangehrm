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

namespace OrangeHRM\OpenidAuthentication\OpenID;

use OrangeHRM\Core\Traits\Auth\AuthUserTrait;

class OpenIDConnectClient extends \Jumbojett\OpenIDConnectClient
{
    use AuthUserTrait;

    protected ?string $generatedAuthUrl = null;

    public function redirect($url)
    {
        $this->generatedAuthUrl = $url;
    }

    public function getGeneratedAuthUrl(): string
    {
        return $this->generatedAuthUrl;
    }

    public function commitSession()
    {
    }

    protected function setSessionKey($key, $value)
    {
        $this->getAuthUser()->setAttribute($key, $value);
    }

    protected function getSessionKey($key)
    {
        return $this->getAuthUser()->getAttribute($key);
    }

    protected function unsetSessionKey($key)
    {
        $this->getAuthUser()->removeAttribute($key);
    }
}
