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

namespace OrangeHRM\OAuth\Server;

use League\OAuth2\Server\AuthorizationValidators\AuthorizationValidatorInterface;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\OAuthAccessToken;
use Psr\Http\Message\ServerRequestInterface;

class BearerTokenValidator implements AuthorizationValidatorInterface
{
    use CryptTrait;
    use DateTimeHelperTrait;

    public const ATTRIBUTE_ACCESS_TOKEN = '_oauth2_access_token';

    private AccessTokenRepositoryInterface $accessTokenRepository;

    /**
     * @param AccessTokenRepositoryInterface $accessTokenRepository
     */
    public function __construct(AccessTokenRepositoryInterface $accessTokenRepository, string $encryptionKey)
    {
        $this->accessTokenRepository = $accessTokenRepository;
        $this->encryptionKey = $encryptionKey;
    }

    /**
     * @inheritDoc
     */
    public function validateAuthorization(ServerRequestInterface $request): ServerRequestInterface
    {
        if ($request->hasHeader('authorization') === false) {
            throw OAuthServerException::accessDenied('Missing "Authorization" header');
        }

        $header = $request->getHeader('authorization');
        $tokenId = trim((string)preg_replace('/^\s*Bearer\s/', '', $header[0]));

        $tokenId = $this->decrypt($tokenId);

        if (!is_string($tokenId)) {
            throw OAuthServerException::accessDenied();
        }

        $accessToken = $this->accessTokenRepository->getAccessToken($tokenId);
        if (!$accessToken instanceof OAuthAccessToken) {
            throw OAuthServerException::accessDenied('Access token could not be verified');
        }

        // Check if token has been revoked
        if ($this->accessTokenRepository->isAccessTokenRevoked($tokenId)) {
            throw OAuthServerException::accessDenied('Access token has been revoked');
        }

        if ($this->getDateTimeHelper()->getNowInUTC() > $accessToken->getExpiryDateTime()) {
            throw OAuthServerException::accessDenied('The token is expired');
        }

        return $request->withAttribute(self::ATTRIBUTE_ACCESS_TOKEN, $accessToken);
    }
}
