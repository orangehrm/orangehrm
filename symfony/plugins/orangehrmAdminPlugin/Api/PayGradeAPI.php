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

namespace OrangeHRM\Admin\Api;

use OrangeHRM\Admin\Dto\PayGradeSearchFilterParams;
use OrangeHRM\Admin\Api\Model\PayGradeModel;
use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\PayGrade;

class PayGradeAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';

    /**
     * @var null|PayGradeService
     */
    protected ?PayGradeService $payGradeService = null;

    /**
     * @return PayGradeService
     */
    public function getPayGradeService() : PayGradeService
    {
        if(is_null($this->payGradeService)){
            $this->payGradeService = new PayGradeService();
        }
        return $this->payGradeService;
    }

    /**
     * @param PayGradeService $payGradeService
     * @return $this
     */
    public function setPayGradeService(PayGradeService $payGradeService): void
    {
        $this->payGradeService = $payGradeService;
    }

    /**
     * @return EndpointResult
     * @throws \OrangeHRM\Core\Api\V2\Serializer\NormalizeException
     * @throws \OrangeHRM\Core\Exception\DaoException
     */
    public function getAll(): EndpointCollectionResult
    {
        $payGradeSearchFilterParams = new PayGradeSearchFilterParams();
        $this->setSortingAndPaginationParams($payGradeSearchFilterParams);
        $count = $this->getPayGradeService()->getPayGradeDao()->getPayGradesCount($payGradeSearchFilterParams);
        $payGrades =  $this->getPayGradeService()->getPayGradeDao()->getPayGradeList($payGradeSearchFilterParams);

        return  new EndpointCollectionResult(PayGradeModel::class,
            $payGrades,
            new ParameterBag([CommonParams::PARAMETER_TOTAL=>$count])
        );
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(...$this->getSortingAndPaginationParamsRules(PayGradeSearchFilterParams::ALLOWED_SORT_FIELDS));
    }

    /**
     * @return EndpointResourceResult
     * @throws \OrangeHRM\Core\Api\V2\Serializer\NormalizeException
     * @throws \OrangeHRM\Core\Exception\DaoException
     */
    public function create(): EndpointResourceResult
    {
        $payGrade = $this->savePayGrade();
        return new EndpointResourceResult(PayGradeModel::class, $payGrade);
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_NAME),
        );
    }

    /**
     * @return EndpointResult
     * @throws \OrangeHRM\Core\Api\V2\Serializer\NormalizeException
     * @throws \OrangeHRM\Core\Exception\DaoException
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getPayGradeService()->deletePayGrades($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }

    /**
     * @return EndpointResult
     * @throws RecordNotFoundException
     * @throws \OrangeHRM\Core\Api\V2\Serializer\NormalizeException
     * @throws \OrangeHRM\Core\Exception\DaoException
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $payGrade = $this->getPayGradeService()->getPayGradeById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($payGrade,PayGrade::class);
        return new EndpointResourceResult(PayGradeModel::class, $payGrade);
    }

    /**
     * @return ParamRuleCollection
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID)
        );
    }

    /**
     * @return EndpointResult
     * @throws \OrangeHRM\Core\Api\V2\Serializer\NormalizeException
     * @throws \OrangeHRM\Core\Exception\DaoException
     */
    public function update(): EndpointResourceResult
    {
        $payGrade = $this->savePayGrade();
        return new EndpointResourceResult(PayGradeModel::class, $payGrade);
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(self::PARAMETER_NAME),
        );
    }

    /**
     * @return PayGrade
     * @throws \OrangeHRM\Core\Exception\DaoException
     */
    protected function savePayGrade(): PayGrade
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        if (!empty($id)) {
            $payGrade = $this->getPayGradeService()->getPayGradeById($id);
        }else{
            $payGrade = new PayGrade();
        }
        $payGrade->setName($name);
        return  $this->getPayGradeService()->savePayGrade($payGrade);
    }
}