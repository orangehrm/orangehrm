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

namespace OrangeHRM\I18N\Service;

use OrangeHRM\Core\Traits\CacheTrait;
use OrangeHRM\Core\Traits\ETagHelperTrait;
use OrangeHRM\I18N\Dao\I18NDao;
use Symfony\Component\Cache\CacheItem;

class I18NService
{
    use CacheTrait;
    use ETagHelperTrait;

    public const CORE_I18N_CACHE_KEY_PREFIX = 'core.i18n';
    public const CORE_I18N_TRANSLATION_CACHE_KEY_SUFFIX = 'translation';
    public const CORE_I18N_ETAG_CACHE_KEY_SUFFIX = 'etag';

    private ?I18NDao $i18nDao = null;

    /**
     * @return I18NDao
     */
    public function getI18NDao(): I18NDao
    {
        if (!$this->i18nDao instanceof I18NDao) {
            $this->i18nDao = new I18NDao();
        }
        return $this->i18nDao;
    }

    /**
     * @param string $langCode
     * @return string
     */
    protected function generateTranslationCacheKey(string $langCode): string
    {
        return self::CORE_I18N_CACHE_KEY_PREFIX . ".$langCode." . self::CORE_I18N_TRANSLATION_CACHE_KEY_SUFFIX;
    }

    /**
     * @param string $langCode
     * @return string
     */
    protected function generateETagCacheKey(string $langCode): string
    {
        return self::CORE_I18N_CACHE_KEY_PREFIX . ".$langCode." . self::CORE_I18N_ETAG_CACHE_KEY_SUFFIX;
    }

    /**
     * @param string $langCode
     * @return array<string, array> e.g. array('general.employee' => ['source' => 'Employee', 'target' => 'Employé']
     */
    protected function getTranslationMessages(string $langCode): array
    {
        $results = $this->getI18NDao()->getAllTranslationMessagesByLangCode($langCode);

        $resource = [];
        foreach ($results as $result) {
            $unitId = $result['unitId'];
            unset($result['unitId']);
            // TODO:: directly use unit id
            $unitId = is_numeric($unitId) ? str_replace(' ', '_', strtolower($result['source'])) : $unitId;

            $group = isset($result['groupName']) ? $result['groupName'] . '.' : '';
            unset($result['groupName']);
            $key = $group . $unitId;
            $resource[$key] = $result;
        }
        return $resource;
    }

    /**
     * @param string $langCode
     * @return string
     */
    protected function getTranslationMessagesAsJsonStringAlongWithCache(string $langCode): string
    {
        return $this->getCache()->get(
            $this->generateTranslationCacheKey($langCode),
            function () use ($langCode) {
                return json_encode($this->getTranslationMessages($langCode));
            }
        );
    }

    /**
     * @param string $langCode
     * @return string
     */
    protected function getETagAlongWithCache(string $langCode): string
    {
        $content = $this->getTranslationMessagesAsJsonStringAlongWithCache($langCode);
        return $this->getCache()->get(
            $this->generateETagCacheKey($langCode),
            function () use ($content) {
                return $this->generateEtag($content);
            }
        );
    }

    /**
     * @param string $langCode
     * @return string
     */
    public function getTranslationMessagesAsJsonString(string $langCode): string
    {
        $this->getETagByLangCode($langCode); // To avoid flow issues
        return $this->getTranslationMessagesAsJsonStringAlongWithCache($langCode);
    }

    /**
     * @param string $langCode
     * @return string
     */
    public function getETagByLangCode(string $langCode): string
    {
        $cacheKey = $this->generateETagCacheKey($langCode);
        /** @var CacheItem $cacheItem */
        $cacheItem = $this->getCache()->getItem($cacheKey);
        if (!$cacheItem->isHit()) {
            // If cache not found, need to create
            $this->getETagAlongWithCache($langCode);
            // Re-fetch cache item once update the cache
            $cacheItem = $this->getCache()->getItem($cacheKey);
        }
        return $cacheItem->get();
    }
}
