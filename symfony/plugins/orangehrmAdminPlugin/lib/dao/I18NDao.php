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

class I18NDao extends BaseDao
{
    /**
     * @param string $langCode
     * @param bool $onlyCustomized
     * @return Doctrine_Collection|I18NTranslate[]
     * @throws DaoException
     */
    public function getMessages(string $langCode, bool $onlyCustomized = true)
    {
        try {
            $q = Doctrine_Query::create()
                ->from('I18NTranslate t')
                ->leftJoin('t.I18NLangString ls')
                ->leftJoin('t.I18NLanguage l')
                ->andWhere('l.code = ?', $langCode);
            if ($onlyCustomized) {
                $q->andWhere('t.customized = ?', true);
            }
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $langCode
     * @return Doctrine_Record|I18NLanguage
     * @throws DaoException
     */
    public function getLanguageByCode(string $langCode)
    {
        try {
            return Doctrine::getTable('I18NLanguage')->findOneBy('code', $langCode);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param ParameterObject $searchParams
     * @return Doctrine_Collection|I18NLanguage[]
     * @throws DaoException
     */
    public function searchLanguages(ParameterObject $searchParams)
    {
        try {
            $q = Doctrine_Query::create()->from('I18NLanguage l');

            if (!empty($searchParams->getParameter('langCode'))) {
                $q->andWhere('l.code = ?', $searchParams->getParameter('langCode'));
            }
            if (is_bool($searchParams->getParameter('enabled'))) {
                $q->andWhere('l.enabled = ?', $searchParams->getParameter('enabled'));
            }
            if (is_bool($searchParams->getParameter('added'))) {
                $q->andWhere('l.added = ?', $searchParams->getParameter('added'));
            }

            if (!empty($searchParams->getParameter('sortField'))) {
                if (in_array($searchParams->getParameter('sortOrder'), ['ASC', 'DESC'])) {
                    $q->addOrderBy(
                        $searchParams->getParameter('sortField') . ' ' . $searchParams->getParameter('sortOrder')
                    );
                } else {
                    $q->addOrderBy($searchParams->getParameter('sortField'));
                }
            }
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $id
     * @return null|I18NLanguage
     * @throws DaoException
     */
    public function getLanguageById(string $id)
    {
        try {
            $lang = Doctrine::getTable('I18NLanguage')->find($id);
            return $lang === false ? null : $lang;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param array $currentSourceIds
     * @param int $sourceId
     * @return int
     * @throws DaoException
     */
    public function deleteLangStrings(array $currentSourceIds, int $sourceId)
    {
        try {
            $q = Doctrine_Query::create()
                ->delete('I18NLangString ls')
                ->andWhereNotIn('ls.unitId', $currentSourceIds)
                ->andWhere('ls.sourceId = ?', $sourceId);
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param int $sourceId
     * @return Doctrine_Collection|I18NLangString[]
     * @throws DaoException
     */
    public function getLangStringsBySourceId(int $sourceId)
    {
        try {
            $q = Doctrine_Query::create()
                ->from('I18NLangString ls')
                ->andWhere('ls.sourceId = ?', $sourceId);
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $source
     * @return Doctrine_Record|I18NSource
     * @throws DaoException
     */
    public function getI18NSource(string $source)
    {
        try {
            return Doctrine::getTable('I18NSource')->findOneBy('source', $source);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param I18NSource $i18NSource
     * @return I18NSource
     * @throws Exception
     */
    public function saveI18NSource(I18NSource $i18NSource)
    {
        $i18NSource->save();
        return $i18NSource;
    }

    /**
     * @param I18NSource $i18NSource
     * @return bool
     */
    public function deleteI18NSource(I18NSource $i18NSource)
    {
        return $i18NSource->delete();
    }

    /**
     * @return Doctrine_Collection|I18NSource[]
     * @throws DaoException
     */
    public function getAllI18NSources()
    {
        try {
            return Doctrine::getTable('I18NSource')->findAll();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param I18NLangString $i18NLangString
     * @return I18NLangString
     * @throws Exception
     */
    public function saveI18NLangString(I18NLangString $i18NLangString)
    {
        $i18NLangString->save();
        return $i18NLangString;
    }

    /**
     * @param int $langStringId
     * @param int $langId
     * @return Doctrine_Record|I18NTranslate
     * @throws DaoException
     */
    public function getI18NTranslate(int $langStringId, int $langId)
    {
        try {
            $q = Doctrine_Query::create()
                ->from('I18NTranslate t')
                ->andWhere('t.langStringId = ?', $langStringId)
                ->andWhere('t.languageId = ?', $langId);
            return $q->fetchOne();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $id
     * @return Doctrine_Record|null|I18NTranslate
     * @throws DaoException
     */
    public function getI18NTranslateById(string $id)
    {
        try {
            $translate = Doctrine::getTable('I18NTranslate')->find($id);
            return $translate === false ? null : $translate;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param I18NTranslate $i18NTranslate
     * @return I18NTranslate
     * @throws Exception
     */
    public function saveI18NTranslate(I18NTranslate $i18NTranslate)
    {
        $i18NTranslate->save();
        return $i18NTranslate;
    }

    /**
     * @param I18NLanguage $i18NLanguage
     * @return I18NLanguage
     * @throws Exception
     */
    public function saveI18NLanguage(I18NLanguage $i18NLanguage)
    {
        $i18NLanguage->save();
        return $i18NLanguage;
    }

    /**
     * @param ParameterObject $searchParams
     * @param bool $getCount
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function searchTranslations(ParameterObject $searchParams, bool $getCount = false)
    {
        try {
            $q = Doctrine_Query::create()
                ->from('I18NTranslate t')
                ->leftJoin('t.I18NLangString ls')
                ->leftJoin('ls.I18NGroup g')
                ->leftJoin('t.I18NLanguage l');

            if (!empty($searchParams->getParameter('langCode'))) {
                $q->andWhere('l.code = ?', $searchParams->getParameter('langCode'));
            }
            if (is_bool($searchParams->getParameter('translated'))) {
                $q->andWhere('t.translated = ?', $searchParams->getParameter('translated'));
            }
            if (!empty($searchParams->getParameter('sourceText'))) {
                $q->andWhere('ls.value LIKE ?', ['%' . $searchParams->getParameter('sourceText') . '%']);
            }
            if (!empty($searchParams->getParameter('translatedText'))) {
                $q->andWhere('t.value LIKE ?', ['%' . $searchParams->getParameter('translatedText') . '%']);
            }
            if (!empty($searchParams->getParameter('group'))) {
                $q->andWhere('g.name = ?', $searchParams->getParameter('group'));
            }

            if ($getCount) {
                return $q->count();
            }

            if (!empty($searchParams->getParameter('sortField'))) {
                if (in_array($searchParams->getParameter('sortOrder'), ['ASC', 'DESC'])) {
                    $q->addOrderBy(
                        $searchParams->getParameter('sortField') . ' ' . $searchParams->getParameter('sortOrder')
                    );
                } else {
                    $q->addOrderBy($searchParams->getParameter('sortField'));
                }
            }
            if (!empty($searchParams->getParameter('offset'))) {
                $q->offset($searchParams->getParameter('offset'));
            }
            if (!empty($searchParams->getParameter('limit'))) {
                $q->limit($searchParams->getParameter('limit'));
            }
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return Doctrine_Collection|I18NGroup[]
     * @throws DaoException
     */
    public function getI18NGroups()
    {
        try {
            return Doctrine::getTable('I18NGroup')->findAll();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
}
