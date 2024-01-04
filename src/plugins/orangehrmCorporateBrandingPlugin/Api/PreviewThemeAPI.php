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

namespace OrangeHRM\CorporateBranding\Api;

use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\CorporateBranding\Api\Traits\VariablesParamRuleCollection;
use OrangeHRM\CorporateBranding\Dto\ThemeVariables;
use OrangeHRM\CorporateBranding\Traits\ThemeServiceTrait;

class PreviewThemeAPI extends Endpoint implements CollectionEndpoint
{
    use ThemeServiceTrait;
    use VariablesParamRuleCollection;

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @OA\Post(
     *     path="/api/v2/admin/theme/preview",
     *     tags={"Admin/Theme"},
     *     summary="Preview Theme",
     *     operationId="preview-theme",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="primaryColor", type="string", example="#FF7B1D"),
     *             @OA\Property(property="primaryFontColor", type="string", example="#FFFFFF"),
     *             @OA\Property(property="secondaryColor", type="string", example="#76BC21"),
     *             @OA\Property(property="secondaryFontColor", type="string", example="#FFFFFF"),
     *             @OA\Property(property="primaryGradientStartColor", type="string", example="#FF920B"),
     *             @OA\Property(property="primaryGradientEndColor", type="string", example="#F35C17"),
     *             required={"primaryColor", "primaryFontColor", "secondaryColor", "secondaryFontColor", "primaryGradientStartColor", "primaryGradientEndColor"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="--oxd-primary-one-color", type="string", example="#FF7B1D"),
     *                 @OA\Property(property="--oxd-primary-font-color", type="string", example="#FFFFFF"),
     *                 @OA\Property(property="--oxd-secondary-four-color", type="string", example="#76BC21"),
     *                 @OA\Property(property="--oxd-secondary-font-color", type="string", example="#FFFFFF"),
     *                 @OA\Property(property="--oxd-primary-gradient-start-color", type="string", example="#FF920B"),
     *                 @OA\Property(property="--oxd-primary-gradient-end-color", type="string", example="#F35C17"),
     *                 @OA\Property(property="--oxd-secondary-gradient-start-color", type="string", example="#FF920B"),
     *                 @OA\Property(property="--oxd-secondary-gradient-end-color", type="string", example="#F35C17"),
     *                 @OA\Property(property="--oxd-primary-one-lighten-5-color", type="string", example="#ff8a37"),
     *                 @OA\Property(property="--oxd-primary-one-lighten-30-color", type="string", example="#ffd4b6"),
     *                 @OA\Property(property="--oxd-primary-one-darken-5-color", type="string", example="#ff6c03"),
     *                 @OA\Property(property="--oxd-primary-one-alpha-10-color", type="string", example="rgba(255, 123, 29, 0.1)"),
     *                 @OA\Property(property="--oxd-primary-one-alpha-15-color", type="string", example="rgba(255, 123, 29, 0.15)"),
     *                 @OA\Property(property="--oxd-primary-one-alpha-20-color", type="string", example="rgba(255, 123, 29, 0.2)"),
     *                 @OA\Property(property="--oxd-primary-one-alpha-50-color", type="string", example="rgba(255, 123, 29, 0.5)"),
     *                 @OA\Property(property="--oxd-secondary-four-lighten-5-color", type="string", example="#84d225"),
     *                 @OA\Property(property="--oxd-secondary-four-darken-5-color", type="string", example="#68a61d"),
     *                 @OA\Property(property="--oxd-secondary-four-alpha-10-color", type="string", example="rgba(118, 188, 33, 0.1)"),
     *                 @OA\Property(property="--oxd-secondary-four-alpha-15-color", type="string", example="rgba(118, 188, 33, 0.15)"),
     *                 @OA\Property(property="--oxd-secondary-four-alpha-20-color", type="string", example="rgba(118, 188, 33, 0.2)"),
     *                 @OA\Property(property="--oxd-secondary-four-alpha-50-color", type="string", example="rgba(118, 188, 33, 0.5)")
     *             )
     *         )
     *     ),
     * )
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $themeVariables = ThemeVariables::createFromArray($this->getRequest()->getBody()->all());
        return new EndpointResourceResult(
            ArrayModel::class,
            $this->getThemeService()->getDerivedCssVariables($themeVariables)
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return $this->getParamRuleCollection();
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
