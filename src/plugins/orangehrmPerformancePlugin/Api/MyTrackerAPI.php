<?php

namespace OrangeHRM\Performance\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Performance\Api\Model\PerformanceTrackerModel;
use OrangeHRM\Performance\Dto\PerformanceTrackerSearchFilterParams;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerServiceTrait;

class MyTrackerAPI extends Endpoint implements CrudEndpoint
{
    use AuthUserTrait;
    use DateTimeHelperTrait;
    use PerformanceTrackerServiceTrait;

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $performanceTrackerSearchParamHolder = new PerformanceTrackerSearchFilterParams();
        $this->setSortingAndPaginationParams($performanceTrackerSearchParamHolder);

        $performanceTrackerSearchParamHolder->setEmpNumber(
            $this->getAuthUser()->getEmpNumber()
        );
        $performanceTrackers = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->getPerformanceTrackList($performanceTrackerSearchParamHolder);
        $count = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->getPerformanceTrackerCount($performanceTrackerSearchParamHolder);
        return new EndpointCollectionResult(
            PerformanceTrackerModel::class,
            $performanceTrackers,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(...$this->getSortingAndPaginationParamsRules(PerformanceTrackerSearchFilterParams::ALLOWED_SORT_FIELDS));
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
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
