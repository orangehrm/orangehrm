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

use Exception;
use OpenApi\Annotations as OA;
use OrangeHRM\Buzz\Api\Model\BuzzLikeOnCommentModel;
use OrangeHRM\Buzz\Dto\BuzzLikeOnCommentSearchFilterParams;
use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\BuzzComment;
use OrangeHRM\Entity\BuzzLikeOnComment;
use OrangeHRM\ORM\Exception\TransactionException;

class BuzzLikeOnCommentAPI extends Endpoint implements CollectionEndpoint
{
    use AuthUserTrait;
    use BuzzServiceTrait;
    use EntityManagerHelperTrait;

    public const PARAMETER_COMMENT_ID = 'commentId';

    /**
     * @OA\Get(
     *     path="/api/v2/buzz/comments/{commentId}/likes",
     *     tags={"Buzz/Comment Likes"},
     *     summary="List Likes on a Comment",
     *     operationId="List-likes-on-a-comment",
     *     @OA\PathParameter(
     *         name="commentId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=BuzzLikeOnCommentSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Buzz-BuzzLikeOnCommentModel")
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
        $commentId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_COMMENT_ID
        );
        $buzzComment = $this->getBuzzService()->getBuzzDao()->getBuzzCommentById($commentId);
        if (!$buzzComment instanceof BuzzComment) {
            throw $this->getInvalidParamException(self::PARAMETER_COMMENT_ID);
        }

        $buzzLikeOnCommentSearchFilterParams = new BuzzLikeOnCommentSearchFilterParams();
        $buzzLikeOnCommentSearchFilterParams->setCommentId($commentId);

        $this->setSortingAndPaginationParams($buzzLikeOnCommentSearchFilterParams);

        $likes = $this->getBuzzService()
            ->getBuzzLikeDao()
            ->getBuzzLikeOnCommentList($buzzLikeOnCommentSearchFilterParams);
        $likeCount = $this->getBuzzService()
            ->getBuzzLikeDao()
            ->getBuzzLikeOnCommentCount($buzzLikeOnCommentSearchFilterParams);

        return new EndpointCollectionResult(
            BuzzLikeOnCommentModel::class,
            $likes,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $likeCount])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_COMMENT_ID,
                new Rule(Rules::POSITIVE),
            ),
            ...$this->getSortingAndPaginationParamsRules(BuzzLikeOnCommentSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/buzz/comments/{commentId}/likes",
     *     tags={"Buzz/Comment Likes"},
     *     summary="Like a Comment",
     *     operationId="like-a-comment",
     *     @OA\PathParameter(
     *         name="commentId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Buzz-BuzzLikeOnCommentModel"
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request - Liking a comment that is already liked",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", default="400"),
     *                 @OA\Property(property="message", type="string", default="Already liked")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $commentId = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_COMMENT_ID
            );

            $buzzComment = $this->getBuzzService()->getBuzzDao()->getBuzzCommentById($commentId);
            if (!$buzzComment instanceof BuzzComment) {
                throw $this->getInvalidParamException(self::PARAMETER_COMMENT_ID);
            }

            $buzzShareOnComment = $this->getBuzzService()
                ->getBuzzLikeDao()
                ->getBuzzLikeOnCommentByShareIdAndEmpNumber($commentId, $this->getAuthUser()->getEmpNumber());
            if ($buzzShareOnComment instanceof BuzzLikeOnComment) {
                throw $this->getBadRequestException('Already liked');
            }

            $buzzComment->getDecorator()->increaseNumOfLikesByOne();
            $this->getBuzzService()->getBuzzDao()->saveBuzzComment($buzzComment);

            $like = new BuzzLikeOnComment();
            $this->setBuzzLikeOnComment($like);

            $like = $this->getBuzzService()->getBuzzLikeDao()->saveBuzzLikeOnComment($like);
            $this->commitTransaction();

            return new EndpointResourceResult(BuzzLikeOnCommentModel::class, $like);
        } catch (InvalidParamException | BadRequestException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @param BuzzLikeOnComment $buzzLikeOnComment
     */
    private function setBuzzLikeOnComment(BuzzLikeOnComment $buzzLikeOnComment): void
    {
        $buzzLikeOnComment->getDecorator()->setCommentByCommentId(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_COMMENT_ID
            )
        );

        $buzzLikeOnComment->getDecorator()->setEmployeeByEmpNumber(
            $this->getAuthUser()->getEmpNumber()
        );

        $buzzLikeOnComment->setLikedAtUtc();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_COMMENT_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/buzz/comments/{commentId}/likes",
     *     tags={"Buzz/Comment Likes"},
     *     summary="Unlike a Liked Comment",
     *     operationId="unlike-a-liked-comment",
     *     @OA\PathParameter(
     *         name="commentId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="commentId", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request - Unlike a comment that is not liked",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", default="400"),
     *                 @OA\Property(property="messsage", type="string", default="Not previously liked")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $commentId = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_COMMENT_ID
            );

            $buzzComment = $this->getBuzzService()->getBuzzDao()->getBuzzCommentById($commentId);
            if (!$buzzComment instanceof BuzzComment) {
                throw $this->getInvalidParamException(self::PARAMETER_COMMENT_ID);
            }

            $buzzCommentOnLike = $this->getBuzzService()
                ->getBuzzLikeDao()
                ->getBuzzLikeOnCommentByShareIdAndEmpNumber($commentId, $this->getAuthUser()->getEmpNumber());
            if (!$buzzCommentOnLike instanceof BuzzLikeOnComment) {
                throw $this->getBadRequestException('Not previously liked');
            }

            $buzzComment->getDecorator()->decreaseNumOfLikesByOne();
            $this->getBuzzService()->getBuzzDao()->saveBuzzComment($buzzComment);

            $this->getBuzzService()->getBuzzLikeDao()->deleteBuzzLikeOnComment(
                $commentId,
                $this->getAuthUser()->getEmpNumber()
            );
            $this->commitTransaction();

            return new EndpointResourceResult(ArrayModel::class, [self::PARAMETER_COMMENT_ID => $commentId]);
        } catch (InvalidParamException | BadRequestException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_COMMENT_ID,
                new Rule(Rules::POSITIVE)
            )
        );
    }
}
