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

namespace OrangeHRM\OAuth\Controller;

use League\OAuth2\Server\Exception\OAuthServerException;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\CorporateBranding\Traits\ThemeServiceTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\OAuth\Traits\OAuthServerTrait;
use OrangeHRM\OAuth\Traits\PsrHttpFactoryHelperTrait;
use Throwable;

class AuthorizationController extends AbstractVueController
{
    use OAuthServerTrait;
    use PsrHttpFactoryHelperTrait;
    use LoggerTrait;
    use ThemeServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $psrRequest = $this->getPsrHttpFactoryHelper()->createPsr7Request($request);
        $component = new Component('oauth-authorize');
        $authRequest = null;
        try {
            $server = $this->getOAuthServer()->getServer();
            $authRequest = $server->validateAuthorizationRequest($psrRequest);
        } catch (OAuthServerException $e) {
            $component->addProp(new Prop('error-type', Prop::TYPE_STRING, $e->getErrorType()));
            $this->getLogger()->error($e->getMessage());
            $this->getLogger()->error($e->getTraceAsString());
        } catch (Throwable $e) {
            $component->addProp(new Prop('error-type', Prop::TYPE_STRING, 'server_error'));
            $this->getLogger()->error($e->getMessage());
            $this->getLogger()->error($e->getTraceAsString());
        }

        $component->addProp(new Prop('params', Prop::TYPE_OBJECT, $psrRequest->getQueryParams()));
        if ($authRequest !== null) {
            $component->addProp(
                new Prop('client-name', Prop::TYPE_STRING, $authRequest->getClient()->getDisplayName())
            );
        }

        $assetsVersion = Config::get(Config::VUE_BUILD_TIMESTAMP);
        $loginBannerUrl = $request->getBasePath() . "/images/ohrm_branding.png?$assetsVersion";
        if (!is_null($this->getThemeService()->getImageETag('login_banner'))) {
            $loginBannerUrl = $request->getBaseUrl() . "/admin/theme/image/loginBanner?$assetsVersion";
        }
        $component->addProp(new Prop('login-banner-src', Prop::TYPE_STRING, $loginBannerUrl));

        $this->setComponent($component);
        $this->setTemplate('no_header.html.twig');
    }
}
