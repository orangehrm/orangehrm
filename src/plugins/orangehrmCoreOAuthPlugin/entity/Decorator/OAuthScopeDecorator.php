<?php

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Entity\OAuthScope;

class OAuthScopeDecorator
{
    private OAuthScope $oauthScope;

    /**
     * @param OAuthScope $oauthScope
     */
    public function __construct(OAuthScope $oauthScope)
    {
        $this->oauthScope = $oauthScope;
    }
}
