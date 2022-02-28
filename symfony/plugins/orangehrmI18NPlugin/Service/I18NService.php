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

use InvalidArgumentException;
use OrangeHRM\Core\Traits\CacheTrait;
use OrangeHRM\Core\Traits\ETagHelperTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\I18N\Dao\I18NDao;
use OrangeHRM\I18N\Dto\TranslationCollection;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;

class I18NService
{
    use CacheTrait;
    use ETagHelperTrait;
    use ConfigServiceTrait;

    public const CORE_I18N_CACHE_KEY_PREFIX = 'core.i18n';
    public const TRANSLATION_CACHE_KEY_SUFFIX = 'translation';
    public const TRANSLATION_KEY_TARGET_ARRAY_CACHE_KEY_SUFFIX = 'translation_key_target_array';
    public const TRANSLATION_SOURCE_TARGET_ARRAY_CACHE_KEY_SUFFIX = 'translation_source_target_array';
    public const ETAG_CACHE_KEY_SUFFIX = 'etag';
    public const TRANSLATOR_DEFAULT_FORMAT = 'array';
    public const TRANSLATOR_DEFAULT_DOMAIN = 'messages+intl-icu';
    public const TRANSLATION_TYPE_KEY_TARGET = 'key_target';
    public const TRANSLATION_TYPE_SOURCE_TARGET = 'source_target';

    private ?I18NDao $i18nDao = null;
    private ?Translator $translator = null;
    private array $loadedLanguages = [];

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
     * @param string $type
     */
    private function addLanguage(string $langCode, string $type): void
    {
        $key = "${langCode}_$type";
        if (isset($this->loadedLanguages[$key])) {
            throw new InvalidArgumentException("Already added resource under: $langCode");
        }
        $this->loadedLanguages[$key] = [
            'locale' => $langCode,
            'type' => $type,
        ];
    }

    /**
     * @param string $langCode
     * @param string $type
     * @return bool
     */
    private function isLanguageLoaded(string $langCode, string $type): bool
    {
        return isset($this->loadedLanguages["${langCode}_$type"]);
    }

    /**
     * @param string $langCode
     * @return string
     */
    protected function generateCacheKeyPrefixForLang(string $langCode): string
    {
        return self::CORE_I18N_CACHE_KEY_PREFIX . ".$langCode";
    }

    /**
     * @param string $langCode
     * @return string
     */
    protected function generateTranslationCacheKey(string $langCode): string
    {
        return $this->generateCacheKeyPrefixForLang($langCode) . '.' . self::TRANSLATION_CACHE_KEY_SUFFIX;
    }

    /**
     * @param string $langCode
     * @return string
     */
    protected function generateTranslationKeyTargetArrayCacheKey(string $langCode): string
    {
        return $this->generateCacheKeyPrefixForLang($langCode) . '.' .
            self::TRANSLATION_KEY_TARGET_ARRAY_CACHE_KEY_SUFFIX;
    }

    /**
     * @param string $langCode
     * @return string
     */
    protected function generateTranslationSourceTargetArrayCacheKey(string $langCode): string
    {
        return $this->generateCacheKeyPrefixForLang($langCode) . '.' .
            self::TRANSLATION_SOURCE_TARGET_ARRAY_CACHE_KEY_SUFFIX;
    }

    /**
     * @param string $langCode
     * @return string
     */
    protected function generateETagCacheKey(string $langCode): string
    {
        return $this->generateCacheKeyPrefixForLang($langCode) . '.' . self::ETAG_CACHE_KEY_SUFFIX;
    }

    /**
     * @param string $langCode
     * @return TranslationCollection
     */
    protected function getTranslationCollection(string $langCode): TranslationCollection
    {
        $results = $this->getI18NDao()->getAllTranslationMessagesByLangCode($langCode);

        $keyAndSourceTarget = [];
        $keyAndTarget = [];
        $sourceAndTarget = [];
        foreach ($results as $result) {
            $unitId = $result['unitId'];
            unset($result['unitId']);
            // TODO:: directly use unit id
            $unitId = is_numeric($unitId) ? str_replace(' ', '_', strtolower($result['source'])) : $unitId;

            $group = isset($result['groupName']) ? $result['groupName'] . '.' : '';
            unset($result['groupName']);
            $key = $group . $unitId;
            $keyAndSourceTarget[$key] = $result;
            $keyAndTarget[$key] = $result['target'] ?? $result['source'];
            $sourceAndTarget[$result['source']] = $result['target'] ?? $result['source'];
        }
        return new TranslationCollection($keyAndSourceTarget, $keyAndTarget, $sourceAndTarget);
    }

