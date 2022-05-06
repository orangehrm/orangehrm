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

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(...$this->getSortingAndPaginationParamsRules(PerformanceTrackerSearchFilterParams::ALLOWED_SORT_FIELDS));
    }

    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function getOne(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
