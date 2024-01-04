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

use OrangeHRM\Buzz\Api\Model\BuzzFeedPostModel;
use OrangeHRM\Buzz\Api\Model\BuzzPostModel;
use OrangeHRM\Buzz\Api\ValidationRules\BuzzVideoLinkValidationRule;
use OrangeHRM\Buzz\Dto\BuzzFeedFilterParams;
use OrangeHRM\Buzz\Dto\BuzzFeedPost;
use OrangeHRM\Buzz\Dto\BuzzVideoURL\BuzzEmbeddedURL;
use OrangeHRM\Buzz\Exception\InvalidURLException;
use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Dto\Base64Attachment;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\BuzzLink;
use OrangeHRM\Entity\BuzzPhoto;
use OrangeHRM\Entity\BuzzPost;
use OrangeHRM\Entity\BuzzShare;
use OrangeHRM\ORM\Exception\TransactionException;
use Exception;

class BuzzPostAPI extends Endpoint implements CrudEndpoint
{
    use EntityManagerHelperTrait;
    use BuzzServiceTrait;
    use UserRoleManagerTrait;
    use DateTimeHelperTrait;
    use AuthUserTrait;

    public const PARAMETER_POST_TEXT = 'text';
    public const PARAMETER_POST_TYPE = 'type';
    public const PARAMETER_VIDEO_LINK = 'link';
    public const PARAMETER_POST_PHOTOS = 'photos';
    public const PARAMETER_DELETED_PHOTO_IDS = 'deletedPhotos';
    public const PARAMETER_MODEL = 'model';

    public const PARAM_RULE_TEXT_MAX_LENGTH = 65530;
    public const PARAM_RULE_PHOTO_NAME_MAX_LENGTH = 100;
    public const MAX_ATTACHMENT_SIZE = 2097152; //2MB

