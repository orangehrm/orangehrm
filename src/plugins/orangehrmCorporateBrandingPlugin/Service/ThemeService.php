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

namespace OrangeHRM\CorporateBranding\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\CacheTrait;
use OrangeHRM\Core\Traits\ETagHelperTrait;
use OrangeHRM\CorporateBranding\Dao\ThemeDao;
use OrangeHRM\CorporateBranding\Dto\PartialTheme;
use OrangeHRM\CorporateBranding\Dto\ThemeImage;
use OrangeHRM\CorporateBranding\Dto\ThemeVariables;
use OrangeHRM\Framework\Http\Request;

class ThemeService
{
    use CacheTrait;
    use ETagHelperTrait;

    public const DEFAULT_THEME = 'default';
    public const CUSTOM_THEME = 'custom';

    public const PRIMARY_COLOR = 'primaryColor';
    public const PRIMARY_FONT_COLOR = 'primaryFontColor';
    public const SECONDARY_COLOR = 'secondaryColor';
    public const SECONDARY_FONT_COLOR = 'secondaryFontColor';
    public const PRIMARY_GRADIENT_START_COLOR = 'primaryGradientStartColor';
    public const PRIMARY_GRADIENT_END_COLOR = 'primaryGradientEndColor';

    public const THEME_CLIENT_LOGO_CACHE_KEY = 'admin.theme.client_logo.image';
    public const THEME_CLIENT_LOGO_ETAG_CACHE_KEY = 'admin.theme.client_logo.etag';
    public const THEME_CLIENT_BANNER_CACHE_KEY = 'admin.theme.client_banner.image';
    public const THEME_CLIENT_BANNER_ETAG_CACHE_KEY = 'admin.theme.client_banner.etag';
    public const THEME_LOGIN_BANNER_CACHE_KEY = 'admin.theme.login_banner.image';
    public const THEME_LOGIN_BANNER_ETAG_CACHE_KEY = 'admin.theme.login_banner.etag';
    public const THEME_SHOW_SOCIAL_MEDIA_IMAGES_CACHE_KEY = 'admin.theme.show_social_media_images';
    public const THEME_VARIABLES_CACHE_KEY = 'admin.theme.variables';

    /**
     * @var ThemeDao|null
     */
    private ?ThemeDao $themeDao = null;

    /**
     * @var ScssHelper|null
     */
    private ?ScssHelper $scssHelper = null;

    /**
     * @return ThemeDao
     */
    public function getThemeDao(): ThemeDao
    {
        if (!$this->themeDao instanceof ThemeDao) {
            $this->themeDao = new ThemeDao();
        }
        return $this->themeDao;
    }

    /**
     * @return ScssHelper
     */
    public function getScssHelper(): ScssHelper
    {
        if (!$this->scssHelper instanceof ScssHelper) {
            $this->scssHelper = new ScssHelper();
        }
        return $this->scssHelper;
    }

    /**
     * @param string $imageKey client_logo, client_banner, login_banner
     * @return string|null
     */
    public function getImageETag(string $imageKey): ?string
    {
        $cacheKey = "admin.theme.$imageKey.etag";
        $cacheItem = $this->getCache()->getItem($cacheKey);
        if (!$cacheItem->isHit()) {
            // If cache not found, need to create
            return $this->getImageETagAlongWithCache($imageKey);
        }
        return $cacheItem->get();
    }

    /**
     * @param string $imageKey
     * @return ThemeImage|null
     */
    public function getImage(string $imageKey): ?ThemeImage
    {
        $cacheKey = "admin.theme.$imageKey.image";
        $cacheItem = $this->getCache()->getItem($cacheKey);
        if (!$cacheItem->isHit()) {
            $this->getImageETag($imageKey);
            $cacheItem = $this->getCache()->getItem($cacheKey);
        }
        return ThemeImage::createFromArray($cacheItem->get());
    }

    /**
     * @param string $imageKey
     * @return string|null
     */
    private function getImageETagAlongWithCache(string $imageKey): ?string
    {
        $image = $this->getCache()->get(
            "admin.theme.$imageKey.image",
            function () use ($imageKey) {
                $image = $this->getThemeDao()->getImageByImageKeyAndThemeName($imageKey);
                if ($image instanceof ThemeImage) {
                    return $image->toArray();
                }
                return null;
            }
        );
        return $this->getCache()->get(
            "admin.theme.$imageKey.etag",
            function () use ($image) {
                if (is_null($image)) {
                    return null;
                }
                return $this->generateEtag($image['content']);
            }
        );
    }

    /**
     * @return array
     */
    public function getCurrentThemeVariables(): array
    {
        return $this->getCache()->get(
            self::THEME_VARIABLES_CACHE_KEY,
            function () {
                $theme = $this->getThemeDao()->getPartialThemeByThemeName(ThemeService::CUSTOM_THEME);
                if (!$theme instanceof PartialTheme) {
                    $theme = $this->getThemeDao()->getPartialThemeByThemeName(ThemeService::DEFAULT_THEME);
                }
                return $this->getDerivedCssVariables(ThemeVariables::createFromArray($theme->getVariables()));
            }
        );
    }

