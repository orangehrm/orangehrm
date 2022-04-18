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
 *
 */

namespace OrangeHRM\Pim\Api;

use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\CSVUploadFailedException;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Pim\Service\PimCsvDataImportService;

class EmployeeCSVImportAPI extends Endpoint implements CollectionEndpoint
{
    use EntityManagerHelperTrait;

    public const PARAMETER_ATTACHMENT = 'attachment';

    public const PARAM_RULE_IMPORT_FILE_FORMAT = ["text/csv", 'text/comma-separated-values', "application/csv", "application/vnd.ms-excel"];
    public const PARAM_RULE_IMPORT_FILE_EXTENSIONS = ["csv"];

    /**
     * @var null|PimCsvDataImportService
     */
    protected ?PimCsvDataImportService $pimCsvDataImportService = null;

    /**
     * @return PimCsvDataImportService
     */
    public function getPimCsvDataImportService(): PimCsvDataImportService
    {
        if (!$this->pimCsvDataImportService instanceof PimCsvDataImportService) {
            $this->pimCsvDataImportService = new PimCsvDataImportService();
        }
        return $this->pimCsvDataImportService;
    }


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
     * @throws Exception
     */
    public function create(): EndpointResult
    {
        $attachment = $this->getRequestParams()->getAttachment(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ATTACHMENT
        );

        $this->beginTransaction();
        try {
            $result = $this->getPimCsvDataImportService()->import($attachment->getContent());
            $this->commitTransaction();
        } catch (CSVUploadFailedException $e) {
            $this->rollBackTransaction();
            throw new BadRequestException($e->getMessage());
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }

        return new EndpointResourceResult(
            ArrayModel::class,
            [],
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $result])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getAttachmentRule(),
        );
    }


    /**
     * @return ParamRule
     */
    private function getAttachmentRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_ATTACHMENT,
            new Rule(
                Rules::BASE_64_ATTACHMENT,
                [self::PARAM_RULE_IMPORT_FILE_FORMAT, self::PARAM_RULE_IMPORT_FILE_EXTENSIONS]
            )
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
