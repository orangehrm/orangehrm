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

namespace OrangeHRM\Maintenance\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Maintenance\Api\Model\PurgeCandidateListModel;
use OrangeHRM\Maintenance\Api\Model\PurgeCandidateModel;
use OrangeHRM\Maintenance\Service\PurgeService;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Recruitment\Dto\CandidateSearchFilterParams;
use OrangeHRM\Recruitment\Traits\Service\CandidateServiceTrait;

class PurgeCandidateAPI extends Endpoint implements CollectionEndpoint
{
    use CandidateServiceTrait;

    private ?PurgeService $purgeService = null;

    public const PARAMETER_VACANCY_ID = 'vacancyId';

    /**
     * @return PurgeService
     */
    public function getPurgeService(): PurgeService
    {
        if (is_null($this->purgeService)) {
            $this->purgeService = new PurgeService();
        }
        return $this->purgeService;
    }

    /**
     * @inheritDoc
     * @throws TransactionException
     */
    public function delete(): EndpointResult
    {
        $vacancyId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_VACANCY_ID
        );
        $this->getPurgeService()->purgeCandidateData($vacancyId);
        return new EndpointResourceResult(PurgeCandidateModel::class, $vacancyId);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_VACANCY_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [Vacancy::class])
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $candidateSearchFilterParamHolder = new CandidateSearchFilterParams();

        $candidateSearchFilterParamHolder->setVacancyId(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_QUERY,
                self::PARAMETER_VACANCY_ID
            )
        );
        $candidateSearchFilterParamHolder->setConsentToKeepData(false);

        $this->setSortingAndPaginationParams($candidateSearchFilterParamHolder);

        $candidates = $this->getCandidateService()->getCandidateDao()->getCandidatesList($candidateSearchFilterParamHolder);
        $count = $this->getCandidateService()->getCandidateDao()->getCandidatesCount($candidateSearchFilterParamHolder);

        return new EndpointCollectionResult(
            PurgeCandidateListModel::class,
            $candidates,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_VACANCY_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [Vacancy::class])
            ),
            ...$this->getSortingAndPaginationParamsRules([])
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
