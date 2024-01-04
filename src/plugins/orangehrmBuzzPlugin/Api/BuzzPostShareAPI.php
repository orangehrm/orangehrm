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

use OpenApi\Annotations as OA;
use OrangeHRM\Buzz\Api\Model\BuzzShareModel;
use OrangeHRM\Buzz\Dto\BuzzPostShareSearchFilterParams;
use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\BuzzShare;

class BuzzPostShareAPI extends Endpoint implements CollectionEndpoint
{
    use BuzzServiceTrait;

    public const PARAMETER_POST_ID = 'postId';

    /**
     * @OA\Get(
     *     path="/api/v2/buzz/posts/{id}/shares",
     *     tags={"Buzz/Shares"},
     *     summary="List All Shares of a Post",
     *     operationId="list-all-shares-of-a-post",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=BuzzPostShareSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Buzz-ShareModel")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $postId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_POST_ID
        );
        $buzzShare = $this->getBuzzService()->getBuzzDao()->getBuzzShareByPostId($postId);
        if (!$buzzShare instanceof BuzzShare) {
            throw $this->getInvalidParamException(self::PARAMETER_POST_ID);
        }

        $buzzSharePostSearchFilterParams = new BuzzPostShareSearchFilterParams();
        $buzzSharePostSearchFilterParams->setPostId($postId);
        $this->setSortingAndPaginationParams($buzzSharePostSearchFilterParams);

        $shares = $this->getBuzzService()->getBuzzDao()->getBuzzPostSharesList($buzzSharePostSearchFilterParams);
        $count = $this->getBuzzService()->getBuzzDao()->getBuzzPostSharesCount($buzzSharePostSearchFilterParams);

        return new EndpointCollectionResult(
            BuzzShareModel::class,
            $shares,
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
                self::PARAMETER_POST_ID,
                new Rule(Rules::POSITIVE)
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
