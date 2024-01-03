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

namespace OrangeHRM\Help\Processor;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use OrangeHRM\Help\Service\HelpConfigService;

class ZendeskHelpProcessor implements HelpProcessor
{
    public const DEFAULT_CONTENT_TYPE = "application/json";
    public const ZENDESK_SEARCH_URL = '/api/v2/help_center/articles/search.json?';
    public const ZENDESK_DEFAULT_URL_PATH = '/hc/en-us';

    protected ?HelpConfigService $helpConfigService = null;
    private ?Client $httpClient = null;

    /**
     * @return HelpConfigService
     */
    public function getHelpConfigService(): HelpConfigService
    {
        return $this->helpConfigService ??= new HelpConfigService();
    }

    public function getHttpClient(): Client
    {
        return $this->httpClient ??= new Client();
    }

    /**
     * @return String
     */
    public function getBaseUrl(): string
    {
        return $this->getHelpConfigService()->getBaseHelpUrl();
    }

    /**
     * @param string $label
     * @return string
     */
    public function getSearchUrl(string $label): string
    {
        return $this->getBaseUrl() . self::ZENDESK_SEARCH_URL . 'label_names=' . $label;
    }

    /**
     * @param string $label
     * @return string
     */
    public function getRedirectUrl(string $label): string
    {
        $searchUrl = $this->getSearchUrl($label);
        $redirectUrl = $this->getDefaultRedirectUrl();

        $results = $this->sendQuery($searchUrl);
        if (isset($results['response'])) {
            $response = json_decode($results['response'], true);
            $count = $response['count'];
            if (($count >= 1) && ($results['responseCode'] == 200)) {
                $redirectUrl = $response['results'][0]['html_url'];
            }
        }

        return $redirectUrl;
    }

    /**
     * @param string $url
     * @param string $contentType
     * @return array|null
     */
    protected function sendQuery(string $url, string $contentType = self::DEFAULT_CONTENT_TYPE): ?array
    {
        $headerOptions = [];

        $headerOptions[RequestOptions::TIMEOUT] = 30;
        $headerOptions[RequestOptions::HEADERS] = [
            'Content-Type' => $contentType
        ];

        try {
            $response = $this->getHttpClient()->get($url, $headerOptions);
            $body = $response->getBody();
            $responseCode = $response->getStatusCode();
            return ['responseCode' => $responseCode, 'response' => $body];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getDefaultRedirectUrl(): string
    {
        return $this->getBaseUrl() . self::ZENDESK_DEFAULT_URL_PATH;
    }
}
