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

namespace OrangeHRM\Recruitment\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
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
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateHistory;
use OrangeHRM\Recruitment\Api\Model\CandidateHistoryDetailedModel;
use OrangeHRM\Recruitment\Api\Model\CandidateHistoryListModel;
use OrangeHRM\Recruitment\Dto\CandidateHistorySearchFilterParams;
use OrangeHRM\Recruitment\Traits\Service\CandidateServiceTrait;

class CandidateHistoryAPI extends Endpoint implements CrudEndpoint
{
    use CandidateServiceTrait;

    public const PARAMETER_CANDIDATE_ID = 'candidateId';
    public const PARAMETER_HISTORY_ID = 'historyId';
    public const PARAMETER_NOTE = 'note';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $candidateHistorySearchFilterParams = new CandidateHistorySearchFilterParams();
        $this->setSortingAndPaginationParams($candidateHistorySearchFilterParams);
        $candidateHistorySearchFilterParams->setCandidateId($this->getCandidateId());

        $candidateHistoryRecords = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateHistoryRecords($candidateHistorySearchFilterParams);

        $count = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateHistoryRecordsCount($candidateHistorySearchFilterParams);

        return new EndpointCollectionResult(
            CandidateHistoryListModel::class,
            $candidateHistoryRecords,
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
                self::PARAMETER_CANDIDATE_ID,
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Candidate::class])
            ),
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

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $candidateHistoryRecord = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateHistoryRecordByCandidateIdAndHistoryId($this->getCandidateId(), $this->getHistoryId());

        $this->throwRecordNotFoundExceptionIfNotExist($candidateHistoryRecord, CandidateHistory::class);
        return new EndpointResourceResult(CandidateHistoryDetailedModel::class, $candidateHistoryRecord);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_CANDIDATE_ID,
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Candidate::class])
            ),
            new ParamRule(
                self::PARAMETER_HISTORY_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $candidateVacancy = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateVacancyByCandidateId($this->getCandidateId());

        if (is_null($candidateVacancy)) {
            throw $this->getForbiddenException();
        }

        $candidateHistoryRecord = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateHistoryRecordByCandidateIdAndHistoryId($this->getCandidateId(), $this->getHistoryId());

        $this->throwRecordNotFoundExceptionIfNotExist($candidateHistoryRecord, CandidateHistory::class);

        $candidateHistoryRecord->setNote(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NOTE
            )
        );
        $this->getCandidateService()->getCandidateDao()->saveCandidateHistory($candidateHistoryRecord);
        return new EndpointResourceResult(CandidateHistoryDetailedModel::class, $candidateHistoryRecord);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_CANDIDATE_ID,
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Candidate::class])
            ),
            new ParamRule(
                self::PARAMETER_HISTORY_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NOTE,
                    new Rule(Rules::STRING_TYPE)
                ),
            )
        );
    }

    /**
     * @return int
     */
    private function getCandidateId(): int
    {
        return $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_CANDIDATE_ID
        );
    }

    /**
     * @return int
     */
    private function getHistoryId(): int
    {
        return $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_HISTORY_ID
        );
    }
}
