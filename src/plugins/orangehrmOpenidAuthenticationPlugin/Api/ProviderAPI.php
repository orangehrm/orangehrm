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

namespace OrangeHRM\OpenidAuthentication\Api;

use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Utility\EncryptionHelperTrait;
use OrangeHRM\Entity\AuthProviderExtraDetails;
use OrangeHRM\Entity\OpenIdProvider;
use OrangeHRM\OpenidAuthentication\Api\Model\ProviderModel;
use OrangeHRM\OpenidAuthentication\Dto\ProviderSearchFilterParams;
use OrangeHRM\OpenidAuthentication\Traits\Service\SocialMediaAuthenticationServiceTrait;
use OrangeHRM\ORM\Exception\TransactionException;

class ProviderAPI extends Endpoint implements CrudEndpoint
{
    use EncryptionHelperTrait;
    use EntityManagerHelperTrait;
    use SocialMediaAuthenticationServiceTrait;

    public const PARAMETER_NAME = 'name';
    public const PARAMETER_STATUS = 'status';
    public const PARAMETER_URL = 'url';
    public const CLIENT_ID = 'clientId';
    public const CLIENT_SECRET = 'clientSecret';
    public const PARAM_RULE_NAME_MAX_LENGTH = 40;
    public const PARAM_RULE_PROVIDER_URL_MAX_LENGTH = 2000;
    public const PARAM_RULE_CLIENT_ID_MAX_LENGTH = 255;
    public const PARAM_RULE_CLIENT_SECRET_MAX_LENGTH = 255;

