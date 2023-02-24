<?php

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Entity\OAuthRefreshToken;

class OAuthRefreshTokenDecorator
{
    private OAuthRefreshToken $oauthRefreshToken;

    /**
     * @param OAuthRefreshToken $oauthRefreshToken
     */
    public function __construct(OAuthRefreshToken $oauthRefreshToken)
    {
        $this->oauthRefreshToken = $oauthRefreshToken;
    }
}
