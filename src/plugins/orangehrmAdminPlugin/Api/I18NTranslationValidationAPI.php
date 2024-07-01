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

namespace OrangeHRM\Admin\Api;

use OrangeHRM\Admin\Traits\Service\LocalizationServiceTrait;
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
use OrangeHRM\Entity\I18NLangString;

class I18NTranslationValidationAPI extends Endpoint implements ResourceEndpoint
{
    use LocalizationServiceTrait;

    public const PARAMETER_LANG_STRING_ID = 'langStringId';
    public const PARAMETER_TRANSLATION = 'translation';
    public const PARAM_RULE_TRANSLATION_MAX_LENGTH = 1000;

    /**
     * @OA\Get(
     *     path="/api/v2/admin/i18n/translation/{langStringId}/validate",
     *     tags={"Admin/I18N"},
     *     summary="Validate I18N Translation",
     *     operationId="validate-i18n-translation",
     *     @OA\PathParameter(
     *         name="langStringId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="translation",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=OrangeHRM\Admin\Api\I18NTranslationValidationAPI::PARAM_RULE_TRANSLATION_MAX_LENGTH)
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="valid", type="boolean"),
     *                 @OA\Property(property="code", type="string", nullable=true, enum=OrangeHRM\Entity\I18NError::ERROR_MAP),
     *                 @OA\Property(property="message", type="string", nullable=true),
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $langStringId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_LANG_STRING_ID
        );
        $translation = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_TRANSLATION
        );

        $validationError = !is_null($translation) ?
            $this->getLocalizationService()->validateTargetString($langStringId, $translation) :
            null;

        return new EndpointResourceResult(
            ArrayModel::class,
            [
                "valid" => is_null($validationError),
                "code" => !is_null($validationError) ? $validationError->getName() : null,
                "message" => !is_null($validationError) ? $validationError->getMessage() : null,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_LANG_STRING_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [I18NLangString::class])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_TRANSLATION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TRANSLATION_MAX_LENGTH])
                )
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
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
