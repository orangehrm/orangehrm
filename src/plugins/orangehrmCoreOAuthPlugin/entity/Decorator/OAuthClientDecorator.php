<?php

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Entity\OAuthClient;

class OAuthClientDecorator
{
    private OAuthClient $oauthClient;

    /**
     * @param OAuthClient $oauthClient
     */
    public function __construct(OAuthClient $oauthClient)
    {
        $this->oauthClient = $oauthClient;
    }
}