    /**
     * @OA\Get(
     *     path="/api/v2/auth/openid-providers",
     *     tags={"OpenIDConnect/openid-providers"},
     *     summary="List All OpenID Providers",
     *     operationId="list-all-openid-providers",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=ProviderSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/OpenIdConnect-ProviderModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     */
    public function getAll(): EndpointResult
    {
        $providerSearchFilterParams = new ProviderSearchFilterParams();
        $this->setSortingAndPaginationParams($providerSearchFilterParams);
        $providerSearchFilterParams->setName(
            $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_NAME)
        );
        $providerSearchFilterParams->setStatus(
            $this->getRequestParams()
                ->getBooleanOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_STATUS, true)
        );
        $providerSearchFilterParams->setId(
            $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_QUERY, CommonParams::PARAMETER_ID)
        );

        $providers = $this->getSocialMediaAuthenticationService()->getAuthProviderDao()->getAuthProviders(
            $providerSearchFilterParams
        );

        $count = $this->getSocialMediaAuthenticationService()->getAuthProviderDao()->getAuthProviderCount(
            $providerSearchFilterParams
        );

        return new EndpointCollectionResult(
            ProviderModel::class,
            $providers,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_STATUS,
                    new Rule(Rules::BOOL_TYPE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            ...$this->getSortingAndPaginationParamsRules(ProviderSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/auth/openid-providers",
     *     tags={"OpenIDConnect/openid-providers"},
     *     summary="Create OpenID Provider",
     *     operationId="create-openid-provider",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="url", type="string"),
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="clientId", type="string"),
     *             @OA\Property(property="clientSecret", type="string"),
     *             required={"name", "url", "clientId", "clientSecret"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/OpenIdConnect-ProviderModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $openIdProvider = new OpenIdProvider();
            $this->saveProvider($openIdProvider);

            $authProviderExtraDetails = $this->saveProviderExtraDetails($openIdProvider);

            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
        return new EndpointResourceResult(ProviderModel::class, $authProviderExtraDetails);
    }

    /**
     * @param OpenIdProvider $openIdProvider
     */
    private function saveProvider(OpenIdProvider $openIdProvider): void
    {
        $openIdProvider->setProviderName(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME)
        );
        $openIdProvider->setProviderUrl(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_URL)
        );
        $openIdProvider->setStatus(
            $this->getRequestParams()
                ->getBooleanOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STATUS, true)
        );

        $this->getSocialMediaAuthenticationService()->getAuthProviderDao()->saveProvider($openIdProvider);
    }

    /**
     * @param OpenIdProvider $openIdProvider
     * @return AuthProviderExtraDetails
     */
    private function saveProviderExtraDetails(OpenIdProvider $openIdProvider): AuthProviderExtraDetails
    {
        $authProviderExtraDetails = $this->getSocialMediaAuthenticationService()->getAuthProviderDao()
            ->getAuthProviderDetailsByProviderId($openIdProvider->getId());
        if (!$authProviderExtraDetails instanceof AuthProviderExtraDetails) {
            $authProviderExtraDetails = new AuthProviderExtraDetails();
        }

        $authProviderExtraDetails->setClientId(
            $openIdProvider->getId()
        );
        $authProviderExtraDetails->setOpenIdProvider(
            $openIdProvider
        );
        $authProviderExtraDetails->setClientId(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::CLIENT_ID)
        );

        $clientSecret = $this->getRequestParams()
            ->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::CLIENT_SECRET);

        if (!is_null($clientSecret)) {
            if (self::encryptionEnabled()) {
                $clientSecret = self::getCryptographer()->encrypt($clientSecret);
            }
            $authProviderExtraDetails->setClientSecret($clientSecret);
        }

        return $this->getSocialMediaAuthenticationService()->getAuthProviderDao()->saveAuthProviderExtraDetails(
            $authProviderExtraDetails
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                $this->getNameRule($this->getOpenIdProviderCommonUniqueOption()),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_URL,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null , self::PARAM_RULE_PROVIDER_URL_MAX_LENGTH])
                ),
                true
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::CLIENT_ID,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_CLIENT_ID_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::CLIENT_SECRET,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_CLIENT_SECRET_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_STATUS,
                    new Rule(Rules::BOOL_TYPE)
                )
            ),
        );
    }

    /**
     * @param bool $update
     * @return ParamRule
     * @throws InvalidParamException
     */
    protected function getNameRule(?EntityUniquePropertyOption $uniqueOption = null): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_NAME,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
            new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [OpenIdProvider::class, 'providerName', $uniqueOption])
        );
    }

    /**
     * @return EntityUniquePropertyOption
     */
    private function getOpenIdProviderCommonUniqueOption(): EntityUniquePropertyOption
    {
        $uniqueOption = new EntityUniquePropertyOption();
        $uniqueOption->setIgnoreValues(
            ['status' => false]
        );
        return $uniqueOption;
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/auth/openid-providers",
     *     tags={"OpenIDConnect/openid-providers"},
     *     summary="Update OpenID Providers",
     *     operationId="update-openid-provider",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse")
     * )
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getSocialMediaAuthenticationService()->getAuthProviderDao()->deleteProviders($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::INT_ARRAY)
            ),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/auth/openid-providers/{id}",
     *     tags={"OpenIDConnect/openid-providers"},
     *     summary="Get an OpenID Provider",
     *     operationId="list-a-openid-provider",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/OpenIdConnect-ProviderModel"
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @throws InvalidParamException
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $openIdProvider = $this->getSocialMediaAuthenticationService()->getAuthProviderDao()->getAuthProviderDetailsByProviderId($id);
        $this->throwRecordNotFoundExceptionIfNotExist($openIdProvider, AuthProviderExtraDetails::class);

        return new EndpointResourceResult(
            ProviderModel::class,
            $openIdProvider,
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/auth/openid-providers/{id}",
     *     tags={"OpenIDConnect/openid-providers"},
     *     summary="Update a OpenID Provider",
     *     operationId="update-a-openid-provider",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="url", type="string"),
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="clientId", type="string"),
     *             @OA\Property(property="clientSecret", type="string"),
     *             required={"name", "url", "clientId", "clientSecret"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/OpenIdConnect-ProviderModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @throws InvalidParamException
     */
    public function update(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
            $openIdProvider = $this->getSocialMediaAuthenticationService()->getAuthProviderDao()
                ->getAuthProviderById($id);

            if (!$openIdProvider instanceof OpenIdProvider) {
                throw $this->getInvalidParamException(CommonParams::PARAMETER_ID);
            }

            $name = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
            if ($name == null) {
                throw $this->getInvalidParamException(self::PARAMETER_NAME);
            }

            $this->saveProvider($openIdProvider);
            $authProviderExtraDetails = $this->saveProviderExtraDetails($openIdProvider);

            $this->commitTransaction();
        } catch (InvalidParamException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
        return new EndpointResourceResult(ProviderModel::class, $authProviderExtraDetails);
    }

    /**
     * @inheritDoc
     * @throws InvalidParamException
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $uniqueOption = $this->getOpenIdProviderCommonUniqueOption();
        $uniqueOption->setIgnoreId($this->getAttributeId());

        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                $this->getNameRule($uniqueOption),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_URL,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null , self::PARAM_RULE_PROVIDER_URL_MAX_LENGTH])
                ),
                true
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::CLIENT_ID,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_CLIENT_ID_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::CLIENT_SECRET,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_CLIENT_SECRET_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_STATUS,
                    new Rule(Rules::BOOL_TYPE)
                )
            ),
        );
    }
}
