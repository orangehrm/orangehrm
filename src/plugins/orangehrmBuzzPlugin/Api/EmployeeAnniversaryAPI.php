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

namespace OrangeHRM\Buzz\Api;

use DateInterval;
use OrangeHRM\Buzz\Api\Model\EmployeeAnniversaryModel;
use OrangeHRM\Buzz\Traits\Service\BuzzAnniversaryServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Buzz\Dto\EmployeeAnniversarySearchFilterParams;

class EmployeeAnniversaryAPI extends Endpoint implements CollectionEndpoint
{
    use BuzzAnniversaryServiceTrait;
    use DateTimeHelperTrait;

    public const DATE_DIFFERENCE_MIN = 0;
    public const DATE_DIFFERENCE_MAX = 30;

    /**
     * @OA\Get(
     *     path="/api/v2/buzz/anniversaries",
     *     tags={"Buzz/Employee Anniversary"},
     *     summary="List Upcoming Employee Anniversaries",
     *     operationId="list-upcoming-employees-anniversaries",
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
     *                 @OA\Items(ref="#/components/schemas/Buzz-EmployeeAnniversaryModel")
     *             ),
     *             @OA\Property(property="meta",
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
        $employeeAnniversarySearchFilterParams = new EmployeeAnniversarySearchFilterParams();
        $this->setSortingAndPaginationParams($employeeAnniversarySearchFilterParams);

        $thisYear = $this->getDateTimeHelper()->getNow()->format('Y');
        $nextDate =  $this->getDateTimeHelper()->getNow();
        $nextDate->add(new DateInterval('P30D'));

        $employeeAnniversarySearchFilterParams->setThisYear($thisYear);
        $employeeAnniversarySearchFilterParams->setNextDate($nextDate);
        $employeeAnniversarySearchFilterParams->setDateDiffMin(self::DATE_DIFFERENCE_MIN);
        $employeeAnniversarySearchFilterParams->setDateDiffMax(self::DATE_DIFFERENCE_MAX);

        $upcomingAnniversaries = $this->getBuzzAnniversaryService()->getBuzzAnniversaryDao()
            ->getUpcomingAnniversariesList($employeeAnniversarySearchFilterParams);

        $count = $this->getBuzzAnniversaryService()
            ->getBuzzAnniversaryDao()
            ->getUpcomingAnniversariesCount($employeeAnniversarySearchFilterParams);

        return new EndpointCollectionResult(
            EmployeeAnniversaryModel::class,
            $upcomingAnniversaries,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules()
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
}
