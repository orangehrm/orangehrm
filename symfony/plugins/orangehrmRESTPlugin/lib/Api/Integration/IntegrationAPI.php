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

namespace Orangehrm\Rest\Api\Integration;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Leave\Entity\LeaveEntitlement;


use Orangehrm\Rest\Http\Response;

class IntegrationAPI extends EndPoint
{

    const PARAMETER_INTEGRATION_CONFIG = '';
    const PARAMETER_CONTENT_XML = '';

    protected $configService;

    /**
     * Adding a new integration
     *
     * @return Response
     */
    public function addIntegration()
    {
        $newComponentXML = $this->getIntegrationContent();
        $integrationContent = $this->getIntegrationsXML();

        $xml = simplexml_load_string($integrationContent);
        list($subReport) = $xml->xpath('//integrations');

        $displayGroups = $subReport;

        $domDisplayGroups = dom_import_simplexml($displayGroups);
        $this->addElement($domDisplayGroups, $newComponentXML);

        $this->configService->setIntegrationsConfigValue($xml->asXML());

        return new Response(array("Success" => 'Successfully Added'), array());

    }

    /**
     * Get os integrations from
     * config
     *
     * @return null|xml content
     */
    public function getIntegrationContent()
    {
        $content = $filters[self::PARAMETER_CONTENT_XML] = $this->getRequestParams()->getContent();

        if ($content != null) {

            try {
                $xml = $content;
                return $xml;
            } catch (\Exception $e) {
                $logger = \Logger::getLogger("orangehrm.log");
                $logger->error($e);
            }

        } else {
            return null;
        }
        return null;
    }

    /**
     * Get Integrations XML
     *
     * @return null|string
     */
    public function getIntegrationsXML()
    {
        $configVal = $this->getConfigService()->getIntegrationsConfigValue();
        if ($configVal != null) {

            try {
                $xml = $configVal;
                return $xml;
            } catch (\Exception $e) {
                $logger = \Logger::getLogger("orangehrm.log");
                $logger->error($e);
            }

        } else {
            return null;
        }

        return null;
    }

    public function getConfigService()
    {

        if (!$this->configService instanceof \ConfigService) {
            $this->configService = new \ConfigService();
        }
        return $this->configService;
    }

    public function setConfigService($configService)
    {
        $this->configService = $configService;
    }

    /**
     * Add element method
     *
     * @param \DOMElement $groups
     * @param $displayGroupXml
     */
    protected function addElement(\DOMElement $groups, $displayGroupXml)
    {

        $domGroup = dom_import_simplexml(simplexml_load_string($displayGroupXml));
        $domGroup = $groups->ownerDocument->importNode($domGroup, true);
        $groups->appendChild($domGroup);


    }

    /**
     * XML append
     *
     * @param \SimpleXMLElement $to
     * @param \SimpleXMLElement $from
     */
    function sxml_append(\SimpleXMLElement $to, \SimpleXMLElement $from)
    {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));


    }


}


