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

namespace OrangeHRM\Core\Api\V2;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Exception\EndpointExceptionTrait;
use OrangeHRM\Core\Api\V2\Validator\Helpers\ValidationDecorator;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\ORM\ListSorter;

abstract class Endpoint
{
    use EndpointExceptionTrait;

    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var RequestParams
     */
    private RequestParams $requestParams;

    /**
     * @var ValidationDecorator|null
     */
    private ?ValidationDecorator $validationDecorator = null;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->requestParams = new RequestParams($request);
        $this->init();
    }

    /**
     * Init lifecycle hook for child classes
     */
    protected function init()
    {
    }

    /**
     * @return Request
     */
    protected function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return RequestParams
     */
    protected function getRequestParams(): RequestParams
    {
        return $this->requestParams;
    }

    /**
     * @param RequestParams $requestParams
     * @internal
     */
    protected function setRequestParams(RequestParams $requestParams): void
    {
        $this->requestParams = $requestParams;
    }

    /**
     * @return ValidationDecorator
     */
    protected function getValidationDecorator(): ValidationDecorator
    {
        if (!$this->validationDecorator instanceof ValidationDecorator) {
            $this->validationDecorator = new ValidationDecorator();
        }
        return $this->validationDecorator;
    }

    /**
     * @param FilterParams $searchParamHolder
     * @param string|null $defaultSortField
     * @return FilterParams
     */
    protected function setSortingAndPaginationParams(
        FilterParams $searchParamHolder,
        ?string $defaultSortField = null
    ): FilterParams {
        $searchParamHolder->setSortField(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                CommonParams::PARAMETER_SORT_FIELD,
                $defaultSortField ?? $searchParamHolder->getSortField()
            )
        );
        $searchParamHolder->setSortOrder(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_QUERY,
                CommonParams::PARAMETER_SORT_ORDER,
                $searchParamHolder->getSortOrder()
            )
        );
        $searchParamHolder->setLimit(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_QUERY,
                CommonParams::PARAMETER_LIMIT,
                FilterParams::DEFAULT_LIMIT
            )
        );
        $searchParamHolder->setOffset(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_QUERY,
                CommonParams::PARAMETER_OFFSET,
                FilterParams::DEFAULT_OFFSET
            )
        );
        return $searchParamHolder;
    }

    /**
     * @param array $allowedSortFields
     * @param bool $excludeSortField
     * @return ParamRule[]
     */
    protected function getSortingAndPaginationParamsRules(
        array $allowedSortFields = [],
        bool $excludeSortField = false
    ): array {
        $rules = [
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_SORT_ORDER,
                    new Rule(Rules::IN, [[ListSorter::ASCENDING, ListSorter::DESCENDING]])
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_LIMIT,
                    new Rule(Rules::ZERO_OR_POSITIVE), // Zero for not to limit results
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_OFFSET,
                    new Rule(Rules::ZERO_OR_POSITIVE)
                )
            ),
        ];
        if (!$excludeSortField) {
            $rules[] = $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_SORT_FIELD,
                    new Rule(Rules::IN, [$allowedSortFields])
                )
            );
        }
        return $rules;
    }

    /**
     * @return int
     */
    public function getAttributeId(): int
    {
        return $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
    }
}
