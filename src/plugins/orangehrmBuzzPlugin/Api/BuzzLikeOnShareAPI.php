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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\Buzz\Api;

use OrangeHRM\Buzz\Api\Model\BuzzLikeOnShareModel;
use OrangeHRM\Buzz\Dto\BuzzLikeSearchFilterParams;
use OrangeHRM\Buzz\Traits\Service\BuzzLikeServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\BuzzLikeOnShare;
use OrangeHRM\Entity\BuzzShare;

class BuzzLikeOnShareAPI extends Endpoint implements CollectionEndpoint
{
    use AuthUserTrait;
    use BuzzLikeServiceTrait;

    public const FILTER_SHARE_ID = 'shareId';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $buzzLikeSearchFilterParams = new BuzzLikeSearchFilterParams();
        $buzzLikeSearchFilterParams->setShareId(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::FILTER_SHARE_ID
            )
        );

        $this->setSortingAndPaginationParams($buzzLikeSearchFilterParams);


        $likes = $this->getBuzzLikeService()->getBuzzLikeDao()->getBuzzLikeOnShareList($buzzLikeSearchFilterParams);
        $likeCount = $this->getBuzzLikeService()->getBuzzLikeDao()->getBuzzLikeOnShareCount($buzzLikeSearchFilterParams);

        return new EndpointCollectionResult(
            BuzzLikeOnShareModel::class,
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
                self::FILTER_SHARE_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [BuzzShare::class])
            ),
            ...$this->getSortingAndPaginationParamsRules(BuzzLikeSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $shareId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::FILTER_SHARE_ID
        );

        if ($this->getBuzzLikeService()->getBuzzLikeDao()->getBuzzLikeOnShareByShareIdAndEmpNumber(
            $shareId,
            $this->getAuthUser()->getEmpNumber()
        ) instanceof BuzzLikeOnShare) {
            throw $this->getBadRequestException('Share is already liked');
        }

        $like = new BuzzLikeOnShare();
        $this->setBuzzLikeOnShare($like);

        $like = $this->getBuzzLikeService()->getBuzzLikeDao()->saveBuzzLikeOnShare($like);
        return new EndpointResourceResult(BuzzLikeOnShareModel::class, $like);
    }

    /**
     * @param BuzzLikeOnShare $buzzLikeOnShare
     */
    private function setBuzzLikeOnShare(BuzzLikeOnShare $buzzLikeOnShare): void
    {
        $buzzLikeOnShare->getDecorator()->setShareByShareId(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::FILTER_SHARE_ID
            )
        );

        $buzzLikeOnShare->getDecorator()->setEmployeeByEmpNumber(
            $this->getAuthUser()->getEmpNumber()
        );

        $buzzLikeOnShare->setLikedAtUtc();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::FILTER_SHARE_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [BuzzShare::class])
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $shareId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::FILTER_SHARE_ID
        );
        $this->getBuzzLikeService()->getBuzzLikeDao()->deleteBuzzLikeOnShare($shareId, $this->getAuthUser()->getEmpNumber());
        return new EndpointResourceResult(ArrayModel::class, [$shareId]);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::FILTER_SHARE_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [BuzzShare::class])
            ),
        );
    }
}
