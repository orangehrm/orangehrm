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

namespace OrangeHRM\OpenidAuthentication\OpenID;

use OrangeHRM\Core\Traits\Auth\AuthUserTrait;

class OpenIDConnectClient extends \Jumbojett\OpenIDConnectClient
{
    use AuthUserTrait;

    protected ?string $generatedAuthUrl = null;

    /**
     * @inheritDoc
     */
    public function redirect(string $url)
    {
        $this->generatedAuthUrl = $url;
    }

    /**
     * @inheritDoc
     */
    public function getGeneratedAuthUrl(): string
    {
        return $this->generatedAuthUrl;
    }

    /**
     * @inheritDoc
     */
    public function commitSession(): void
    {
    }

    /**
     * @inheritDoc
     */
    protected function setSessionKey(string $key, $value)
    {
        $this->getAuthUser()->setAttribute($key, $value);
    }

    /**
     * @inheritDoc
     */
    protected function getSessionKey(string $key)
    {
        return $this->getAuthUser()->getAttribute($key);
    }

    /**
     * @inheritDoc
     */
    protected function unsetSessionKey(string $key)
    {
        $this->getAuthUser()->removeAttribute($key);
    }
}
