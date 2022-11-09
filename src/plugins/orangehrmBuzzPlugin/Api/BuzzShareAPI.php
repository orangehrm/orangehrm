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

namespace OrangeHRM\Buzz\Api;

use OrangeHRM\Buzz\Api\Model\BuzzShareModel;
use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
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

class BuzzShareAPI extends Endpoint implements CollectionEndpoint
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

    public const PARAM_RULE_TEXT_MAX_LENGTH = 65530;
    public const PARAM_RULE_PHOTO_NAME_MAX_LENGTH = 100;

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
     * @inheritDoc
     * @throws TransactionException
     */
    public function create(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $buzzPost = new BuzzPost();
            $this->setBuzzPost($buzzPost);
            $empNumber = $this->getAuthUser()->getEmpNumber();

            $postType = $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_POST_TYPE
            );

            $buzzPost->getDecorator()->setEmployeeByEmpNumber($empNumber);

            $this->getBuzzService()->getBuzzDao()->saveBuzzPost($buzzPost);

            if ($postType == BuzzShare::POST_TYPE_PHOTO) {
                $this->setBuzzPhotos($buzzPost);
            } elseif ($postType == BuzzShare::POST_TYPE_VIDEO) {
                $videoLink = $this->getRequestParams()->getString(
                    RequestParams::PARAM_TYPE_BODY,
                    self::PARAMETER_VIDEO_LINK
                );
                $this->setBuzzVideoPost($buzzPost, $videoLink);
            }

            $this->setBuzzShare($buzzPost, BuzzShare::TYPE_POST);

            $this->commitTransaction();
            return new EndpointResourceResult(BuzzShareModel::class, $buzzPost);
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
        return new ParamRuleCollection(
            $this->getPhotoValidationRule(),
            $this->getVideoValidationRule(),
            ...$this->getCommonBodyValidationRules()
        );
    }

    /**
     * @param BuzzPost $buzzPost
     */
    private function setBuzzPost(BuzzPost $buzzPost): void
    {
        $buzzPost->setText(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_POST_TEXT
            )
        );

        $buzzPost->setCreatedAt($this->getDateTimeHelper()->getNow());
        $buzzPost->setUpdatedAt($this->getDateTimeHelper()->getNow());
    }

    /**
     * @param BuzzPost $buzzPost
     * @param int      $type
     *
     * @return BuzzShare
     */
    private function setBuzzShare(BuzzPost $buzzPost, int $type): BuzzShare
    {
        $buzzShare = new BuzzShare();
        $buzzShare->setPost($buzzPost);
        $buzzShare->setEmployee($buzzPost->getEmployee());
        $buzzShare->setType($type);
        $buzzShare->setCreatedAt($this->getDateTimeHelper()->getNow());
        $buzzShare->setUpdatedAt($this->getDateTimeHelper()->getNow());

        return $this->getBuzzService()->getBuzzDao()->saveBuzzShare($buzzShare);
    }

    /**
     * @param BuzzPost $buzzPost
     */
    private function setBuzzPhotos(BuzzPost $buzzPost): void
    {
        $postPhotos = $this->getRequestParams()->getArray(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_POST_PHOTOS
        );

        foreach ($postPhotos as $photo) {
            $buzzPhoto = new BuzzPhoto();

            $attachment = Base64Attachment::createFromArray($photo);

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
     * @param string   $videoLink
     *
     * @return BuzzLink
     */
    private function setBuzzVideoPost(BuzzPost $buzzPost, string $videoLink): BuzzLink
    {
        $buzzVideoPost = new BuzzLink();
        $buzzVideoPost->setPost($buzzPost);
        $buzzVideoPost->setLink($videoLink);

        return $this->getBuzzService()->getBuzzDao()->saveBuzzVideo($buzzVideoPost);
    }

    /**
     * @return array
     */
    private function getCommonBodyValidationRules(): array
    {
        return [
            new ParamRule(
                self::PARAMETER_POST_TEXT,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TEXT_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_POST_TYPE,
                new Rule(
                    Rules::IN,
                    [[BuzzShare::POST_TYPE_TEXT, BuzzShare::POST_TYPE_PHOTO, BuzzShare::POST_TYPE_VIDEO]]
                ),
            )
        ];
    }

    /**
     * @return ParamRule
     */
    private function getPhotoValidationRule(): ParamRule
    {
        return $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(
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
                                    self::PARAM_RULE_PHOTO_NAME_MAX_LENGTH
                                ]
                            ),
                        )
                    ]
                )
            ),
        );
    }

    /**
     * @return ParamRule
     */
    private function getVideoValidationRule(): ParamRule
    {
        return $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(
                self::PARAMETER_VIDEO_LINK,
                new Rule(Rules::STRING_TYPE),
            ),
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
}
