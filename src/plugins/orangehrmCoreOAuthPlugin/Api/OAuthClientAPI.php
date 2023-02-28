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
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\OAuth\Api\Model\OAuthClientModel;
use OrangeHRM\OAuth\Dto\OAuthClientSearchFilterParams;
use OrangeHRM\OAuth\Traits\OAuthServiceTrait;

class OAuthClientAPI extends Endpoint implements CrudEndpoint
{
    use OAuthServiceTrait;

    public const PARAMETER_NAME = 'name';
    public const PARAMETER_CLIENT_SECRET = 'clientSecret';
    public const PARAMETER_REDIRECT_URI = 'redirectUri';
    public const PARAMETER_ENABLED = 'enabled';

    public const PARAM_RULE_CLIENT_ID_MAX_LENGTH = 80;
    public const PARAM_RULE_CLIENT_SECRET_MAX_LENGTH = 80;
    public const PARAM_RULE_REDIRECT_URI_MAX_LENGTH = 2000;

    /**
     * @OA\Get(
     *     path="/api/v2/admin/oauth-clients",
     *     tags={"OAuth/OAuth Clients"},
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
        $oAuthClientSearchFilterParams = new OAuthClientSearchFilterParams();
        $this->setSortingAndPaginationParams($oAuthClientSearchFilterParams);

        $oauthClients = $this->getOAuthService()->getOAuthClientDao()->getOAuthClientList($oAuthClientSearchFilterParams);
        $count = $this->getOAuthService()->getOAuthClientDao()->getOAuthClientCount($oAuthClientSearchFilterParams);

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
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="clientSecret", type="string"),
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
        $oAuthClient = new OAuthClient();
        $this->setOAuthClient($oAuthClient);

        $oAuthClient = $this->getOAuthService()->getOAuthClientDao()->saveOAuthClient($oAuthClient);
        return new EndpointResourceResult(OAuthClientModel::class, $oAuthClient);
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
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(): array
    {
        return [
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::REQUIRED),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_CLIENT_ID_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_CLIENT_SECRET,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::REQUIRED),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_CLIENT_SECRET_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_REDIRECT_URI,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_REDIRECT_URI_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_ENABLED,
                new Rule(Rules::BOOL_TYPE)
            ),
        ];
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/admin/oauth-clients",
     *     tags={"OAuth/OAuth Clients"},
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(ref="#/components/responses/DeleteResponse")
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
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/oauth-client",
     *     tags={"OAuth/OAuth Clients"},
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
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $oAuthClient = $this->getOAuthService()->getOAuthClientDao()->getOAuthClientById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($oAuthClient, OAuthClient::class);

        return new EndpointResourceResult(OAuthClientModel::class, $oAuthClient);
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
     *     path="/api/v2/admin/oauth-client",
     *     tags={"OAuth/OAuth Clients"},
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="clientSecret", type="string"),
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
        $oAuthClient = $this->getOAuthService()->getOAuthClientDao()->getOAuthClientById($id);

        $this->throwRecordNotFoundExceptionIfNotExist($oAuthClient, OAuthClient::class);
        $this->setOAuthClient($oAuthClient);

        $oAuthClient = $this->getOAuthService()->getOAuthClientDao()->saveOAuthClient($oAuthClient);
        return new EndpointResourceResult(OAuthClientModel::class, $oAuthClient);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @param OAuthClient $oAuthClient
     */
    public function setOAuthClient(OAuthClient $oAuthClient): void
    {
        $oAuthClient->setName(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NAME
            )
        );

        $oAuthClient->setClientSecret(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_CLIENT_SECRET,
            )
        );

        $oAuthClient->setRedirectUri(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_REDIRECT_URI
            )
        );

        $oAuthClient->setEnabled(
            $this->getRequestParams()->getBooleanOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_ENABLED
            )
        );

        $oAuthClient->setConfidential(false);
    }
}
