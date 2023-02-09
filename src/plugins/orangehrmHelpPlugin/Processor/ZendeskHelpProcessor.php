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
 * Boston, MA 02110-1301, USA
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
    public const ZENDESK_CATEGORY_URL = '/api/v2/help_center/categories';
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
     * @param string|null $query
     * @param array $labels
     * @param array $categoryIds
     * @return string
     */
    public function getSearchUrlFromQuery(string $query = null, array $labels = [], array $categoryIds = []): string
    {
        $mainUrl = $this->getBaseUrl() . self::ZENDESK_SEARCH_URL;
        if ($query != null) {
            $mainUrl .= 'query=' . $query;
        }
        if (count($labels) > 0) {
            if (substr($mainUrl, -1) != '?') {
                $mainUrl .= '&';
            }
            $mainUrl .= 'label_names=';
            foreach ($labels as $label) {
                $mainUrl .= $label . ',';
            }
            $mainUrl = substr($mainUrl, 0, -1);
        }
        if (count($categoryIds) > 0) {
            if (substr($mainUrl, -1) != '?') {
                $mainUrl .= '&';
            }
            $mainUrl .= 'category=';
            foreach ($categoryIds as $categoryId) {
                $mainUrl .= $categoryId . ',';
            }
            $mainUrl = substr($mainUrl, 0, -1);
        }
        return $mainUrl;
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

        $results = $this->sendQuery($searchUrl);
        if ($results['response']) {
            $response = json_decode($results['response'], true);
        }
        $count = $response['count'];
        if (($count >= 1) && ($results['responseCode'] == 200)) {
            $redirectUrl = $response['results'][0]['html_url'];
        } else {
            $redirectUrl = $this->getDefaultRedirectUrl();
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

    /**
     * @param string|null $query
     * @param array $labels
     * @param array $categoryIds
     * @return array
     */
    public function getRedirectUrlList(string $query = null, array $labels = [], array $categoryIds = []): array
    {
        if ($query == null && $labels == [] && $categoryIds == []) {
            return [];
        }
        $searchUrl = $this->getSearchUrlFromQuery($query, $labels, $categoryIds);
        $results = $this->sendQuery($searchUrl);
        if ($results['response']) {
            $response = json_decode($results['response'], true);
        }
        $redirectUrls = [];
        $count = $response['count'];
        if (($count >= 1) && ($results['responseCode'] == 200)) {
            foreach ($response['results'] as $result) {
                $redirectUrl = $result['html_url'];
                $name = $result['name'];
                $redirectUrls[] = ['name' => $name, 'url' => $redirectUrl];
            }
            return $redirectUrls;
        } else {
            return [];
        }
    }

    /**
     * @param string $categoryId
     * @return string
     */
    public function getCategoryRedirectUrl(string $categoryId): string
    {
        $url = $this->getBaseUrl() . self::ZENDESK_CATEGORY_URL . '/' . $categoryId;
        $results = $this->sendQuery($url);
        if ($results['response']) {
            $response = json_decode($results['response'], true);
        }
        if (($results['responseCode'] == 200)) {
            $redirectUrl = $response['category']['html_url'];
        } else {
            $redirectUrl = $this->getDefaultRedirectUrl();
        }
        return $redirectUrl;
    }

    /**
     * @param string|null $query
     * @return array
     */
    public function getCategoriesFromSearchQuery(string $query = null): array
    {
        $url = $this->getBaseUrl() . self::ZENDESK_CATEGORY_URL;
        $results = $this->sendQuery($url);
        if ($results['response']) {
            $response = json_decode($results['response'], true);
        }
        $categories = [];
        if (($results['responseCode'] == 200)) {
            foreach ($response['categories'] as $category) {
                $redirectUrl = $category['html_url'];
                $name = $category['name'];
                if ($query != null) {
                    if (strpos($name, $query) !== false) {
                        $categories[] = ['name' => $name, 'url' => $redirectUrl];
                    }
                } else {
                    $categories[] = ['name' => $name, 'url' => $redirectUrl];
                }
            }
        }
        return $categories;
    }
}
