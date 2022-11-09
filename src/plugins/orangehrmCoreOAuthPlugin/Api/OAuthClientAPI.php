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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\OAuth\Api\Model\OAuthClientModel;
use OrangeHRM\OAuth\Constant\GrantType;
use OrangeHRM\OAuth\Constant\Scope;
use OrangeHRM\OAuth\Dto\OAuthClientSearchFilterParams;
use OrangeHRM\OAuth\Service\OAuthService;

class OAuthClientAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_CLIENT_ID = 'clientId';
    public const PARAMETER_CLIENT_SECRET = 'clientSecret';
    public const PARAMETER_REDIRECT_URI = 'redirectUri';

    public const PARAM_RULE_CLIENT_ID_MAX_LENGTH = 80;
    public const PARAM_RULE_CLIENT_SECRET_MAX_LENGTH = 80;
    public const PARAM_RULE_REDIRECT_URI_MAX_LENGTH = 2000;

    /**
     * @var null|OAuthService
     */
    protected ?OAuthService $oAuthService = null;


    /**
     * @return OAuthService
     */
    public function getOAuthService(): OAuthService
    {
        if (is_null($this->oAuthService)) {
            $this->oAuthService = new OAuthService();
        }
        return $this->oAuthService;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $oAuthClientSearchFilterParams = new OAuthClientSearchFilterParams();
        $this->setSortingAndPaginationParams($oAuthClientSearchFilterParams);

        $count = $this->getOAuthService()->getOAuthClientDao()->getOAuthClientsCount($oAuthClientSearchFilterParams);

        $oauthClients = $this->getOAuthService()->getOAuthClientDao()->getOAuthClients($oAuthClientSearchFilterParams);
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
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $oAuthClient = $this->saveOAuthClient();

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
                self::PARAMETER_CLIENT_ID,
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
        ];
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getOAuthService()->deleteOAuthClients($ids);
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
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_CLIENT_ID);
        $oAuthClient = $this->getOAuthService()->getOAuthClientByClientId($id);
        $this->throwRecordNotFoundExceptionIfNotExist($oAuthClient, OAuthClient::class);

        return new EndpointResourceResult(OAuthClientModel::class, $oAuthClient);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_CLIENT_ID),
        );
    }

    /**
     * @inheritDoc
     * @throws DaoException
     */
    public function update(): EndpointResult
    {
        $oAuthClient = $this->saveOAuthClient();

        return new EndpointResourceResult(OAuthClientModel::class, $oAuthClient);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return OAuthClient
     * @throws RecordNotFoundException|DaoException
     */
    public function saveOAuthClient(): OAuthClient
    {
        $clientId = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CLIENT_ID);
        $existingClientId = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_CLIENT_ID
        );
        $clientSecret = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CLIENT_SECRET
        );
        $redirectUri = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_REDIRECT_URI
        );
        if (!empty($existingClientId)) {
            $oAuthClient = $this->getOAuthService()->getOAuthClientByClientId($existingClientId);
            $this->throwRecordNotFoundExceptionIfNotExist($oAuthClient, OAuthClient::class);
        } else {
            $oAuthClient = new OAuthClient();
            $oAuthClient->setGrantTypes(GrantType::CLIENT_CREDENTIALS);
            $oAuthClient->setScope(Scope::SCOPE_ADMIN);
        }

        $oAuthClient->setClientId($clientId);
        $oAuthClient->setClientSecret($clientSecret);
        $oAuthClient->setRedirectUri($redirectUri);
        return $this->getOAuthService()->saveOAuthClient($oAuthClient);
    }
}
