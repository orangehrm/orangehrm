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

namespace Orangehrm\Rest\Api\User\Help;


use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Http\Response;
use \HelpService;

class HelpConfigurationAPI extends EndPoint
{
    const LABEL = 'label';
    const LABELS = 'labels';
    const CATEGORY_IDS = 'categoryIds';
    const QUERY='query';
    const MODE='mode';
    protected $helpService;
    /**
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function getHelpConfiguration()
    {
        $params=$this->getParameters();
        $labels = $params[self::LABELS];
        $categoryIds = $params[self::CATEGORY_IDS];
        $query = $params[self::QUERY];
        $mode = $params[self::MODE];
        if($mode=='category') {
            return new Response(
                array(
                    'defaultRedirectUrl' => $this->getHelpService()->getDefaultRedirectUrl(),
                    'redirectUrls' => $this->getHelpService()->getCategoriesFromSearchQuery($query),
                )
            );
        } else{
            return new Response(
                array(
                    'defaultRedirectUrl' => $this->getHelpService()->getDefaultRedirectUrl(),
                    'redirectUrls' => $this->getHelpService()->getRedirectUrlList($query, $labels, $categoryIds),
                )
            );
        }
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        $params = array();
        $params[self::LABELS] = $this->getRequestParams()->getUrlParam(self::LABELS);
        $params[self::CATEGORY_IDS] = $this->getRequestParams()->getUrlParam(self::CATEGORY_IDS);
        $params[self::QUERY] = $this->getRequestParams()->getUrlParam(self::QUERY);
        $params[self::MODE] = $this->getRequestParams()->getUrlParam(self::MODE);
        return $params;
    }

    /**
     * @return HelpService
     */
    public function getHelpService() {
        if (!$this->helpService instanceof HelpService) {
            $this->helpService = new HelpService();
        }
        return $this->helpService;
    }

    /**
     * @param HelpService $helpService
     */
    public function setHelpService(HelpService $helpService)
    {
        $this->helpService = $helpService;
    }

    /**
     * @return array
     */
    public function getValidationRules()
    {
        return array(
            self::LABEL => array('StringType' => true)
        );
    }
}
