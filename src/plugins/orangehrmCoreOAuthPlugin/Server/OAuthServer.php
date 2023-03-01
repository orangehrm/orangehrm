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

namespace OrangeHRM\OAuth\Server;

use DateInterval;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use OrangeHRM\OAuth\Dto\CryptKey;
use OrangeHRM\OAuth\Repository\AccessTokenRepository;
use OrangeHRM\OAuth\Repository\AuthorizationCodeRepository;
use OrangeHRM\OAuth\Repository\ClientRepository;
use OrangeHRM\OAuth\Repository\RefreshTokenRepository;
use OrangeHRM\OAuth\Repository\ScopeRepository;

class OAuthServer
{
    private ?AuthorizationServer $oauthServer = null;
    private ClientRepository $clientRepository;
    private ScopeRepository $scopeRepository;
    private AccessTokenRepository $accessTokenRepository;
    private AuthorizationCodeRepository $authCodeRepository;
    private RefreshTokenRepository $refreshTokenRepository;
    private string $encryptionKey;
    private DateInterval $authCodeTTL;
    private DateInterval $refreshTokenTTL;
    private DateInterval $accessTokenTTL;

    private function init(): void
    {
        $this->encryptionKey = 'lxZFUEsBCJ2Yb14IF2ygAHI5N4+ZAUXXaSeeJm6+twsUmIen'; // TODO:: generate using base64_encode(random_bytes(32))
        $this->clientRepository = new ClientRepository();
        $this->scopeRepository = new ScopeRepository();
        $this->accessTokenRepository = new AccessTokenRepository();
        $this->accessTokenRepository->setEncryptionKey($this->encryptionKey);
        $this->authCodeRepository = new AuthorizationCodeRepository();
        $this->refreshTokenRepository = new RefreshTokenRepository();
        $this->authCodeTTL = new DateInterval('PT10M');
        $this->refreshTokenTTL = new DateInterval('P1M');
        $this->accessTokenTTL = new DateInterval('PT30M');
    }

    /**
     * @return AuthorizationServer
     */
    public function getServer(): AuthorizationServer
    {
        if (!$this->oauthServer instanceof AuthorizationServer) {
            $this->init();
            $this->oauthServer = new AuthorizationServer(
                $this->clientRepository,
                $this->accessTokenRepository,
                $this->scopeRepository,
                new CryptKey(),
                $this->encryptionKey
            );

            $grant = new AuthCodeGrant($this->authCodeRepository, $this->refreshTokenRepository, $this->authCodeTTL);
            $grant->setRefreshTokenTTL($this->refreshTokenTTL);

            $refreshTokenGrant = new RefreshTokenGrant($this->refreshTokenRepository);
            $refreshTokenGrant->setRefreshTokenTTL($this->refreshTokenTTL);

            $this->oauthServer->enableGrantType($grant, $this->accessTokenTTL);
            $this->oauthServer->enableGrantType($refreshTokenGrant, $this->accessTokenTTL);
        }
        return $this->oauthServer;
    }
}
