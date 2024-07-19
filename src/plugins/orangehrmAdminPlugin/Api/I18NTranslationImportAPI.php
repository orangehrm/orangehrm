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
use OpenApi\Annotations as OA;
use OrangeHRM\Admin\Exception\XliffFileProcessFailedException;
use OrangeHRM\Admin\Traits\Service\LocalizationServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Dto\Base64Attachment;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Core\Api\V2\ParameterBag;

class I18NTranslationImportAPI extends Endpoint implements CollectionEndpoint
{
    use LocalizationServiceTrait;
    use EntityManagerHelperTrait;
    use AuthUserTrait;

    public const PARAMETER_LANGUAGE_ID = 'languageId';
    public const PARAMETER_ATTACHMENT = 'attachment';

    public const PARAM_RULE_IMPORT_FILE_FORMAT = ['text/xml', 'application/xml', 'application/xliff+xml'];
    public const PARAM_RULE_IMPORT_FILE_EXTENSIONS = ['xml', 'xlf'];

    public const PARAMETER_SUCCESS = 'success';
    public const PARAMETER_FAILED = 'failed';
    public const PARAMETER_SKIPPED = 'skipped';

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
     *     @OA\PathParameter(
     *         name="languageId",
     *         @OA\Schema(type="integer")
     *     ),
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
     *                 @OA\Property(property="success", description="The number of lang strings that successfully imported", type="integer"),
     *                 @OA\Property(property="failed", description="The number of lang strings that failed to import", type="integer"),
     *                 @OA\Property(property="skipped", description="The number of lang strings that were skipped", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
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
            list(
                $validLangStrings,
                $invalidLangStrings,
                $skippedLangStrings
            ) = $this->getLocalizationService()->processXliffFile($attachment, $languageId);

            $this->getLocalizationService()->saveAndUpdateTranslatedStringsFromRows(
                $languageId,
                $validLangStrings
            );
            $this->getLocalizationService()->getLocalizationDao()->clearImportErrorsForLanguageAndEmpNumber(
                $languageId,
                $this->getAuthUser()->getEmpNumber()
            );
            $this->getLocalizationService()->saveImportErrorLangStringsFromRows(
                $languageId,
                $this->getAuthUser()->getEmpNumber(),
                $invalidLangStrings
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
                self::PARAMETER_SUCCESS => count($validLangStrings),
                self::PARAMETER_FAILED => count($invalidLangStrings),
                self::PARAMETER_SKIPPED => count($skippedLangStrings),
                CommonParams::PARAMETER_TOTAL => count($validLangStrings) + count($invalidLangStrings) + count($skippedLangStrings)
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
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [I18NLanguage::class])
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
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     *
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