    /**
     * @return bool
     */
    public function showSocialMediaImages(): bool
    {
        return $this->getCache()->get(
            self::THEME_SHOW_SOCIAL_MEDIA_IMAGES_CACHE_KEY,
            function () {
                $theme = $this->getThemeDao()->getPartialThemeByThemeName(ThemeService::CUSTOM_THEME);
                if (!$theme instanceof PartialTheme) {
                    $theme = $this->getThemeDao()->getPartialThemeByThemeName(ThemeService::DEFAULT_THEME);
                }
                return $theme->showSocialMediaIcons();
            }
        );
    }

    public function resetThemeCache(): void
    {
        $this->getCache()->clear('admin.theme');
    }

    /**
     * @param ThemeVariables $variables
     * @return array
     */
    public function getDerivedCssVariables(ThemeVariables $variables): array
    {
        return [
            '--oxd-primary-one-color' => $variables->getPrimaryColor(),
            '--oxd-primary-font-color' => $variables->getPrimaryFontColor(),
            '--oxd-secondary-four-color' => $variables->getSecondaryColor(),
            '--oxd-secondary-font-color' => $variables->getSecondaryFontColor(),
            '--oxd-primary-gradient-start-color' => $variables->getPrimaryGradientStartColor(),
            '--oxd-primary-gradient-end-color' => $variables->getPrimaryGradientEndColor(),
            '--oxd-secondary-gradient-start-color' => $variables->getPrimaryGradientStartColor(),
            '--oxd-secondary-gradient-end-color' => $variables->getPrimaryGradientEndColor(),

            // Primary
            '--oxd-primary-one-lighten-5-color' => $this->getScssHelper()->lighten($variables->getPrimaryColor(), '5%'),
            '--oxd-primary-one-lighten-30-color' => $this->getScssHelper()
                ->lighten($variables->getPrimaryColor(), '30%'),
            '--oxd-primary-one-darken-5-color' => $this->getScssHelper()->darken($variables->getPrimaryColor(), '5%'),
            '--oxd-primary-one-alpha-10-color' => $this->getScssHelper()->rgba($variables->getPrimaryColor(), 0.1),
            '--oxd-primary-one-alpha-15-color' => $this->getScssHelper()->rgba($variables->getPrimaryColor(), 0.15),
            '--oxd-primary-one-alpha-20-color' => $this->getScssHelper()->rgba($variables->getPrimaryColor(), 0.2),
            '--oxd-primary-one-alpha-50-color' => $this->getScssHelper()->rgba($variables->getPrimaryColor(), 0.5),

            // Secondary
            '--oxd-secondary-four-lighten-5-color' => $this->getScssHelper()
                ->lighten($variables->getSecondaryColor(), '5%'),
            '--oxd-secondary-four-darken-5-color' => $this->getScssHelper()
                ->darken($variables->getSecondaryColor(), '5%'),
            '--oxd-secondary-four-alpha-10-color' => $this->getScssHelper()->rgba($variables->getSecondaryColor(), 0.1),
            '--oxd-secondary-four-alpha-15-color' => $this->getScssHelper()
                ->rgba($variables->getSecondaryColor(), 0.15),
            '--oxd-secondary-four-alpha-20-color' => $this->getScssHelper()->rgba($variables->getSecondaryColor(), 0.2),
            '--oxd-secondary-four-alpha-50-color' => $this->getScssHelper()->rgba($variables->getSecondaryColor(), 0.5),
        ];
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getClientLogoURL(Request $request): string
    {
        $assetsVersion = Config::get(Config::VUE_BUILD_TIMESTAMP);
        if ($this->getImageETag('client_logo') !== null) {
            return $request->getBaseUrl() . "/admin/theme/image/clientLogo?v=$assetsVersion";
        }
        return $request->getBasePath() . "/images/orange.png?v=$assetsVersion";
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getClientBannerURL(Request $request): string
    {
        $assetsVersion = Config::get(Config::VUE_BUILD_TIMESTAMP);
        if ($this->getImageETag('client_banner') !== null) {
            return $request->getBaseUrl() . "/admin/theme/image/clientBanner?v=$assetsVersion";
        }
        return $request->getBasePath() . "/images/orangehrm-logo.png?v=$assetsVersion";
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getLoginBannerURL(Request $request): string
    {
        $assetsVersion = Config::get(Config::VUE_BUILD_TIMESTAMP);
        if ($this->getImageETag('login_banner') !== null) {
            return $request->getBaseUrl() . "/admin/theme/image/loginBanner?v=$assetsVersion";
        }
        return $request->getBasePath() . "/images/ohrm_branding.png?v=$assetsVersion";
    }
}
