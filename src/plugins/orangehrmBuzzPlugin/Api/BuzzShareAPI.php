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
use OrangeHRM\Buzz\Api\Model\BuzzFeedPostModel;
use OrangeHRM\Buzz\Api\Model\BuzzShareModel;
use OrangeHRM\Buzz\Dto\BuzzFeedFilterParams;
use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\BuzzShare;
use OrangeHRM\ORM\Exception\TransactionException;

class BuzzShareAPI extends Endpoint implements CrudEndpoint
{
    use BuzzServiceTrait;
    use AuthUserTrait;
    use EntityManagerHelperTrait;

    public const PARAMETER_TEXT = 'text';
    public const PARAMETER_SHARE_ID = 'shareId';

    public const PARAMETER_MODEL = 'model';
    public const MODEL_DEFAULT_POST = 'default';
    public const MODEL_DETAILED_POST = 'detailed';
    public const MODEL_MAP = [
            self::MODEL_DEFAULT_POST => BuzzShareModel::class,
            self::MODEL_DETAILED_POST => BuzzFeedPostModel::class,
        ];

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @OA\Post(
     *     path="/api/v2/buzz/shares",
     *     tags={"Buzz/Shares"},
     *     summary="Share a Post",
     *     operationId="share-a-post",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="text", type="string"),
     *             @OA\Property(property="shareId", type="integer"),
     *             required={"shareId"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Buzz-ShareModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $shareId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_SHARE_ID);
        $buzzShare = $this->getBuzzService()->getBuzzDao()->getBuzzShareById($shareId);
        if (!$buzzShare instanceof BuzzShare) {
            throw $this->getInvalidParamException(self::PARAMETER_SHARE_ID);
        }

        $share = new BuzzShare();
        $share->getDecorator()->setEmployeeByEmpNumber($this->getAuthUser()->getEmpNumber());
        $share->setPost($buzzShare->getPost());
        $share->setType(BuzzShare::TYPE_SHARE);
        $this->setBuzzShareText($share);
        $share->setCreatedAtUtc();
        $this->getBuzzService()->getBuzzDao()->saveBuzzShare($share);

        return new EndpointResourceResult(BuzzShareModel::class, $share);
    }

    /**
     * @param BuzzShare $buzzShare
     */
    private function setBuzzShareText(BuzzShare $buzzShare): void
    {
        $text = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_TEXT);
        $buzzShare->setText($text);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_SHARE_ID, new Rule(Rules::POSITIVE)),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_TEXT,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::STR_LENGTH, [null, BuzzPostAPI::PARAM_RULE_TEXT_MAX_LENGTH])
                )
            )
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/buzz/shares/{id}",
     *     tags={"Buzz/Shares"},
     *     summary="Delete a Share",
     *     operationId="delete-a-share",
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
     *                 type="object",
     *                 @OA\Property(property="shareId", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="403", ref="#/components/responses/ForbiddenResponse")
     * )
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $shareId = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                CommonParams::PARAMETER_ID
            );

            $buzzShare = $this->getBuzzService()->getBuzzDao()->getBuzzShareById($shareId);
            if (!$buzzShare instanceof BuzzShare) {
                throw $this->getInvalidParamException(CommonParams::PARAMETER_ID);
            }

            if (!$this->getBuzzService()->canDeleteBuzzFeedPost($buzzShare->getEmployee()->getEmpNumber())) {
                throw $this->getForbiddenException();
            }

            if ($buzzShare->getType() === BuzzShare::TYPE_POST) {
                $this->getBuzzService()->getBuzzDao()->deleteBuzzPost($buzzShare->getPost()->getId());
            } else {
                $this->getBuzzService()->getBuzzDao()->deleteBuzzShare($buzzShare->getId());
            }
            $this->commitTransaction();

            return new EndpointResourceResult(ArrayModel::class, [self::PARAMETER_SHARE_ID => $shareId]);
        } catch (InvalidParamException|ForbiddenException $e) {
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
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            )
        );
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
     * @OA\Put(
     *     path="/api/v2/buzz/shares/{id}",
     *     tags={"Buzz/Shares"},
     *     summary="Edit a Share",
     *     operationId="edit-a-share",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="text", type="string")
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Buzz-ShareModel"),
     *                     @OA\Schema(ref="#/components/schemas/Buzz-FeedPostModel"),
     *                 }
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $shareId = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                CommonParams::PARAMETER_ID
            );

            $buzzShare = $this->getBuzzService()->getBuzzDao()->getBuzzShareById($shareId);
            if (!$buzzShare instanceof BuzzShare || $buzzShare->getType() === BuzzShare::TYPE_POST) {
                throw $this->getInvalidParamException(CommonParams::PARAMETER_ID);
            }

            if (!$this->getBuzzService()->canUpdateBuzzFeedPost($buzzShare->getEmployee()->getEmpNumber())) {
                throw $this->getForbiddenException();
            }

            $this->setBuzzShareText($buzzShare);
            $buzzShare->setUpdatedAtUtc();
            $this->getBuzzService()->getBuzzDao()->saveBuzzShare($buzzShare);

            $modelClass = $this->getModelClass();
            $data = $buzzShare;
            if ($modelClass == BuzzFeedPostModel::class) {
                $buzzFeedFilterParams = new BuzzFeedFilterParams();
                $buzzFeedFilterParams->setAuthUserEmpNumber($this->getAuthUser()->getEmpNumber());
                $buzzFeedFilterParams->setShareId($shareId);
                $buzzFeedPosts = $this->getBuzzService()->getBuzzDao()->getBuzzFeedPosts($buzzFeedFilterParams);
                $data = $buzzFeedPosts[0];
            }

            $this->commitTransaction();
            return new EndpointResourceResult($modelClass, $data);
        } catch (InvalidParamException|ForbiddenException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        $model = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_MODEL,
            self::MODEL_DEFAULT_POST
        );
        return self::MODEL_MAP[$model];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_TEXT,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::STR_LENGTH, [null, BuzzPostAPI::PARAM_RULE_TEXT_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MODEL,
                    new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])
                )
            )
        );
    }
}
