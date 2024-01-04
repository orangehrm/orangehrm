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

namespace OrangeHRM\CorporateBranding\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\CorporateBranding\Dto\PartialTheme;
use OrangeHRM\CorporateBranding\Dto\ThemeImage;
use OrangeHRM\CorporateBranding\Service\ThemeService;
use OrangeHRM\Entity\Theme;

class ThemeDao extends BaseDao
{
    /**
     * @param Theme $theme
     * @return Theme
     */
    public function saveTheme(Theme $theme): Theme
    {
        $this->persist($theme);
        return $theme;
    }

    /**
     * @param string $themeName
     * @return Theme|null
     */
    public function getThemeByThemeName(string $themeName = ThemeService::DEFAULT_THEME): ?Theme
    {
        return $this->getRepository(Theme::class)->findOneBy(['name' => $themeName]);
    }

    /**
     * @param string $themeName
     * @return PartialTheme|null
     */
    public function getPartialThemeByThemeName(string $themeName = ThemeService::DEFAULT_THEME): ?PartialTheme
    {
        $select = 'NEW ' . PartialTheme::class .
            '(t.id, t.name, t.variables, t.showSocialMediaIcons, t.clientLogoFilename, t.clientBannerFilename, t.loginBannerFilename)';
        $q = $this->createQueryBuilder(Theme::class, 't')
            ->select($select);
        $q->andWhere('t.name = :themeName')
            ->setParameter('themeName', $themeName);

        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $themeName
     * @return int
     */
    public function deleteThemeByThemeName(string $themeName): int
    {
        return $this->createQueryBuilder(Theme::class, 'theme')
            ->delete()
            ->where('theme.name = :themeName')
            ->setParameter('themeName', $themeName)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $imageKey
     * @param string $themeName
     * @return ThemeImage|null
     */
    public function getImageByImageKeyAndThemeName(
        string $imageKey,
        string $themeName = ThemeService::CUSTOM_THEME
    ): ?ThemeImage {
        $map = [
            'client_logo' => 'clientLogo',
            'client_banner' => 'clientBanner',
            'login_banner' => 'loginBanner',
        ];
        $field = $map[$imageKey];
        $select = 'NEW ' . ThemeImage::class . "(t.$field, t.{$field}Filename, t.{$field}FileType, t.{$field}FileSize)";
        $q = $this->createQueryBuilder(Theme::class, 't')
            ->select($select);
        $q->andWhere('t.name = :themeName')
            ->setParameter('themeName', $themeName);

        $result = $q->getQuery()->getOneOrNullResult();
        if ($result instanceof ThemeImage && !$result->isEmpty()) {
            return $result;
        }
        return null;
    }
}
