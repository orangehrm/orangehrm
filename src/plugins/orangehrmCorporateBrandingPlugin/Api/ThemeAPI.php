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

namespace OrangeHRM\CorporateBranding\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Dto\Base64Attachment;
use OrangeHRM\Core\Traits\ValidatorTrait;
use OrangeHRM\CorporateBranding\Api\Model\ThemeModel;
use OrangeHRM\CorporateBranding\Api\Traits\VariablesParamRuleCollection;
use OrangeHRM\CorporateBranding\Api\ValidationRules\ImageAspectRatio;
use OrangeHRM\CorporateBranding\Dto\PartialTheme;
use OrangeHRM\CorporateBranding\Service\ThemeService;
use OrangeHRM\CorporateBranding\Traits\ThemeServiceTrait;
use OrangeHRM\Entity\Theme;

class ThemeAPI extends Endpoint implements ResourceEndpoint
{
    use ThemeServiceTrait;
    use ValidatorTrait;
    use VariablesParamRuleCollection;

    public const PARAMETER_VARIABLES = 'variables';
    public const PARAMETER_SHOW_SOCIAL_MEDIA_ICONS = 'showSocialMediaImages';
    public const PARAMETER_CLIENT_LOGO = 'clientLogo';
    public const PARAMETER_CLIENT_BANNER = 'clientBanner';
    public const PARAMETER_LOGIN_BANNER = 'loginBanner';
    public const PARAMETER_CURRENT_CLIENT_LOGO = 'currentClientLogo';
    public const PARAMETER_CURRENT_CLIENT_BANNER = 'currentClientBanner';
    public const PARAMETER_CURRENT_LOGIN_BANNER = 'currentLoginBanner';

    public const KEEP_CURRENT = 'keepCurrent';
    public const DELETE_CURRENT = 'deleteCurrent';
    public const REPLACE_CURRENT = 'replaceCurrent';

    public const PARAM_RULE_FILE_NAME_MAX_LENGTH = 100;

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $theme = $this->getThemeService()
            ->getThemeDao()
            ->getPartialThemeByThemeName(ThemeService::CUSTOM_THEME);

        if (!$theme instanceof PartialTheme) {
            $theme = $this->getThemeService()
                ->getThemeDao()
                ->getPartialThemeByThemeName();
        }

