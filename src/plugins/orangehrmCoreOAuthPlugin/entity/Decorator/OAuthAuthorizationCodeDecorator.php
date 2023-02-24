<?php

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Entity\OAuthAuthorizationCode;

class OAuthAuthorizationCodeDecorator
{
    private OAuthAuthorizationCode $oauthAuthorizationCode;

    /**
     * @param OAuthAuthorizationCode $oauthAuthorizationCode
     */
    public function __construct(OAuthAuthorizationCode $oauthAuthorizationCode)
    {
        $this->oauthAuthorizationCode = $oauthAuthorizationCode;
    }
}
