<?php

/*
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

class osIntegrationComponent extends sfComponent
{

    protected $configService;

    public function getConfigService()
    {

        if (!$this->configService instanceof ConfigService) {
            $this->configService = new ConfigService();
        }
        return $this->configService;
    }

    public function setConfigService($configService)
    {
        $this->configService = $configService;
    }

    public function execute($request)
    {

        $initialModule = $request->getParameter('initialModuleName', '');

        if (!empty($initialModule)) {
            $this->module = $initialModule;
        } else {
            $this->module = $this->getContext()->getModuleName();
        }

        $this->action = $this->getContext()->getActionName();

        $this->page = $this->checkIntegrations($this->module, $this->action);
        $this->inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
//        $this->param['id'] = ;
//        $this->param['secret'] = '1_21yl7nfa51q80k8c4c8ocwow0oowskoswo0wggkwog40gg0s0w';
//        $this->param['url'] = '1_21yl7nfa51q80k8c4c8ocwow0oowskoswo0wggkwog40gg0s0w';

    }

    /**
     * Check for integrations
     *
     * @param $module
     * @param $action
     */
    protected function checkIntegrations($module, $action)
    {
        $xml = $this->getIntegrationsXML();
        $processor = new IntegrationXMLProcessor();

        if ($xml != null) {
            foreach ($xml->integrations[0] as $integration) {

                $content = $integration->content[0]->asXML();
                foreach ($integration->screen as $screen){

                    if (rtrim($screen->module[0]) == $module && rtrim($screen->action[0]) == $action) {
                        return $processor->processXML($integration->content[0]);
                        break;
                    }
                }

            }
        } else {
            return null;
        }
        return null;

    }

    /**
     * Write content into success file
     *
     * @param $contentData
     */
    protected function writeContent($contentData)
    {
        try {

            $fname = "../plugins/orangehrmAdminPlugin/modules/integration/templates/_osIntegration.php";
            $fhandle = fopen($fname, "w");
            fwrite($fhandle, $contentData);
            fclose($fhandle);

        } catch (Exception $e) {
            $logger = Logger::getLogger("orangehrm.log");
            $logger->error($e);
        }

    }

    /**
     * clear content
     */
    function clearContent()
    {
        try {

            $fname = "../plugins/orangehrmAdminPlugin/modules/integration/templates/_osIntegration.php";
            $content = $this->getDefaultContent();
            $fhandle = fopen($fname, "w");
            fwrite($fhandle, $content);
            fclose($fhandle);

        } catch (Exception $e) {
            $logger = Logger::getLogger("orangehrm.log");
            $logger->error($e);
        }

    }

    private function getDefaultContent()
    {

        return "<div class=\"notification-bar\" id = \"notificationBar\" >
    
    </div>";
    }

    /**
     * Get config value
     *
     * @return SimpleXMLElement
     */
    public function getIntegrationsXML()
    {
        $configVal = $this->getConfigService()->getIntegrationsConfigValue();
        if ($configVal != null) {

            try {
                $xml = new SimpleXMLElement($configVal);
                return $xml;
            } catch (Exception $e) {
                $logger = Logger::getLogger("orangehrm.log");
                $logger->error($e);
            }

        } else {
            return null;
        }

    }

}