    /**
     * @param string $langCode
     * @return string
     */
    protected function getTranslationMessagesAsJsonStringAlongWithCache(string $langCode): string
    {
        $translations = null;
        $this->getCache()->get(
            $this->generateTranslationKeyTargetArrayCacheKey($langCode),
            function () use ($langCode, &$translations) {
                $translations instanceof TranslationCollection ?:
                    $translations = $this->getTranslationCollection($langCode);
                return $translations->getKeyAndTarget();
            }
        );
        $this->getCache()->get(
            $this->generateTranslationSourceTargetArrayCacheKey($langCode),
            function () use ($langCode, &$translations) {
                $translations instanceof TranslationCollection ?:
                    $translations = $this->getTranslationCollection($langCode);
                return $translations->getSourceAndTarget();
            }
        );
        return $this->getCache()->get(
            $this->generateTranslationCacheKey($langCode),
            function () use ($langCode, &$translations) {
                $translations instanceof TranslationCollection ?:
                    $translations = $this->getTranslationCollection($langCode);
                return json_encode($translations->getKeyAndSourceTarget());
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
     * @return array
     */
    public function getTranslationMessagesKeyTargetArray(string $langCode): array
    {
        $this->getETagByLangCode($langCode); // To avoid flow issues
        $cacheItem = $this->getCache()->getItem($this->generateTranslationKeyTargetArrayCacheKey($langCode));
        return $this->cleanAndRefetchIfCacheNotHit($langCode, $cacheItem);
    }

    /**
     * @param string $langCode
     * @return array
     */
    public function getTranslationMessagesSourceTargetArray(string $langCode): array
    {
        $this->getETagByLangCode($langCode); // To avoid flow issues
        $cacheItem = $this->getCache()->getItem($this->generateTranslationSourceTargetArrayCacheKey($langCode));
        return $this->cleanAndRefetchIfCacheNotHit($langCode, $cacheItem);
    }

    /**
     * @param string $langCode
     * @param CacheItem $cacheItem
     * @return array
     */
    private function cleanAndRefetchIfCacheNotHit(string $langCode, CacheItem $cacheItem): array
    {
        if (!$cacheItem->isHit()) {
            $this->getCache()->clear($this->generateCacheKeyPrefixForLang($langCode));
            $this->getETagByLangCode($langCode);
            $cacheItem = $this->getCache()->getItem($cacheItem->getKey());
        }
        return $cacheItem->get();
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

    /**
     * @return Translator
     * @internal
     */
    public function getTranslator(): Translator
    {
        if (!$this->translator instanceof Translator) {
            $langCode = $this->getConfigService()->getAdminLocalizationDefaultLanguage();
            $this->translator = new Translator($langCode);
            $this->translator->addLoader(self::TRANSLATOR_DEFAULT_FORMAT, new ArrayLoader());
        }
        return $this->translator;
    }

    /**
     * @param string $langCode
     * @internal
     */
    public function setTranslatorLanguage(string $langCode): void
    {
        $this->getTranslator()->setLocale($langCode);
    }

    /**
     * @param string $key
     * @param array $parameters
     * @param string|null $langCode
     * @return string
     * @internal
     */
    public function trans(string $key, array $parameters = [], string $langCode = null): string
    {
        $langCode !== null ?: $langCode = $this->getTranslator()->getLocale();
        if (!$this->isLanguageLoaded($langCode, self::TRANSLATION_TYPE_KEY_TARGET)) {
            $this->getTranslator()->addResource(
                self::TRANSLATOR_DEFAULT_FORMAT,
                $this->getTranslationMessagesKeyTargetArray($langCode),
                $langCode,
                self::TRANSLATOR_DEFAULT_DOMAIN
            );
            $this->addLanguage($langCode, self::TRANSLATION_TYPE_KEY_TARGET);
        }
        return $this->getTranslator()->trans($key, $parameters, null, $langCode);
    }

    /**
     * @param string $sourceLangString
     * @param array $parameters
     * @param string|null $langCode
     * @return string
     * @internal
     */
    public function transBySource(string $sourceLangString, array $parameters = [], string $langCode = null): string
    {
        $langCode !== null ?: $langCode = $this->getTranslator()->getLocale();
        if (!$this->isLanguageLoaded($langCode, self::TRANSLATION_TYPE_SOURCE_TARGET)) {
            $this->getTranslator()->addResource(
                self::TRANSLATOR_DEFAULT_FORMAT,
                $this->getTranslationMessagesSourceTargetArray($langCode),
                $langCode,
                self::TRANSLATOR_DEFAULT_DOMAIN
            );
            $this->addLanguage($langCode, self::TRANSLATION_TYPE_SOURCE_TARGET);
        }
        return $this->getTranslator()->trans($sourceLangString, $parameters, null, $langCode);
    }
}