        return new EndpointResourceResult(ThemeModel::class, $theme);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection();
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $variables = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_VARIABLES);
        $this->validate($variables, $this->getParamRuleCollection());

        $theme = $this->getThemeService()
            ->getThemeDao()
            ->getThemeByThemeName(ThemeService::CUSTOM_THEME);

        if (!$theme instanceof Theme) {
            $theme = new Theme();
            $theme->setName(ThemeService::CUSTOM_THEME);
        }

        $theme->setVariables($variables);
        $theme->setShowSocialMediaIcons(
            $this->getRequestParams()->getBoolean(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SHOW_SOCIAL_MEDIA_ICONS,
                true
            )
        );

        $currentClientLogo = $this->getRequestParams()
            ->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CURRENT_CLIENT_LOGO);
        if (is_null($currentClientLogo) || $currentClientLogo === self::REPLACE_CURRENT) {
            $clientLogo = $this->getRequestParams()->getAttachmentOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_CLIENT_LOGO
            );
            if ($clientLogo instanceof Base64Attachment) {
                $theme->setClientLogo($clientLogo->getContent());
                $theme->setClientLogoFilename($clientLogo->getFilename());
                $theme->setClientLogoFileType($clientLogo->getFileType());
                $theme->setClientLogoFileSize($clientLogo->getSize());
            }
        } elseif ($currentClientLogo === self::DELETE_CURRENT) {
            $theme->setClientLogo(null);
            $theme->setClientLogoFilename(null);
            $theme->setClientLogoFileType(null);
            $theme->setClientLogoFileSize(null);
        } // else self::KEEP_CURRENT

        $currentClientBanner = $this->getRequestParams()
            ->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CURRENT_CLIENT_BANNER);
        if (is_null($currentClientBanner) || $currentClientBanner === self::REPLACE_CURRENT) {
            $clientBanner = $this->getRequestParams()->getAttachmentOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_CLIENT_BANNER
            );
            if ($clientBanner instanceof Base64Attachment) {
                $theme->setClientBanner($clientBanner->getContent());
                $theme->setClientBannerFilename($clientBanner->getFilename());
                $theme->setClientBannerFileType($clientBanner->getFileType());
                $theme->setClientBannerFileSize($clientBanner->getSize());
            }
        } elseif ($currentClientBanner === self::DELETE_CURRENT) {
            $theme->setClientBanner(null);
            $theme->setClientBannerFilename(null);
            $theme->setClientBannerFileType(null);
            $theme->setClientBannerFileSize(null);
        } // else self::KEEP_CURRENT

        $currentLoginBanner = $this->getRequestParams()
            ->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CURRENT_LOGIN_BANNER);
        if (is_null($currentLoginBanner) || $currentLoginBanner === self::REPLACE_CURRENT) {
            $loginBanner = $this->getRequestParams()->getAttachmentOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_LOGIN_BANNER
            );
            if ($loginBanner instanceof Base64Attachment) {
                $theme->setLoginBanner($loginBanner->getContent());
                $theme->setLoginBannerFilename($loginBanner->getFilename());
                $theme->setLoginBannerFileType($loginBanner->getFileType());
                $theme->setLoginBannerFileSize($loginBanner->getSize());
            }
        } elseif ($currentLoginBanner === self::DELETE_CURRENT) {
            $theme->setLoginBanner(null);
            $theme->setLoginBannerFilename(null);
            $theme->setLoginBannerFileType(null);
            $theme->setLoginBannerFileSize(null);
        } // else self::KEEP_CURRENT

        $this->getThemeService()->getThemeDao()->saveTheme($theme);
        $this->getThemeService()->resetThemeCache();

        return new EndpointResourceResult(ThemeModel::class, PartialTheme::createFromTheme($theme));
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $currentAttachmentRule = new Rule(
            Rules::IN,
            [[self::REPLACE_CURRENT, self::KEEP_CURRENT, self::DELETE_CURRENT]]
        );
        $paramRules = new ParamRuleCollection(
            new ParamRule(self::PARAMETER_VARIABLES, new Rule(Rules::ARRAY_TYPE)),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::PARAMETER_SHOW_SOCIAL_MEDIA_ICONS, new Rule(Rules::BOOL_TYPE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::PARAMETER_CURRENT_CLIENT_LOGO, $currentAttachmentRule)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::PARAMETER_CURRENT_CLIENT_BANNER, clone $currentAttachmentRule)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::PARAMETER_CURRENT_LOGIN_BANNER, clone $currentAttachmentRule)
            ),
        );

        $currentClientLogo = $this->getRequestParams()
            ->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CURRENT_CLIENT_LOGO);
        $currentClientBanner = $this->getRequestParams()
            ->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CURRENT_CLIENT_BANNER);
        $currentLoginBanner = $this->getRequestParams()
            ->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CURRENT_LOGIN_BANNER);
        if (is_null($currentClientLogo)) {
            $paramRules->addParamValidation(
                $this->getValidationDecorator()->notRequiredParamRule(
                    $this->getAttachmentParamRule(
                        self::PARAMETER_CLIENT_LOGO,
                        Theme::CLIENT_LOGO_ASPECT_RATIO
                    )
                )
            );
        } elseif ($currentClientLogo === self::REPLACE_CURRENT) {
            $paramRules->addParamValidation(
                $this->getAttachmentParamRule(self::PARAMETER_CLIENT_LOGO, Theme::CLIENT_LOGO_ASPECT_RATIO)
            );
        }

        if (is_null($currentClientBanner)) {
            $paramRules->addParamValidation(
                $this->getValidationDecorator()->notRequiredParamRule(
                    $this->getAttachmentParamRule(
                        self::PARAMETER_CLIENT_BANNER,
                        Theme::CLIENT_BANNER_ASPECT_RATIO
                    )
                )
            );
        } elseif ($currentClientBanner === self::REPLACE_CURRENT) {
            $paramRules->addParamValidation(
                $this->getAttachmentParamRule(
                    self::PARAMETER_CLIENT_BANNER,
                    Theme::CLIENT_BANNER_ASPECT_RATIO
                )
            );
        }

        if (is_null($currentLoginBanner)) {
            $paramRules->addParamValidation(
                $this->getValidationDecorator()->notRequiredParamRule(
                    $this->getAttachmentParamRule(
                        self::PARAMETER_LOGIN_BANNER,
                        Theme::LOGIN_BANNER_ASPECT_RATIO
                    )
                )
            );
        } elseif ($currentLoginBanner === self::REPLACE_CURRENT) {
            $paramRules->addParamValidation(
                $this->getAttachmentParamRule(self::PARAMETER_LOGIN_BANNER, Theme::LOGIN_BANNER_ASPECT_RATIO)
            );
        }

        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
    }

    /**
     * @param string $paramKey
     * @return ParamRule
     */
    private function getAttachmentParamRule(string $paramKey, float $aspectRatio): ParamRule
    {
        return new ParamRule(
            $paramKey,
            new Rule(
                Rules::BASE_64_ATTACHMENT,
                [Theme::ALLOWED_IMAGE_TYPES, Theme::ALLOWED_IMAGE_EXTENSIONS, self::PARAM_RULE_FILE_NAME_MAX_LENGTH]
            ),
            new Rule(ImageAspectRatio::class, [$aspectRatio]),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $affectedRows = $this->getThemeService()
            ->getThemeDao()
            ->deleteThemeByThemeName(ThemeService::CUSTOM_THEME);
        $this->getThemeService()->resetThemeCache();

        return new EndpointResourceResult(ArrayModel::class, ['success' => !$affectedRows == 0]);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection();
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
    }
}
