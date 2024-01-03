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

namespace OrangeHRM\CorporateBranding\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\CorporateBranding\Dto\PartialTheme;

/**
 * @OA\Schema(
 *     schema="CorporateBranding-ThemeModel",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="default"),
 *     @OA\Property(property="variables", type="object",
 *         @OA\Property(property="primaryColor", type="string", example="#FF7B1D"),
 *         @OA\Property(property="primaryFontColor", type="string", example="#FFFFFF"),
 *         @OA\Property(property="secondaryColor", type="string", example="#76BC21"),
 *         @OA\Property(property="secondaryFontColor", type="string", example="#FFFFFF"),
 *         @OA\Property(property="primaryGradientStartColor", type="string", example="#FF920B"),
 *         @OA\Property(property="primaryGradientEndColor", type="string", example="#F35C17"),
 *     ),
 *     @OA\Property(property="showSocialMediaImages", type="boolean", example=true),
 *     @OA\Property(property="clientLogo", type="string"),
 *     @OA\Property(property="clientBanner", type="string"),
 *     @OA\Property(property="loginBanner", type="string"),
 * )
 */
class ThemeModel implements Normalizable
{
    use ModelTrait {
        ModelTrait::toArray as entityToArray;
    }

    public function __construct(PartialTheme $theme)
    {
        $this->setEntity($theme);
        $this->setFilters(
            [
                'name',
                'variables',
                ['showSocialMediaIcons'],
            ]
        );
        $this->setAttributeNames(
            [
                'name',
                'variables',
                'showSocialMediaImages',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $result = $this->entityToArray();
        $result['clientLogo'] = null;
        $result['clientBanner'] = null;
        $result['loginBanner'] = null;
        /** @var PartialTheme $theme */
        $theme = $this->getEntity();
        is_null($theme->getClientLogoFilename())
            ?: $result['clientLogo'] = ['filename' => $theme->getClientLogoFilename()];
        is_null($theme->getClientBannerFilename())
            ?: $result['clientBanner'] = ['filename' => $theme->getClientBannerFilename()];
        is_null($theme->getLoginBannerFilename())
            ?: $result['loginBanner'] = ['filename' => $theme->getLoginBannerFilename()];
        return $result;
    }
}
