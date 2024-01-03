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

namespace OrangeHRM\Recruitment\Controller\PublicController;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\Request;
use OrangeHRM\Core\Api\V2\Response;
use OrangeHRM\Core\Api\V2\Validator\Helpers\ValidationDecorator;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Controller\Rest\V2\AbstractRestController;
use OrangeHRM\Core\Dto\FilterParams;
use OrangeHRM\Core\Exception\SearchParamException;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\Recruitment\Api\Model\VacancyModel;
use OrangeHRM\Recruitment\Dto\VacancySearchFilterParams;
use OrangeHRM\Recruitment\Traits\Service\VacancyServiceTrait;

class VacancyListRestController extends AbstractRestController implements PublicControllerInterface
{
    use VacancyServiceTrait;
    use NormalizerServiceTrait;

    private const VACANCY_ID = 'vacancy.id';
    private const VACANCY_OFFSET = 'offset';
    private const VACANCY_LIMIT = 'limit';
    /**
     * @var ValidationDecorator|null
     */
    private ?ValidationDecorator $validationDecorator = null;


    /**
     * @param Request $request
     * @return Response
     * @throws SearchParamException
     */
    public function handleGetRequest(Request $request): Response
    {
        $offset = $request->getQuery()->get(self::VACANCY_OFFSET, FilterParams::DEFAULT_OFFSET);
        $limit = $request->getQuery()->get(self::VACANCY_LIMIT, FilterParams::DEFAULT_LIMIT);
        $vacancySearchFilterParams = new VacancySearchFilterParams();
        $vacancySearchFilterParams->setStatus(true);
        $vacancySearchFilterParams->setIsPublished(true);
        $vacancySearchFilterParams->setSortField(self::VACANCY_ID);
        $vacancySearchFilterParams->setSortOrder(ListSorter::DESCENDING);
        $vacancySearchFilterParams->setLimit($limit);
        $vacancySearchFilterParams->setOffset($offset);
        $vacancies = $this->getVacancyService()->getVacancyDao()->getVacancies($vacancySearchFilterParams);
        $count = $this->getVacancyService()->getVacancyDao()->getVacanciesCount($vacancySearchFilterParams);

        return new Response(
            $this->getNormalizerService()
                ->normalizeArray(VacancyModel::class, $vacancies),
            [CommonParams::PARAMETER_TOTAL => $count]
        );
    }

    /**
     * @param Request $request
     * @return Response
     * @throws NotImplementedException
     */
    public function handlePostRequest(Request $request): Response
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @return NotImplementedException
     */
    private function getNotImplementedException(): NotImplementedException
    {
        return new NotImplementedException();
    }

    /**
     * @param Request $request
     * @return Response
     * @throws NotImplementedException
     */
    public function handlePutRequest(Request $request): Response
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @param Request $request
     * @return Response
     * @throws NotImplementedException
     */
    public function handleDeleteRequest(Request $request): Response
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @param Request $request
     * @return ParamRuleCollection|null
     */
    protected function initGetValidationRule(Request $request): ?ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_LIMIT,
                    new Rule(Rules::ZERO_OR_POSITIVE), // Zero for not to limit results
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_OFFSET,
                    new Rule(Rules::ZERO_OR_POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_SORT_ORDER,
                    new Rule(Rules::IN, [[ListSorter::ASCENDING, ListSorter::DESCENDING]])
                ),
                true
            )
        );
    }

    /**
     * @return ValidationDecorator
     */
    protected function getValidationDecorator(): ValidationDecorator
    {
        if (!$this->validationDecorator instanceof ValidationDecorator) {
            $this->validationDecorator = new ValidationDecorator();
        }
        return $this->validationDecorator;
    }

    /**
     * @param Request $request
     * @return ParamRuleCollection|null
     * @throws NotImplementedException
     */
    public function initPostValidationRule(Request $request): ?ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @param Request $request
     * @return ParamRuleCollection|null
     * @throws NotImplementedException
     */
    public function initPutValidationRule(Request $request): ?ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @param Request $request
     * @return ParamRuleCollection|null
     * @throws NotImplementedException
     */
    public function initDeleteValidationRule(Request $request): ?ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
