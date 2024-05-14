<?php
/*
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
use OpenApi\Annotations as OA;
use OrangeHRM\Admin\Dto\I18NTranslationSearchFilterParams;
use OrangeHRM\Admin\Exception\XliffFileProcessFailedException;
use OrangeHRM\Admin\Traits\Service\LocalizationServiceTrait;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\CacheTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Core\Api\V2\ParameterBag;
use Psr\Cache\InvalidArgumentException;

class I18NTranslationImportAPI extends Endpoint implements CollectionEndpoint
{
    use LocalizationServiceTrait;
    use EntityManagerHelperTrait;
    use CacheTrait;

    public const PARAMETER_LANGUAGE_ID = 'languageId';
    public const PARAMETER_ATTACHMENT = 'attachment';

    public const PARAM_RULE_IMPORT_FILE_FORMAT = ['text/xml', 'application/xml', 'application/xliff+xml'];
    public const PARAM_RULE_IMPORT_FILE_EXTENSIONS = ['xml', 'xlf'];

    public const XLIFF_FILE_ERRORS = 'xliffFileValidations';
    public const XLIFF_VALIDATION_ERRORS = 'xliffLanguageStringValidations';
    public const XLIFF_SUCCESS = 'successXliffLanguageStrings';
    public const CACHE_KEY_SUFFIX = 'admin.i18LanguageStringValidationErrors';

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
     *     path="/api/v2/admin/i18n/languages/{languageId}/import",
     *     tags={"Admin/I18N Language Import"},
     *     summary="Import I18N language",
     *     operationId="import-i18n-language",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="attachment", ref="#/components/schemas/Base64Attachment"),
     *             required={"attachment"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items()),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="xliffStringValidation", description="The language strings that failed to import", type="integer"),
     *                 @OA\Property(property="xliffFileValidation", description="The language file that failed to import")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     * @throws TransactionException
     * @throws InvalidParamException
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function create(): EndpointResult
    {
        $attachment = $this->getRequestParams()->getAttachment(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ATTACHMENT
        );
        $languageId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_LANGUAGE_ID
        );

        $this->beginTransaction();
        try {
            $xliffData = $this->processXliffData($attachment, $languageId);

            $this->saveLanguageStringValidationsToCache(
                $this->generateCacheKey($languageId),
                $xliffData['xliffSourceAndTargetValidationErrors']
            );

            $this->commitTransaction();
        } catch (XliffFileProcessFailedException $e) {
            $this->rollBackTransaction();
            throw new BadRequestException($e->getMessage());
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }

        return new EndpointResourceResult(
            ArrayModel::class,
            [],
            new ParameterBag([
                self::XLIFF_FILE_ERRORS => $xliffData['xliffFileSymfonyValidation'],
                self::XLIFF_VALIDATION_ERRORS => $xliffData['xliffSourceAndTargetValidationErrors'],
                self::XLIFF_SUCCESS => $xliffData['translatedDataValues'],
            ])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_LANGUAGE_ID,
                new Rule(
                    Rules::POSITIVE
                )
            ),
            new ParamRule(
                self::PARAMETER_ATTACHMENT,
                new Rule(
                    Rules::BASE_64_ATTACHMENT,
                    [self::PARAM_RULE_IMPORT_FILE_FORMAT, self::PARAM_RULE_IMPORT_FILE_EXTENSIONS]
                )
            )
        );
    }

    /**
     *
     * @throws NotImplementedException
     */
    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     *
     * @throws NotImplementedException
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     *
     * @throws NotImplementedException
     */
    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     *
     * @throws NotImplementedException
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @param $attachment
     * @param $languageId
     * @return array
     */
    private function processXliffData($attachment, $languageId): array
    {
        $xliffFileSymfonyValidation = $this->xliffFileValidation($attachment);
        $xliffSourceAndTargetValidationErrors = [];
        $translatedDataValues = [];

        if ($xliffFileSymfonyValidation && $xliffFileSymfonyValidation['isValid']) {
            $xliffDocument = new \DOMDocument();
            $xliffDocument->loadXML($attachment->getContent());

            $units = $xliffDocument->getElementsByTagName('unit');

            $languageStrings = $this->getLanguageStrings($languageId);

            $documentDataValues = [];

            foreach ($units as $unit) {
                $unitId = $unit->getAttribute('id');
                $source = $unit->getElementsByTagName('source')->item(0)->nodeValue;
                $target = $unit->getElementsByTagName('target')->item(0)->nodeValue;

                $unitElement = [
                    'unitId' => $unitId,
                    'source' => $source,
                    'target' => $target
                ];

                $xliffSourceAndTargetValidation = $this->xliffSourceAndTargetValidation($unitElement);

                $xliffSourceAndTargetValidation
                    ? $xliffSourceAndTargetValidationErrors[] = $xliffSourceAndTargetValidation
                    : $documentDataValues[] = $unitElement;
            }

            json_encode($documentDataValues, JSON_PRETTY_PRINT);

            foreach ($languageStrings as $languageString) {
                foreach ($documentDataValues as $documentDataValue) {
                    if ($languageString["unitId"] === $documentDataValue["unitId"] && !empty($documentDataValue["target"])) {
                        $translatedDataValues[] = [
                            'langStringId' => $languageString["langStringId"],
                            'translatedValue' => $documentDataValue["target"]
                        ];
                    }
                }
            }

            if (!empty($translatedDataValues)) {
                $this->saveAndUpdateTranslatedStrings($languageId, $translatedDataValues);
            }
        }

        return [
            'xliffFileSymfonyValidation' => $xliffFileSymfonyValidation,
            'xliffSourceAndTargetValidationErrors' => $xliffSourceAndTargetValidationErrors,
            'translatedDataValues' => $translatedDataValues
        ];
    }

    /**
     * @param $attachment
     * @return array
     */
    private function xliffFileValidation($attachment): array
    {
        return $this->getLocalizationService()->validateXliffFile(
            $attachment->getContent()
        );
    }

    /**
     * @param $unitElement
     * @return array
     */
    private function xliffSourceAndTargetValidation($unitElement): array
    {
        return $this->getLocalizationService()->validateXliffLanguageStrings(
            $unitElement
        );
    }

    private function getLanguageStrings($languageId): array
    {
        $i18NTranslationSearchFilterParams = new I18NTranslationSearchFilterParams();
        $i18NTranslationSearchFilterParams->setLanguageId($languageId);
        return $this->getLocalizationService()->getLocalizationDao()->getNormalizedTranslationsForExport(
            $i18NTranslationSearchFilterParams
        );
    }

    private function saveAndUpdateTranslatedStrings($languageId, $translatedDataValues)
    {
        $this->getLocalizationService()->saveAndUpdateTranslatedStringsFromRows(
            $languageId,
            $translatedDataValues
        );
    }

    /**
     * @param $languageId
     * @return string
     */
    private function generateCacheKey($languageId): string
    {
        return self::CACHE_KEY_SUFFIX . ".$languageId";
    }

    /**
     * @throws InvalidArgumentException
     */
    private function saveLanguageStringValidationsToCache($key, $data)
    {
        $this->getCache()->get($key, function () use ($data) {
            return $data;
        });
    }
}
