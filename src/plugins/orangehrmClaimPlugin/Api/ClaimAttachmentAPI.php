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

namespace OrangeHRM\Claim\Api;

use Exception;
use OrangeHRM\Claim\Api\Model\ClaimAttachmentModel;
use OrangeHRM\Claim\Dto\ClaimAttachmentSearchFilterParams;
use OrangeHRM\Claim\Dto\PartialClaimAttachment;
use OrangeHRM\Claim\Traits\Service\ClaimServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Exception\InvalidParamException;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\ClaimAttachment;
use OrangeHRM\Installer\Exception\NotImplementedException;
use OrangeHRM\ORM\Exception\TransactionException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ClaimAttachmentAPI extends Endpoint implements CrudEndpoint
{
    use EntityManagerHelperTrait;
    use ClaimServiceTrait;
    use AuthUserTrait;
    use DateTimeHelperTrait;

    public const PARAMETER_REQUEST_ID = 'requestId';
    public const PARAMETER_CLAIM_REQUEST_ID = 'claimRequestId';
    public const PARAMETER_ATTACHMENT_CONTENT = 'eattachAttachment';
    public const PARAMETER_ATTACHMENT_DESCRIPTION = 'eattachDESC';

    public const ALLOWED_ATTACHMENT_FILE_TYPES = [
        "image/jpeg",
        "text/plain",
        "text/rtf",
        "application/rtf",
        "application/pdf",
        "application/msword",
        "application/vnd.oasis.opendocument.text",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    ];

    /**
     * @return EndpointResult
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $claimAttachmentSearchFilterParams = new ClaimAttachmentSearchFilterParams();
        $claimAttachmentSearchFilterParams->setClaimRequestId($this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_CLAIM_REQUEST_ID));
        $claimAttachments = $this->getClaimService()->getClaimDao()->getClaimAttachmentList($claimAttachmentSearchFilterParams);
        return new EndpointCollectionResult(ClaimAttachmentModel::class, $claimAttachments, new ParameterBag([CommonParams::PARAMETER_TOTAL => count($claimAttachments)]));
    }

    /**
     * @return EndpointResult
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_REQUEST_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            new ParamRule(
                self::PARAMETER_CLAIM_REQUEST_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $claimAttachment = new ClaimAttachment();
            $requestId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_CLAIM_REQUEST_ID);
            if ($this->getClaimService()->getClaimDao()->getClaimRequestById($requestId) == null)
                throw $this->getInvalidParamException(self::PARAMETER_CLAIM_REQUEST_ID, 'Claim Request Id is not valid');
            $claimAttachment->setRequestId($requestId);
            $userId = $this->getAuthUser()->getUserId();
            $claimAttachment->getDecorator()->setUserByUserId($userId);
            $claimAttachment->setEattachId($this->getClaimService()->getClaimDao()->getNextAttachmentId($requestId));
            $claimAttachment->setAttachedTime($this->getDateTimeHelper()->getNow());
            $this->setAttachment($claimAttachment);
            $this->commitTransaction();
        } catch (InvalidParamException|BadRequestException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
        return new EndpointResourceResult(ClaimAttachmentModel::class, $this->getPartialClaimAttachment($claimAttachment));
    }

    private function setAttachment(ClaimAttachment $claimAttachment)
    {
        $base64Attachment = $this->getRequestParams()->getAttachment(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ATTACHMENT_CONTENT
        );
        if (is_null($base64Attachment)) {
            return;
        }
        $claimAttachment->setEattachSize($base64Attachment->getSize());
        $claimAttachment->setEattachType($base64Attachment->getFileType());
        $claimAttachment->setEattachFileName($base64Attachment->getFileName());
        $claimAttachment->setEattachAttachment($base64Attachment->getContent());
        $claimAttachment->setEattachDESC($this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ATTACHMENT_DESCRIPTION));
        $this->getClaimService()->getClaimDao()->saveClaimAttachment($claimAttachment);
    }

    /**
     * @return PartialClaimAttachment
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_CLAIM_REQUEST_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_ATTACHMENT_CONTENT,
                new Rule(Rules::BASE_64_ATTACHMENT, [self::ALLOWED_ATTACHMENT_FILE_TYPES])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ATTACHMENT_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE)
                )
            )
        );
    }

    /**
     * @throws NotImplementedException
     */
    public function delete(): EndpointResult
    {
        throw new NotImplementedException();
    }

    /**
     * @throws NotImplementedException
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }

    /**
     * @throws ResourceNotFoundException
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $requestId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_CLAIM_REQUEST_ID);
        $eattachId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $claimAttachment = $this->getClaimService()->getClaimDao()->getPartialClaimAttachment($requestId, $eattachId);
        if ($claimAttachment == null) {
            throw $this->getRecordNotFoundException();
        }
        return new EndpointResourceResult(ClaimAttachmentModel::class, $claimAttachment);
    }

    /**
     * @return ParamRuleCollection
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_CLAIM_REQUEST_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            )
        );
    }

    /**
     * @throws NotImplementedException
     */
    public function update(): EndpointResult
    {
        throw new NotImplementedException();
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_CLAIM_REQUEST_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_ATTACHMENT_CONTENT,
                new Rule(Rules::BASE_64_ATTACHMENT, [self::ALLOWED_ATTACHMENT_FILE_TYPES])
            ),
            new ParamRule(
                self::PARAMETER_ATTACHMENT_DESCRIPTION,
                new Rule(Rules::STRING_TYPE)
            )
        );
    }

    /**
     * @param ClaimAttachment $claimAttachment
     * @return PartialClaimAttachment
     */
    public function getPartialClaimAttachment(ClaimAttachment $claimAttachment): PartialClaimAttachment
    {
        return new PartialClaimAttachment(
            $claimAttachment->getRequestId(),
            $claimAttachment->getEattachId(),
            $claimAttachment->getEattachSize(),
            $claimAttachment->getEattachDesc(),
            $claimAttachment->getEattachFileName(),
            $claimAttachment->getEattachType(),
            $claimAttachment->getAttachedByName(),
            $claimAttachment->getAttachedTime()
        );
    }
}