    public const MODEL_DEFAULT_POST = 'default';
    public const MODEL_DETAILED_POST = 'detailed';
    public const MODEL_MAP = [
            self::MODEL_DEFAULT_POST => BuzzPostModel::class,
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
     *     path="/api/v2/buzz/posts",
     *     tags={"Buzz/Post"},
     *     summary="Post Text, Photos or Video",
     *     operationId="post-text-photos-or-video",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/Buzz-Post-Text"),
     *                 @OA\Schema(ref="#/components/schemas/Buzz-Post-Photo"),
     *                 @OA\Schema(ref="#/components/schemas/Buzz-Post-Video"),
     *             },
     *             required={"title", "type"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Buzz-PostModel"),
     *                     @OA\Schema(ref="#/components/schemas/Buzz-FeedPostModel"),
     *                 })
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @OA\Schema(
     *     schema="Buzz-Post-Text",
     *     type="object",
     *     @OA\Property(property="text", type="string"),
     *     @OA\Property(property="type", type="string"),
     * )
     *
     * @OA\Schema(
     *     schema="Buzz-Post-Photo",
     *     type="object",
     *     @OA\Property(property="text", type="string"),
     *     @OA\Property(property="type", type="string"),
     *     @OA\Property(property="photos", type="array", @OA\Items(ref="#/components/schemas/Base64Attachment")),
     * )
     *
     * @OA\Schema(
     *     schema="Buzz-Post-Video",
     *     type="object",
     *     @OA\Property(property="text", type="string"),
     *     @OA\Property(property="type", type="string"),
     *     @OA\Property(property="link", type="string"),
     * )
     *
     * @inheritDoc
     * @throws InvalidURLException
     */
    public function create(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $buzzPost = new BuzzPost();
            $this->setBuzzPost($buzzPost);
            $buzzPost->setCreatedAtUtc();
            $this->getBuzzService()->getBuzzDao()->saveBuzzPost($buzzPost);

            $postType = $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_POST_TYPE
            );
            if ($postType == BuzzShare::POST_TYPE_PHOTO) {
                $this->setBuzzPhotos($buzzPost);
            } elseif ($postType == BuzzShare::POST_TYPE_VIDEO) {
                $this->setBuzzVideoPost($buzzPost);
            }

            $buzzShare = new BuzzShare();
            $this->setBuzzShare($buzzShare, $buzzPost);
            $buzzShare->setCreatedAtUtc();
            $this->getBuzzService()->getBuzzDao()->saveBuzzShare($buzzShare);

            $this->commitTransaction();
            return new EndpointResourceResult(BuzzPostModel::class, $buzzPost);
        } catch (InvalidParamException|BadRequestException $e) {
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
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_POST_TYPE,
                new Rule(Rules::STRING_TYPE),
                new Rule(
                    Rules::IN,
                    [[BuzzShare::POST_TYPE_TEXT, BuzzShare::POST_TYPE_PHOTO, BuzzShare::POST_TYPE_VIDEO]]
                ),
            )
        );

        $postType = $this->getRequestParams()
            ->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_POST_TYPE);

        if ($postType === BuzzShare::POST_TYPE_TEXT) {
            $paramRules->addParamValidation(
                $this->getTextValidationRuleForTextPost(),
            );
        } elseif ($postType === BuzzShare::POST_TYPE_PHOTO) {
            $paramRules->addParamValidation(
                $this->getPhotoValidationRule(),
            );
            $paramRules->addParamValidation(
                $this->getTextValidationRuleForOtherPost(),
            );
        } elseif ($postType === BuzzShare::POST_TYPE_VIDEO) {
            $paramRules->addParamValidation(
                $this->getVideoValidationRule(),
            );
            $paramRules->addParamValidation(
                $this->getTextValidationRuleForOtherPost(),
            );
        }
        return $paramRules;
    }

    /**
     * @param BuzzPost $buzzPost
     */
    private function setBuzzPost(BuzzPost $buzzPost): void
    {
        $buzzPost->setText(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_POST_TEXT
            )
        );
        $empNumber = $this->getAuthUser()->getEmpNumber();
        $buzzPost->getDecorator()->setEmployeeByEmpNumber($empNumber);
    }

    /**
     * @param BuzzShare $buzzShare
     * @param BuzzPost  $buzzPost
     */
    private function setBuzzShare(BuzzShare $buzzShare, BuzzPost $buzzPost): void
    {
        $buzzShare->setPost($buzzPost);
        $buzzShare->setEmployee($buzzPost->getEmployee());
        $buzzShare->setType(BuzzShare::TYPE_POST);
    }

    /**
     * @param BuzzPost $buzzPost
     * @throws InvalidParamException
     */
    private function setBuzzPhotos(BuzzPost $buzzPost): void
    {
        $postPhotos = $this->getRequestParams()->getArray(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_POST_PHOTOS
        );

        if (empty($postPhotos) || count($postPhotos) > 5) {
            throw $this->getInvalidParamException(self::PARAMETER_POST_PHOTOS);
        }
        foreach ($postPhotos as $photo) {
            $attachment = Base64Attachment::createFromArray($photo);
            $buzzPhoto = new BuzzPhoto();
            $buzzPhoto->setPost($buzzPost);
            $buzzPhoto->setPhoto($attachment->getContent());
            $buzzPhoto->setFilename($attachment->getFilename());
            $buzzPhoto->setFileType($attachment->getFileType());
            $buzzPhoto->setSize($attachment->getSize());

            $this->getBuzzService()->getBuzzDao()->saveBuzzPhoto($buzzPhoto);
        }
    }

    /**
     * @param BuzzPost $buzzPost
     *
     * @return void
     * @throws InvalidURLException
     */
    private function setBuzzVideoPost(BuzzPost $buzzPost): void
    {
        $videoLink = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_VIDEO_LINK
        );
        $buzzVideoPost = new BuzzLink();
        $buzzVideoPost->setPost($buzzPost);

        $buzzEmbeddedURL = new BuzzEmbeddedURL($videoLink);
        $buzzVideoPost->setLink($buzzEmbeddedURL->getEmbeddedURL());
        $buzzVideoPost->setOriginalLink($videoLink);

        $this->getBuzzService()->getBuzzDao()->saveBuzzVideo($buzzVideoPost);
    }

    /**
     * @return ParamRule
     */
    private function getPhotoValidationRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_POST_PHOTOS,
            new Rule(Rules::ARRAY_TYPE),
            new Rule(
                Rules::EACH,
                [
                    new Rules\Composite\AllOf(
                        new Rule(
                            Rules::BASE_64_ATTACHMENT,
                            [
                                BuzzPhoto::ALLOWED_IMAGE_TYPES,
                                BuzzPhoto::ALLOWED_IMAGE_EXTENSIONS,
                                self::PARAM_RULE_PHOTO_NAME_MAX_LENGTH,
                                null,
                                true,
                                self::MAX_ATTACHMENT_SIZE,
                            ]
                        ),
                    )
                ]
            )
        );
    }

    /**
     * @return ParamRule|null
     */
    private function getVideoValidationRule(): ?ParamRule
    {
        return new ParamRule(
            self::PARAMETER_VIDEO_LINK,
            new Rule(BuzzVideoLinkValidationRule::class),
        );
    }

    /**
     * @return ParamRule|null
     */
    private function getTextValidationRuleForOtherPost(): ?ParamRule
    {
        return $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(
                self::PARAMETER_POST_TEXT,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::STR_LENGTH, [null, self::PARAM_RULE_TEXT_MAX_LENGTH])
            ),
            true
        );
    }

    /**
     * @return ParamRule|null
     */
    private function getTextValidationRuleForTextPost(): ?ParamRule
    {
        return new ParamRule(
            self::PARAMETER_POST_TEXT,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::REQUIRED),
            new Rule(Rules::STR_LENGTH, [null, self::PARAM_RULE_TEXT_MAX_LENGTH])
        );
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
     * @OA\Get(
     *     path="/api/v2/buzz/posts/{id}",
     *     tags={"Buzz/Post"},
     *     summary="Get a Post",
     *     operationId="get-a-post",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={OrangeHRM\Buzz\Api\BuzzPostAPI::MODEL_DEFAULT_POST, OrangeHRM\Buzz\Api\BuzzPostAPI::MODEL_DETAILED_POST},
     *             default=OrangeHRM\Buzz\Api\BuzzPostAPI::MODEL_DEFAULT_POST
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Buzz-PostModel"),
     *                     @OA\Schema(ref="#/components/schemas/Buzz-FeedPostModel"),
     *                 }
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $buzzPost = $this->getBuzzService()->getBuzzDao()->getBuzzPostById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($buzzPost, BuzzPost::class);

        $modelClass = $this->getModelClass();
        if ($modelClass == BuzzFeedPostModel::class) {
            $buzzPost = $this->getBuzzFeedPost($id);
        }

        return new EndpointResourceResult($modelClass, $buzzPost);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MODEL,
                    new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])
                )
            )
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/buzz/posts/{id}",
     *     tags={"Buzz/Post"},
     *     summary="Edit a Post",
     *     operationId="edit-a-post",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={OrangeHRM\Buzz\Api\BuzzPostAPI::MODEL_DEFAULT_POST, OrangeHRM\Buzz\Api\BuzzPostAPI::MODEL_DETAILED_POST},
     *             default=OrangeHRM\Buzz\Api\BuzzPostAPI::MODEL_DEFAULT_POST
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/Buzz-Post-Text-Edit"),
     *                 @OA\Schema(ref="#/components/schemas/Buzz-Post-Photo-Edit"),
     *                 @OA\Schema(ref="#/components/schemas/Buzz-Post-Video-Edit"),
     *             },
     *             required={"title", "type"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Buzz-PostModel"),
     *                     @OA\Schema(ref="#/components/schemas/Buzz-FeedPostModel"),
     *                 }
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @OA\Schema(
     *     schema="Buzz-Post-Text-Edit",
     *     type="object",
     *     @OA\Property(property="text", type="string"),
     *     @OA\Property(property="type", type="string"),
     * )
     *
     * @OA\Schema(
     *     schema="Buzz-Post-Photo-Edit",
     *     type="object",
     *     @OA\Property(property="text", type="string"),
     *     @OA\Property(property="type", type="string"),
     *     @OA\Property(
     *         property="deletedPhotos",
     *         type="array",
     *         @OA\Items(
     *             @OA\Property(property="id", type="integer"),
     *         ),
     *         example="59,60"
     *     ),
     *     @OA\Property(property="photos", type="array", @OA\Items(ref="#/components/schemas/Base64Attachment")),
     * )
     *
     * @OA\Schema(
     *     schema="Buzz-Post-Video-Edit",
     *     type="object",
     *     @OA\Property(property="text", type="string"),
     *     @OA\Property(property="type", type="string"),
     *     @OA\Property(property="link", type="string"),
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $postId = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                CommonParams::PARAMETER_ID
            );

            $buzzPost = $this->getBuzzService()->getBuzzDao()->getBuzzPostById($postId);
            if (!$buzzPost instanceof BuzzPost) {
                throw $this->getInvalidParamException(CommonParams::PARAMETER_ID);
            }

            if (!$this->getBuzzService()->canUpdateBuzzFeedPost($buzzPost->getEmployee()->getEmpNumber())) {
                throw $this->getForbiddenException();
            }

            $originalPostType = $this->getBuzzService()->getBuzzDao()->getBuzzPostTypeByPostId($postId);

            $postType = $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_POST_TYPE
            );

            $text = $this->getRequestParams()
                ->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_POST_TEXT);
            $link = $this->getRequestParams()
                ->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_VIDEO_LINK);
            $photos = $this->getRequestParams()
                ->getArrayOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_POST_PHOTOS);
            $deletedPhotoIds = $this->getRequestParams()
                ->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DELETED_PHOTO_IDS, []);

            if (count($deletedPhotoIds) > 5) {
                throw $this->getInvalidParamException(self::PARAMETER_DELETED_PHOTO_IDS);
            }

            if ($originalPostType === BuzzShare::POST_TYPE_VIDEO) {
                if ($postType === BuzzShare::POST_TYPE_PHOTO || $postType === BuzzShare::POST_TYPE_TEXT) {
                    throw $this->getInvalidParamException(self::PARAMETER_POST_TYPE);
                } elseif ($link !== null) {
                    $buzzPost->setText($text);
                    $this->updateBuzzVideoPost($buzzPost, $link);
                }
            } elseif ($originalPostType === BuzzShare::POST_TYPE_TEXT
                || $originalPostType === BuzzShare::POST_TYPE_PHOTO
            ) {
                if ($postType === BuzzShare::POST_TYPE_VIDEO) {
                    throw $this->getInvalidParamException(self::PARAMETER_POST_TYPE);
                }

                $addedPhotoIds = $this->getBuzzService()->getBuzzDao()->getBuzzPhotoIdsByPostId($postId);

                if (!empty($deletedPhotoIds)) {
                    $wrongPhotoIds = array_diff($deletedPhotoIds, $addedPhotoIds);
                    if (!empty($wrongPhotoIds)) {
                        throw $this->getInvalidParamException(self::PARAMETER_DELETED_PHOTO_IDS);
                    }
                    $this->getBuzzService()->getBuzzDao()->deleteBuzzPostPhotos($deletedPhotoIds, $postId);
                }

                if (!empty($photos)) {
                    if (count($photos) + count($addedPhotoIds) - count($deletedPhotoIds) > 5) {
                        throw $this->getInvalidParamException(self::PARAMETER_POST_PHOTOS);
                    }
                    $this->setBuzzPhotos($buzzPost);
                }
                $buzzPost->setText($text);
            }

            $this->setBuzzPost($buzzPost);
            $buzzPost->setUpdatedAtUtc();
            $this->getBuzzService()->getBuzzDao()->saveBuzzPost($buzzPost);

            $modelClass = $this->getModelClass();
            if ($modelClass == BuzzFeedPostModel::class) {
                $buzzPost = $this->getBuzzFeedPost($postId);
            }

            $this->commitTransaction();
        } catch (InvalidParamException|ForbiddenException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
        return new EndpointResourceResult($modelClass, $buzzPost);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramRules = $this->getValidationRuleForCreate();

        $paramRules->addParamValidation(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE),
            ),
        );

        $paramRules->addParamValidation(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DELETED_PHOTO_IDS,
                    new Rule(Rules::INT_ARRAY),
                )
            )
        );

        $paramRules->addParamValidation(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MODEL,
                    new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])
                )
            )
        );

        return $paramRules;
    }

    /**
     * @param BuzzPost $buzzPost
     * @param string   $link
     *
     * @return void
     * @throws InvalidURLException
     */
    private function updateBuzzVideoPost(BuzzPost $buzzPost, string $link): void
    {
        $buzzLink = $this->getBuzzService()->getBuzzDao()->getBuzzLinkByPostId($buzzPost->getId());
        $buzzLink->setOriginalLink($link);

        $buzzEmbeddedURL = new BuzzEmbeddedURL($link);
        $buzzLink->setLink($buzzEmbeddedURL->getEmbeddedURL());

        $this->getBuzzService()->getBuzzDao()->saveBuzzVideo($buzzLink);
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
     * @param int $postId
     * @return BuzzFeedPost
     */
    private function getBuzzFeedPost(int $postId): BuzzFeedPost
    {
        $buzzShare = $this->getBuzzService()->getBuzzDao()->getBuzzShareByPostId($postId);
        $buzzFeedFilterParams = new BuzzFeedFilterParams();
        $buzzFeedFilterParams->setAuthUserEmpNumber($this->getAuthUser()->getEmpNumber());
        $buzzFeedFilterParams->setShareId($buzzShare->getId());
        $buzzFeedPosts = $this->getBuzzService()->getBuzzDao()->getBuzzFeedPosts($buzzFeedFilterParams);
        return $buzzFeedPosts[0];
    }
}
