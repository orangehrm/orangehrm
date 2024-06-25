<?php

namespace OrangeHRM\Admin\Api;

use OrangeHRM\Admin\Api\Model\I18NImportErrorModel;
use OrangeHRM\Admin\Dto\I18NImportErrorSearchFilterParams;
use OrangeHRM\Admin\Traits\Service\LocalizationServiceTrait;
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
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\I18NLanguage;

class I18NImportErrorAPI extends Endpoint implements CollectionEndpoint
{
    use AuthUserTrait;
    use LocalizationServiceTrait;

    public const PARAMETER_LANGUAGE_ID = 'languageId';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $importErrorSearchFilterParams = new I18NImportErrorSearchFilterParams();
        $this->setSortingAndPaginationParams($importErrorSearchFilterParams);

        $importErrorSearchFilterParams->setLanguageId(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_LANGUAGE_ID
            )
        );
        $importErrorSearchFilterParams->setEmpNumber(
            $this->getAuthUser()->getEmpNumber()
        );

        $items = $this->getLocalizationService()->getLocalizationDao()->getImportErrorList($importErrorSearchFilterParams);
        $count = $this->getLocalizationService()->getLocalizationDao()->getImportErrorCount($importErrorSearchFilterParams);

        return new EndpointCollectionResult(
            I18NImportErrorModel::class,
            $items,
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
                self::PARAMETER_LANGUAGE_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [I18NLanguage::class])
            ),
            ...$this->getSortingAndPaginationParamsRules(I18NImportErrorSearchFilterParams::ALLOWED_SORT_FIELDS)
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
