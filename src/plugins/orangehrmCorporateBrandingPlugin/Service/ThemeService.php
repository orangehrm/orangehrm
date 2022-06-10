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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\CorporateBranding\Service;

use OrangeHRM\Core\Traits\CacheTrait;
use OrangeHRM\Core\Traits\ETagHelperTrait;
use OrangeHRM\CorporateBranding\Dao\ThemeDao;
use OrangeHRM\CorporateBranding\Dto\PartialTheme;
use OrangeHRM\CorporateBranding\Dto\ThemeImage;

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
        /** @var ThemeImage|null $image */
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
                return $theme->getVariables();
            }
        );
    }

    /**
     * @return bool
     */
    public function getShowSocialMediaImages(): bool
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
}
