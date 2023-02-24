<?php

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Entity\OAuthAccessToken;

class OAuthAccessTokenDecorator
{
    private OAuthAccessToken $oauthAccessToken;

    /**
     * @param OAuthAccessToken $oauthAccessToken
     */
    public function __construct(OAuthAccessToken $oauthAccessToken)
    {
        $this->oauthAccessToken = $oauthAccessToken;
    }
}
