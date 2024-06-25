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

use Exception;
use OrangeHRM\Admin\Traits\Service\LocalizationServiceTrait;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\ORM\Exception\TransactionException;

class I18NTranslationBulkAPI extends Endpoint implements ResourceEndpoint
{
    use EntityManagerHelperTrait;
    use LocalizationServiceTrait;

    public const PARAMETER_LANGUAGE_ID = 'languageId';
    public const PARAMETER_DATA = 'data';
    public const PARAMETER_TRANSLATED_VALUE = 'translatedValue';
    public const PARAMETER_LANG_STRING_ID = 'langStringId';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @OA\Put(
     *     path="/api/v2/admin/i18n/languages/{languageId}/translations/bulk",
     *     tags={"Admin/I18N"},
     *     summary="Bulk Update I18N Translations",
     *     operationId="bulk-update-i18n-translations",
     *     @OA\PathParameter(
     *         name="languageId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="langStringId", type="integer"),
     *                     @OA\Property(property="translatedValue", type="string")
     *                 ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-LanguageModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @throws TransactionException
     */
    public function update(): EndpointCollectionResult
    {
        $this->beginTransaction();
        try {
            $languageId = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_LANGUAGE_ID
            );

            $translatedDataValues = $this->getRequestParams()->getArray(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DATA
            );

            $this->getLocalizationService()
                ->saveAndUpdateTranslatedStringsFromRows($languageId, $translatedDataValues);

            $this->getLocalizationService()
                ->clearImportErrorsForLangStrings($languageId, $translatedDataValues);

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }

        return new EndpointCollectionResult(ArrayModel::class, []);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_LANGUAGE_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_DATA,
                new Rule(Rules::ARRAY_TYPE),
                new Rule(
                    Rules::EACH,
                    [
                        new Rules\Composite\AllOf(
                            new Rule(
                                Rules::KEY,
                                [
                                    self::PARAMETER_LANG_STRING_ID,
                                    new Rules\Composite\AllOf(new Rule(Rules::POSITIVE))
                                ]
                            ),
                            new Rule(
                                Rules::KEY,
                                [
                                    self::PARAMETER_TRANSLATED_VALUE,
                                    new Rules\Composite\OneOf(
                                        new Rule(Rules::NOT_REQUIRED, [true]),
                                        new Rule(Rules::STRING_TYPE)
                                    )
                                ]
                            ),
                        )
                    ]
                )
            ),
        );
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
