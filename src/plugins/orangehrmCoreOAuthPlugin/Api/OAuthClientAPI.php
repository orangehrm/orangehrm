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

namespace OrangeHRM\OAuth\Api;

use OpenApi\Annotations as OA;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Core\Utility\PasswordHash;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\OAuth\Api\Model\OAuthClientModel;
use OrangeHRM\OAuth\Dto\OAuthClientSearchFilterParams;
use OrangeHRM\OAuth\Traits\OAuthServiceTrait;

class OAuthClientAPI extends Endpoint implements CrudEndpoint
{
    use OAuthServiceTrait;

    public const PARAMETER_NAME = 'name';
    public const PARAMETER_REDIRECT_URI = 'redirectUri';
    public const PARAMETER_ENABLED = 'enabled';
    public const PARAMETER_CONFIDENTIAL = 'confidential';
    public const PARAMETER_CLIENT_SECRET = 'clientSecret';

    public const PARAM_RULE_NAME_MAX_LENGTH = 80;
    public const PARAM_RULE_REDIRECT_URI_MAX_LENGTH = 2000;

    /**
     * @OA\Get(
     *     path="/api/v2/admin/oauth-clients",
     *     tags={"OAuth/OAuth Clients"},
     *     summary="List All OAuth Clients",
     *     operationId="list-all-oauth-clients",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=OAuthClientSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/OAuth-OAuthClientModel")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $oauthClientSearchFilterParams = new OAuthClientSearchFilterParams();
        $this->setSortingAndPaginationParams($oauthClientSearchFilterParams);

        $oauthClients = $this->getOAuthService()->getOAuthClientDao()->getOAuthClientList($oauthClientSearchFilterParams);
        $count = $this->getOAuthService()->getOAuthClientDao()->getOAuthClientCount($oauthClientSearchFilterParams);

        return new EndpointCollectionResult(
            OAuthClientModel::class,
            $oauthClients,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(OAuthClientSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/admin/oauth-clients",
     *     tags={"OAuth/OAuth Clients"},
     *     summary="Create an OAuth Client",
     *     operationId="create-an-oauth-client",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="redirectUri", type="string"),
     *             @OA\Property(property="enabled", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/OAuth-OAuthClientModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $oauthClient = new OAuthClient();
        $this->setOAuthClient($oauthClient);
        $oauthClient->setClientId(bin2hex(random_bytes(16)));
        $secret = null;
        if ($oauthClient->isConfidential()) {
            $secret = $this->generateSecret();
            $passwordHasher = new PasswordHash();
            $oauthClient->setClientSecret($passwordHasher->hash($secret));
        }

        $oauthClient = $this->getOAuthService()->getOAuthClientDao()->saveOAuthClient($oauthClient);
        return new EndpointResourceResult(
            OAuthClientModel::class,
            $oauthClient,
            new ParameterBag([self::PARAMETER_CLIENT_SECRET => $secret])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @param EntityUniquePropertyOption|null $uniqueOption
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(?EntityUniquePropertyOption $uniqueOption = null): array
    {
        return [
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::REQUIRED),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
                new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [OAuthClient::class, 'name', $uniqueOption])
            ),
            new ParamRule(
                self::PARAMETER_REDIRECT_URI,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_REDIRECT_URI_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_ENABLED,
                new Rule(Rules::BOOL_VAL)
            ),
            new ParamRule(
                self::PARAMETER_CONFIDENTIAL,
                new Rule(Rules::BOOL_VAL)
            ),
        ];
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/admin/oauth-clients",
     *     tags={"OAuth/OAuth Clients"},
     *     summary="Delete OAuth Clients",
     *     operationId="delete-oauth-clients",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getOAuthService()->getOAuthClientDao()->deleteOAuthClients($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        $mobileClientId = $this->getOAuthService()->getMobileClientId();
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::INT_ARRAY),
                new Rule(
                    Rules::EACH,
                    [
                        new Rules\Composite\AllOf(
                            new Rule(Rules::NOT_IN, [[$mobileClientId]])
                        )
                    ]
                )
            ),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/oauth-client/{id}",
     *     tags={"OAuth/OAuth Clients"},
     *     summary="Get an OAuth Client",
     *     operationId="get-an-oauth-client",
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
     *                 ref="#/components/schemas/OAuth-OAuthClientModel"
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $oauthClient = $this->getOAuthService()->getOAuthClientDao()->getOAuthClientById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($oauthClient, OAuthClient::class);

        return new EndpointResourceResult(OAuthClientModel::class, $oauthClient);
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
     *     path="/api/v2/admin/oauth-client/{id}",
     *     tags={"OAuth/OAuth Clients"},
     *     summary="Update an OAuth Client",
     *     operationId="update-an-oauth-client",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="redirectUri", type="string"),
     *             @OA\Property(property="enabled", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/OAuth-OAuthClientModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $oauthClient = $this->getOAuthService()->getOAuthClientDao()->getOAuthClientById($id);

        $this->throwRecordNotFoundExceptionIfNotExist($oauthClient, OAuthClient::class);
        $currentOAuthClient = clone $oauthClient;
        $this->setOAuthClient($oauthClient);

        $secret = null;
        // state changing
        if ($oauthClient->isConfidential() && !$currentOAuthClient->isConfidential()) {
            $secret = $this->generateSecret();
            $passwordHasher = new PasswordHash();
            $oauthClient->setClientSecret($passwordHasher->hash($secret));
        } elseif (!$oauthClient->isConfidential() && $currentOAuthClient->isConfidential()) {
            $oauthClient->setClientSecret(null);
        } // else `confidential` state not changed

        $oauthClient = $this->getOAuthService()->getOAuthClientDao()->saveOAuthClient($oauthClient);
        return new EndpointResourceResult(
            OAuthClientModel::class,
            $oauthClient,
            new ParameterBag([self::PARAMETER_CLIENT_SECRET => $secret])
        );
    }

    /**
     * @return string
     */
    protected function generateSecret(): string
    {
        return base64_encode(random_bytes(32));
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $uniqueOption = new EntityUniquePropertyOption();
        $uniqueOption->setIgnoreId($this->getAttributeId());

        $mobileClientId = $this->getOAuthService()->getMobileClientId();

        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::NOT_IN, [[$mobileClientId]])
            ),
            ...$this->getCommonBodyValidationRules($uniqueOption),
        );
    }

    /**
     * @param OAuthClient $oauthClient
     */
    public function setOAuthClient(OAuthClient $oauthClient): void
    {
        $oauthClient->setName(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NAME
            )
        );

        $oauthClient->setRedirectUri(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_REDIRECT_URI
            )
        );

        $oauthClient->setEnabled(
            $this->getRequestParams()->getBoolean(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_ENABLED,
                true
            )
        );

        $oauthClient->setConfidential(
            $this->getRequestParams()->getBoolean(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_CONFIDENTIAL,
                true
            )
        );
    }
}
