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

namespace OrangeHRM\Pim\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\EmpPicture;
use OrangeHRM\Pim\Api\Model\EmployeePictureModel;
use OrangeHRM\Pim\Service\EmployeePictureService;

class EmployeePictureAPI extends Endpoint implements ResourceEndpoint
{
    public const PARAMETER_EMP_PICTURE = 'empPicture';

    public const PARAM_RULE_EMP_PICTURE_FILE_NAME_MAX_LENGTH = 100;

    /**
     * @var EmployeePictureService|null
     */
    protected ?EmployeePictureService $employeePictureService = null;

    /**
     * @return EmployeePictureService
     */
    public function getEmployeePictureService(): EmployeePictureService
    {
        if (!$this->employeePictureService instanceof EmployeePictureService) {
            $this->employeePictureService = new EmployeePictureService();
        }
        return $this->employeePictureService;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $empPicture = $this->getEmployeePictureService()->getEmpPictureByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($empPicture, EmpPicture::class);
        return new EndpointResourceResult(EmployeePictureModel::class, $empPicture);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $empPicture = $this->getEmployeePictureService()->getEmpPictureByEmpNumber($empNumber);
        if (!$empPicture instanceof EmpPicture) {
            $empPicture = new EmpPicture();
            $empPicture->getDecorator()->setEmployeeByEmpNumber($empNumber);
        }

        $empPictureAttachment = $this->getRequestParams()->getAttachment(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_EMP_PICTURE
        );
        $empPicture->setFilename($empPictureAttachment->getFilename());
        $empPicture->setFileType($empPictureAttachment->getFileType());
        $empPicture->setSize($empPictureAttachment->getSize());
        $empPicture->setPicture($empPictureAttachment->getContent());

        list($width, $height) = $this->getEmployeePictureService()->pictureSizeAdjust(
            $empPictureAttachment->getContent()
        );
        $empPicture->setWidth($width);
        $empPicture->setHeight($height);

        $this->getEmployeePictureService()->saveEmployeePicture($empPicture);

        return new EndpointResourceResult(EmployeePictureModel::class, $empPicture);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                self::PARAMETER_EMP_PICTURE,
                new Rule(
                    Rules::BASE_64_ATTACHMENT,
                    [EmpPicture::ALLOWED_IMAGE_TYPES, EmpPicture::ALLOWED_IMAGE_EXTENSIONS, self::PARAM_RULE_EMP_PICTURE_FILE_NAME_MAX_LENGTH]
                )
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
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
