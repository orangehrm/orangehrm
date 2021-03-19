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

class ZendeskHelpProcessor implements HelpProcessor
{

    const DEFAULT_CONTENT_TYPE = "application/json";
    const ZENDESK_SEARCH_URL = '/api/v2/help_center/articles/search.json?';
    const ZENDESK_CATEGORY_URL = '/api/v2/help_center/categories';
    const ZENDESK_DEFAULT_URL_PATH = '/hc/en-us';
    protected $helpConfigService;

    /**
     * @return mixed
     */
    public function getHelpConfigService()
    {
        if (!$this->helpConfigService instanceof HelpConfigService) {
            $this->helpConfigService = new HelpConfigService();
        }
        return $this->helpConfigService;
    }

    /**
     * @param mixed $helpConfigService
     */
    public function setHelpConfigService($helpConfigService)
    {
        $this->helpConfigService = $helpConfigService;
    }

    /**
     * @return String
     */
    public function getBaseUrl()
    {
        return $this->getHelpConfigService()->getBaseHelpUrl();
    }

    /**
     * @param null $query
     * @param array $labels
     * @param array $categoryIds
     * @return false|string
     */
    public function getSearchUrlFromQuery($query = null, $labels = [], $categoryIds = [])
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
     * @param $label
     * @return string
     */
    public function getSearchUrl($label)
    {
        return $this->getBaseUrl() . self::ZENDESK_SEARCH_URL . 'label_names=' . $label;
    }

    /**
     * @param $label
     * @return mixed|string
     */
    public function getRedirectUrl($label)
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
     * @param $url
     * @param string $contentType
     * @return array|null
     */
    protected function sendQuery($url, $contentType = self::DEFAULT_CONTENT_TYPE)
    {
        $headerOptions = array();

        $headerOptions[GuzzleHttp\RequestOptions::TIMEOUT] = 30;
        $headerOptions[GuzzleHttp\RequestOptions::HEADERS] = [
            'Content-Type' => $contentType
        ];
        $client = new GuzzleHttp\Client();
        try {
            $response = $client->get($url, $headerOptions);
        } catch (Exception $e) {
            return null;
        }
        $body = $response->getBody();
        $responseCode = $response->getStatusCode();
        return array(
            'responseCode' => $responseCode,
            'response' => $body,
        );
    }

    /**
     * @return string
     */
    public function getDefaultRedirectUrl()
    {
        return $this->getBaseUrl() . self::ZENDESK_DEFAULT_URL_PATH;
    }

    /**
     * @param null $query
     * @param array $labels
     * @param array $categoryIds
     * @return array
     */
    public function getRedirectUrlList($query = null, $labels = [], $categoryIds = [])
    {
        if ($query == null && $labels == [] && $categoryIds == []) {
            return [];
        }
        $searchUrl = $this->getSearchUrlFromQuery($query, $labels, $categoryIds);
        $results = $this->sendQuery($searchUrl);
        if ($results['response']) {
            $response = json_decode($results['response'], true);
        }
        $redirectUrls = array();
        $count = $response['count'];
        if (($count >= 1) && ($results['responseCode'] == 200)) {
            foreach ($response['results'] as $result) {
                $redirectUrl = $result['html_url'];
                $name = $result['name'];
                array_push($redirectUrls, array('name' => $name, 'url' => $redirectUrl));
            }
            return $redirectUrls;
        } else {
            return [];
        }
    }

    /**
     * @param $category
     * @return mixed|string
     */
    public function getCategoryRedirectUrl($category)
    {
        $url = $this->getBaseUrl() . self::ZENDESK_CATEGORY_URL . '/' . $category;
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
     * @param null $query
     * @return array
     */
    public function getCategoriesFromSearchQuery($query = null)
    {
        $url = $this->getBaseUrl() . self::ZENDESK_CATEGORY_URL;
        $results = $this->sendQuery($url);
        if ($results['response']) {
            $response = json_decode($results['response'], true);
        }
        $categories = array();
        if (($results['responseCode'] == 200)) {
            foreach ($response['categories'] as $category) {
                $redirectUrl = $category['html_url'];
                $name = $category['name'];
                if ($query != null) {
                    if (strpos($name, $query) !== false) {
                        array_push($categories, array('name' => $name, 'url' => $redirectUrl));
                    }
                } else {
                    array_push($categories, array('name' => $name, 'url' => $redirectUrl));
                }
            }
        }
        return $categories;
    }
}
